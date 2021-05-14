<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class RegistrationControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    
    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $this->databaseTool = $this->testClient->getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testDisplayRegister()
    {
        $crawler = $this->testClient->request('GET', '/register');
 
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $this->testClient->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('title', 'Register');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testRegisterWithBadCredentials()
    {
        $csrfToken = $this->testClient->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        
        $crawler = $this->testClient->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'wrong@email',
            'registration_form[plainPassword]' => 'fake',
            'registration_form[agreeTerms]' => '1',
            'registration_form[_token]' => $csrfToken
        ]);
        $this->testClient->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $this->testClient->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('li', 'The email \'"wrong@email"\' is not a valid email.');
    }

    public function testSuccessfulRegistration()
    {
        $crawler = $this->testClient->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'wrong@email.com',
            'registration_form[plainPassword]' => 'fakeemail',
            'registration_form[agreeTerms]' => '1',
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects('/homepage');
        $this->testClient->followRedirect();
        $this->assertSelectorTextContains('title', 'Homepage');
    }
}