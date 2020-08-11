<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     */
    public function report(Throwable $exception)
    {
      if(env('APP_DEBUG')=='false'){
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
          app('sentry')->captureException($exception);
        }
      }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
      if ($this->isHttpException($exception)) {
        if ($exception->getStatusCode() == 403) {
          $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
          ];

          return response()->view('errors.403', [
            'pageConfigs' => $pageConfigs
          ]);
        }
        if ($exception->getStatusCode() == 404) {
          $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
          ];

          return response()->view('errors.404', [
            'pageConfigs' => $pageConfigs
          ]);
        }
        if ($exception->getStatusCode() == 503) {
          $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
          ];

          return response()->view('errors.503', [
            'pageConfigs' => $pageConfigs
          ]);
        }
        if ($exception->getStatusCode() == 500) {
          $pageConfigs = [
            'bodyClass' => "bg-full-screen-image",
            'blankPage' => true
          ];

          return response()->view('errors.500', [
            'pageConfigs' => $pageConfigs
          ]);
        }
      }
        return parent::render($request, $exception);
    }
}
