<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class SecurityControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    
    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $this->databaseTool = $this->testClient->getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testDisplayLogin()
    {
        $crawler = $this->testClient->request('GET', '/login');
 
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $this->testClient->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('title', 'Log in!');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'unauthorized',
            'password' => 'fake'
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects('/login');
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfulLogin()
    {
        $this->databaseTool->loadAliceFixture([__DIR__.'/users.yaml']);
        $csrfToken = $this->testClient->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $this->testClient->request('POST', '/login', [
            'email' => 'john@doe.com',
            'password' => '000000',
            '_csrf_token' => $csrfToken
        ]);

        $this->assertResponseRedirects('/homepage');
        $this->testClient->followRedirect();
        $this->assertSelectorTextContains('title', 'Homepage');
    }
}