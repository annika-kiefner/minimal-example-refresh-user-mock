<?php

namespace App\Tests;

use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RefreshUserWebTest extends WebTestCase
{
    private $client;
    private $user;

    public function setUp(): void
    {
        // boots the kernel and creates client acting as the browser
        $this->client = static::createClient();
        $this->user = new User('mail','password');
    }

    public function testDoesRefreshWorkOnNewRequests(): void
    {
        $this->client->loginUser($this->user);
        $this->assertIsUserAuthenticated(true);

        $this->client->request('GET', '/home');
        $this->client->request('GET', '/home');
        $this->client->request('GET', '/home');

        $this->assertIsUserAuthenticated(true);
    }

    private function assertIsUserAuthenticated(bool $expectIsAuthenticated)
    {
        $tokenStorage = $this->client->getContainer()
            ->get('security.token_storage');
        $this->assertEquals(
            $expectIsAuthenticated,
            ($tokenStorage->getToken() !== NULL)
        );
    }
}
