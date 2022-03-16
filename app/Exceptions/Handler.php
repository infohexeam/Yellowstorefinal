<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            // return redirect()->route('web.index');
            return redirect()->back();
        }

        if (Auth::guard('api')->check()) {
            if ($exception instanceof AuthenticationException) {
                return response()->json(
                    [
                        'type' => 'error',
                        'status' => Response::HTTP_UNAUTHORIZED,
                        'message' => 'Access Token expired',
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
            return parent::render($request, $exception);
        }
    }
}
