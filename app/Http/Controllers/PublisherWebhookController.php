<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use App\Utils\SecurityUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Throwable;

class PublisherWebhookController extends Controller
{
    /**
     * Handle incoming webhook notifications
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function handleNotification(Request $request) {

        logger('----------> request->data');
        logger($request->data);
        logger('----------> request->all()');
        logger($request->all());

        // make sure the request came from the publisher and 
        // respond early before processing further
        if(SecurityUtils::isValidPublisherHashMac($request->header('HashMac'))) {
            http_response_code(200);
        } else {
            exit();
        }

        $validatedData = Validator::make($request->data,[
            'title'    => 'required|string|max:250|unique:posts,title',
            'body'     => 'required|string|max:50000',
            'slug'     => 'required|string|max:1000|unique:posts,slug',
        ])->validate();

        Post::create($validatedData);
    }

    /**
     * Subscribe to topic
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $topicIdentifier a unique identifier for the topic
     * @return JsonResponse
     */
    public function subscribe(Request $request, $topicIdentifier): JsonResponse
    {
        $error = null;
        $status = false;
        $responseMsg = '';
        $responseCode = 200;
        $responseData = null;

        $request->merge(['topic_identifier' => $topicIdentifier]);
        $request->validate([
            'topic_identifier'   => 'required|string'
        ]);

        $subscriptionUrl = config('webhook.publisher_api_base_url') . '/subscribe/' . $request->topic_identifier;
        $callbackUrl = url(config('webhook.publisher_callback_url_path'));
        
        try {

            // the ClientId and HashMac are neccessary:
            // the publisher sends the same HMAC along with notifications
            // with that we can check and be sure that the notification acutally came from 
            // the publisher
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'ClientId' => config('webhook.publisher_client_id'),
                'HashMac'  => SecurityUtils::getPublisherHashMac(),
            ])->post($subscriptionUrl, [
                'url' => $callbackUrl,
            ]);

            $resJson = $response->json();

            if($response->successful() && $resJson['status'] === true) {

                $topicData = [
                    'topic'         => $resJson['topic'],
                    'identifier'    => $request->topic_identifier,
                ];

                $topic = Topic::where('identifier', $request->topic_identifier)->first();
                if($topic) {
                    $topic->update($topicData);
                    $responseCode = 200;
                } else {
                    $topic = Topic::create($topicData);
                    $responseCode = 201;
                }
                
                $responseData = [
                    'topic'         => $topic->topic,
                    'url'           => $callbackUrl,
                    'identifier'    => $topic->identifier,
                ];
                $responseMsg = 'Subscription successful.';
                $status = true;
            } else {
                $responseCode = 422;
                $responseMsg = isset($resJson['message']) ? $resJson['message'] : 'Failed to subscibe.';
                $error = isset($resJson['error']) ? $resJson['error'] : null;
            }
        } catch(Throwable $e) {
            $responseMsg = 'Error has occured.';
            // handle further? @TODO
            logger($e->getMessage());
        }
        
        return response()->json([
            'status'  => $status,
            'message' => $responseMsg,
            'data'    => $responseData,
            'error'   => $error,
        ], $responseCode);
    }
}