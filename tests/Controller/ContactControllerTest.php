<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;

class ContactControllerTest extends WebTestCase
{
    public function testRedirectToLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');
 
        $this->assertResponseRedirects('/login');
    }

    public function testLoginPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testRegisterPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
