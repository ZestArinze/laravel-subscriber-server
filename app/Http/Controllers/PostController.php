<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Get a listing of posts.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {

        // ignores pagination

        $data = Post::all();

        return response()->json([
            'status'  => true,
            'message' => 'OK.',
            'data'    => $data,
            'error'   => null,
        ]);
    }
}
