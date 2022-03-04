<?php

namespace App\Tests;

use App\Security\ExternalApiClient;
use App\Security\User;
use PHPUnit\Framework\MockObject\MockObject;
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

    public function testDoesThrowExceptionOnUserRefresh(): void
    {
        $this->client->loginUser($this->user);
        $this->assertIsUserAuthenticated(true);

        $this->client->request('GET', '/home');
        $this->client->request('GET', '/home');         // should throw, but does not
        $this->fail('Refreshing the user on this second request should really have thrown an exception!');
    }

    public function testWhyDoesItThrowAnExceptionNow(): void
    {
        $this->client->loginUser($this->user);
        $this->assertIsUserAuthenticated(true);

        $this->client->request('GET', '/home');
        $this->client->request('GET', '/home');         // should throw, but does not
        $this->assertIsUserAuthenticated(true);   // throws
    }

    public function testDoesMockingPreventExceptions(): void
    {
        $this->mockExternalApiClient();
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/home');
        $this->assertExternalApiClientIsCurrentlyMocked();          // correctly mocked

        $this->client->request('GET', '/home');
        $this->assertExternalApiClientIsCurrentlyMocked();          // no longer mocked here!
    }


    /////////////  helper functions  ///////////////

    private function assertIsUserAuthenticated(bool $expectIsAuthenticated)
    {
        $tokenStorage = $this->client->getContainer()
            ->get('security.token_storage');
        $this->assertEquals(
            $expectIsAuthenticated,
            ($tokenStorage->getToken() !== NULL)
        );
    }

    private function mockExternalApiClient()
    {
        $mockApiClient = $this->createMock(ExternalApiClient::class);
        $mockApiClient
            ->method('requestForUserRefresh')
            ->willReturn($this->user);

        $container = $this->client->getContainer();
        $container->set(ExternalApiClient::class, $mockApiClient);
    }

    private function assertExternalApiClientIsCurrentlyMocked()
    {
        $externalApiClient = $this->client->getContainer()
            ->get(ExternalApiClient::class);
        $this->assertInstanceOf(
            MockObject::class,
            $externalApiClient,
            'The ExternalApiClient in the container should really be mocked, but it is not!'
        );
    }
}
