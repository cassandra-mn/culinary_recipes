<?php

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use Tests\Factories\UserFactory;

class CategoryTest extends IntegrationTestCase
{
    #[Test]
    public function get_categories_returns_status_200_when_ok(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);

        $token = $create->body()['data']['token'];
        $response = $this->agent->get('/categories')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(200, $response->status());
        $this->assertCount(13, $response->body()['data']);
        $this->assertEquals(['id', 'name'], array_keys($response->body()['data'][0]));
    }

    #[Test]
    public function get_categories_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->get('/categories');

        $this->assertEquals(401, $response->status());
    }
}
