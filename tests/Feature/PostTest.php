<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 
     * get all topics success
     */
    public function test_getPosts_success_postsRetrieved(): void {
        
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        $response = $this->get('/api/posts', [
            'Accept' => 'application/json'
        ]);

        $this->assertDatabaseCount('posts', 2);

        $response->assertJson([
            'status' => true,
            'message' => 'OK.',
            'data' => [
                [
                    'id' => $post1->id,
                    'title' => $post1->title,
                ],
                [
                    'id' => $post2->id,
                    'title' => $post2->title,
                ],
            ],
        ]);
    }
}
