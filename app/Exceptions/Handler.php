<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // handle validation exception
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'data'    => null,
                    'error'   => $e->errors(),
                ], 422);
            }
        });

        // handle auth exception
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Authentication required.',
                    'data'    => null,
                    'error'   => null,
                ], 401);
            }
        });

        // handle 404
        $this->renderable(function (NotFoundHttpException $e, $request) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Not found.',
                    'data'    => null,
                    'error'   => null,
                ], 404);
            }
        });
    }
}
