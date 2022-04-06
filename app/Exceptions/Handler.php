<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
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

        $this->renderable(function (MethodNotAllowedHttpException $error, $request) {
            if (App::isProduction()) {
                return response()->json([
                    'status' => false,
                    "code" => 0,
                    "locale" => app()->getLocale(),
                    'message' => __('Method not allow.')
                ], 405);
            }
        });

        $this->renderable(function (NotFoundHttpException $error, $request) {
            if (App::isProduction()) {
                return response()->json([
                    'status' => false,
                    "code" => 0,
                    "locale" => app()->getLocale(),
                    'message' => __('Not found.')
                ], 404);
            }
        });
    }
}
