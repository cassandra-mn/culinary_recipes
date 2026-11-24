<?php

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use Tests\Factories\UserFactory;

class AuthTest extends IntegrationTestCase
{
    #[Test]
    public function register_returns_201_for_valid_parameters(): void
    {
        $user = UserFactory::createCredentials();
        $result = $this->agent->post('/register')->send($user);

        $this->assertEquals(201, $result->status());
        $this->assertIsInt($result->body()['data']);
    }

    #[Test]
    public function register_returns_422_for_invalid_parameters(): void
    {
        $body = [];
        $result = $this->agent->post('/register')->send($body);

        $this->assertEquals(422, $result->status());
    }

    #[Test]
    public function register_returns_422_when_login_is_too_long(): void
    {
        $user = UserFactory::createCredentials();
        $result = $this->agent->post('/register')->send([...$user, 'login' => str_repeat('a', 101)]);

        $this->assertEquals(422, $result->status());
    }

    #[Test]
    public function register_returns_409_for_duplicate_parameters(): void
    {
        $user = UserFactory::createUser();
        $result = $this->agent->post('/register')->send($user);

        $this->assertEquals(409, $result->status());
    }

    #[Test]
    public function login_returns_200_for_valid_parameters(): void
    {
        $user = UserFactory::createUser();
        $result = $this->agent->post('/login')->send($user);

        $this->assertEquals(200, $result->status());
    }

    #[Test]
    public function login_returns_422_for_invalid_parameters(): void
    {
        $body = [];
        $result = $this->agent->post('/login')->send($body);

        $this->assertEquals(422, $result->status());
    }

    #[Test]
    public function login_returns_401_for_wrong_password(): void
    {
        $user = UserFactory::createUser();
        $result = $this->agent->post('/login')->send([...$user, 'password' => 'senha-errada']);

        $this->assertEquals(401, $result->status());
    }

    #[Test]
    public function login_returns_401_for_unknown_user(): void
    {
        $user = UserFactory::createCredentials();
        $result = $this->agent->post('/login')->send($user);

        $this->assertEquals(401, $result->status());
    }

    #[Test]
    public function receive_token_on_login(): void
    {
        $user = UserFactory::createUser();
        $response = $this->agent->post('/login')->send($user);
        $token = $response->body()['data']['token'];

        $this->assertNotEmpty($token);
        $this->assertEquals($user['id'], $response->body()['data']['user']['id']);
    }

    #[Test]
    public function login_does_not_expose_the_password(): void
    {
        $user = UserFactory::createUser();
        $response = $this->agent->post('/login')->send($user);

        $this->assertArrayNotHasKey('password', $response->body()['data']['user']);
        $this->assertStringNotContainsString($user['password'], $response->text());
    }

    #[Test]
    public function logout_returns_200_when_ok(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);

        $token = $create->body()['data']['token'];
        $response = $this->agent->post('/logout')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(200, $response->status());
    }

    #[Test]
    public function logout_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->post('/logout');

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function logout_returns_401_for_invalid_token(): void
    {
        $response = $this->agent->post('/logout')->set('Authorization', 'Bearer token-invalido');

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function token_is_revoked_after_logout(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);

        $token = $create->body()['data']['token'];
        $this->agent->post('/logout')->set('Authorization', "Bearer {$token}")->status();

        $response = $this->agent->get('/categories')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(401, $response->status());
    }
}
