<?php

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use Tests\Factories\RecipeFactory;
use Tests\Factories\UserFactory;

class RecipeTest extends IntegrationTestCase
{
    #[Test]
    public function create_recipe_returns_201_for_valid_parameters(): void
    {
        $user = UserFactory::createUser();
        $response = $this->agent->post('/login')->send($user);
        $token = $response->body()['data']['token'];

        $body = RecipeFactory::createRecipeInfo();
        $result = $this->agent->post('/recipes')->send($body)->set('Authorization', "Bearer {$token}");

        $this->assertEquals(201, $result->status());
        $this->assertEquals($user['id'], $result->body()['data']['user_id']);
        $this->assertEquals($body['name'], $result->body()['data']['name']);
        $this->assertEquals($body['category_id'], $result->body()['data']['category_id']);
    }

    #[Test]
    public function create_recipe_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->post('/recipes')->send(RecipeFactory::createRecipeInfo());

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function create_recipe_returns_422_for_invalid_parameters(): void
    {
        $user = UserFactory::createUser();
        $response = $this->agent->post('/login')->send($user);
        $token = $response->body()['data']['token'];

        $body = [];
        $result = $this->agent->post('/recipes')->send($body)->set('Authorization', "Bearer {$token}");

        $this->assertEquals(422, $result->status());
    }

    #[Test]
    public function create_recipe_returns_422_for_unknown_category(): void
    {
        $user = UserFactory::createUser();
        $response = $this->agent->post('/login')->send($user);
        $token = $response->body()['data']['token'];

        $body = RecipeFactory::createRecipeInfo(9999);
        $result = $this->agent->post('/recipes')->send($body)->set('Authorization', "Bearer {$token}");

        $this->assertEquals(422, $result->status());
    }

    #[Test]
    public function create_recipe_returns_422_when_name_is_too_long(): void
    {
        $user = UserFactory::createUser();
        $response = $this->agent->post('/login')->send($user);
        $token = $response->body()['data']['token'];

        $body = [...RecipeFactory::createRecipeInfo(), 'name' => str_repeat('a', 46)];
        $result = $this->agent->post('/recipes')->send($body)->set('Authorization', "Bearer {$token}");

        $this->assertEquals(422, $result->status());
    }

    #[Test]
    public function get_recipes_returns_status_200_when_ok(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);

        $token = $create->body()['data']['token'];
        $response = $this->agent->get('/recipes')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(200, $response->status());
        $this->assertSame([], $response->body()['data']);
    }

    #[Test]
    public function get_recipes_returns_only_recipes_from_the_authenticated_user(): void
    {
        $user = UserFactory::createUser();
        $user2 = UserFactory::createUser();

        $mine = RecipeFactory::createRecipe($user['id']);
        RecipeFactory::createRecipe($user2['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->get('/recipes')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(200, $response->status());
        $this->assertCount(1, $response->body()['data']);
        $this->assertEquals($mine['id'], $response->body()['data'][0]['id']);
    }

    #[Test]
    public function get_recipes_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->get('/recipes');

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function get_recipe_by_id_returns_status_200_when_ok(): void
    {
        $user = UserFactory::createUser();
        $recipe = RecipeFactory::createRecipe($user['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->get("/recipes/{$recipe['id']}")->set('Authorization', "Bearer {$token}");

        $this->assertEquals(200, $response->status());
        $this->assertEquals($recipe['id'], $response->body()['data']['id']);
        $this->assertEquals($recipe['name'], $response->body()['data']['name']);
    }

    #[Test]
    public function get_recipe_by_id_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->get('/recipes/1');

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function get_recipe_by_id_returns_404_when_it_does_not_exist(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);

        $token = $create->body()['data']['token'];
        $response = $this->agent->get('/recipes/9999')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(404, $response->status());
    }

    #[Test]
    public function get_recipe_by_id_returns_404_for_a_recipe_from_another_user(): void
    {
        $user = UserFactory::createUser();
        $user2 = UserFactory::createUser();
        $recipe = RecipeFactory::createRecipe($user2['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->get("/recipes/{$recipe['id']}")->set('Authorization', "Bearer {$token}");

        $this->assertEquals(404, $response->status());
    }

    #[Test]
    public function update_recipe_returns_status_200_and_keeps_the_other_fields(): void
    {
        $user = UserFactory::createUser();
        $recipe = RecipeFactory::createRecipe($user['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $body = ['name' => 'Bolo de cenoura'];
        $response = $this->agent->patch("/recipes/{$recipe['id']}")->send($body)->set('Authorization', "Bearer {$token}");

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Bolo de cenoura', $response->body()['data']['name']);
        $this->assertEquals($recipe['preparation_method'], $response->body()['data']['preparation_method']);

        $saved = $this->agent->get("/recipes/{$recipe['id']}")->set('Authorization', "Bearer {$token}");
        $this->assertEquals('Bolo de cenoura', $saved->body()['data']['name']);
    }

    #[Test]
    public function update_recipe_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->patch('/recipes/1')->send(['name' => 'Novo nome']);

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function update_recipe_returns_404_when_it_does_not_exist(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);

        $token = $create->body()['data']['token'];
        $response = $this->agent->patch('/recipes/9999')->send(['name' => 'Novo nome'])->set('Authorization', "Bearer {$token}");

        $this->assertEquals(404, $response->status());
    }

    #[Test]
    public function update_recipe_returns_404_for_a_recipe_from_another_user(): void
    {
        $user = UserFactory::createUser();
        $user2 = UserFactory::createUser();
        $recipe = RecipeFactory::createRecipe($user2['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->patch("/recipes/{$recipe['id']}")->send(['name' => 'Novo nome'])->set('Authorization', "Bearer {$token}");

        $this->assertEquals(404, $response->status());
    }

    #[Test]
    public function update_recipe_returns_422_for_invalid_parameters(): void
    {
        $user = UserFactory::createUser();
        $recipe = RecipeFactory::createRecipe($user['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $body = ['category_id' => 9999];
        $response = $this->agent->patch("/recipes/{$recipe['id']}")->send($body)->set('Authorization', "Bearer {$token}");

        $this->assertEquals(422, $response->status());
    }

    #[Test]
    public function delete_recipe_returns_status_200_when_ok(): void
    {
        $user = UserFactory::createUser();
        $recipe = RecipeFactory::createRecipe($user['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->delete("/recipes/{$recipe['id']}")->set('Authorization', "Bearer {$token}");
        $this->assertEquals(200, $response->status());

        $deleted = $this->agent->get("/recipes/{$recipe['id']}")->set('Authorization', "Bearer {$token}");
        $this->assertEquals(404, $deleted->status());
    }

    #[Test]
    public function delete_recipe_returns_401_when_no_token_is_provided(): void
    {
        $response = $this->agent->delete('/recipes/1');

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function delete_recipe_returns_404_when_it_does_not_exist(): void
    {
        $user = UserFactory::createUser();
        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->delete('/recipes/9999')->set('Authorization', "Bearer {$token}");

        $this->assertEquals(404, $response->status());
    }

    #[Test]
    public function delete_recipe_returns_404_for_a_recipe_from_another_user(): void
    {
        $user = UserFactory::createUser();
        $user2 = UserFactory::createUser();

        $recipe = RecipeFactory::createRecipe($user2['id']);

        $create = $this->agent->post('/login')->send($user);
        $token = $create->body()['data']['token'];

        $response = $this->agent->delete("/recipes/{$recipe['id']}")->set('Authorization', "Bearer {$token}");

        $this->assertEquals(404, $response->status());
    }
}
