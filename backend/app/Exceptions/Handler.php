<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
class Handler extends ExceptionHandler
{


    public function report(Throwable $e)
    {
        parent::report($e);
        try {
            \App\Jobs\SendErrorLog::dispatch(['class_name'=>get_class($e),'message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'trace'=>substr($e->getTraceAsString(), 0, 100)])->onConnection('redis-queue')->onQueue('redis-queue');
        } catch (\Exception $e) {

        }
    }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->header('content-type') == 'application/json') {
            header("Content-type:application/json");
            return response()->json([
                'message' => 'Not authorized'
            ], 403);
        }
        return redirect()->guest(route('d.login'));
    }
    public function respondUnauthenticated($request, \Illuminate\Auth\AuthenticationException $e)
    {
        return response()->json([
            'message' => 'Not authorized'
        ], 403);
    }
}
