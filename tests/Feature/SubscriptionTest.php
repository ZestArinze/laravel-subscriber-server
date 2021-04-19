<?php

namespace Tests\Feature;

use App\Utils\SecurityUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    function test_notification_success_postCreated(): void {

        $this->withoutExceptionHandling();

        $headers = [
            'Accept' => 'application/json',
            'HashMac' =>  SecurityUtils::getPublisherHashMac(),
        ];

        $publisherResponse = [
            'topic' => 'Body Care',
            'data' => [
                'title'     => 'The best body care',
                'body'      => 'The post body',
                'slug'      => 'the-best-body-care',
            ],
        ];

        $response = $this->post('/api/webhooks/posts', $publisherResponse, $headers);

        $this->assertDatabaseCount('posts', 1);
    }
}
