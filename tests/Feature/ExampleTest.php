<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $category = Category::query()->create([
            'name' => 'Announcements',
            'slug' => 'announcements',
        ]);

        Post::query()->create([
            'category_id' => $category->id,
            'title' => 'Welcome',
            'description' => 'Initial content for the landing page.',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
