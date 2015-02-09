<?php

namespace DemocracyApps\CNP\Ajax;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseAjaxHandler {

    abstract static function handle ($func, Request $request);

    protected static function oKResponse ($message, $data) {
        $resp = [
            'message' => $message,
            'status_code'	=> Response::HTTP_OK,
            'data' => $data
        ];
        return $resp;
    }

    protected static function errorResponse ($code, $message) {
        $resp = [
            'status_code'	=> $code,
            'error' => [
                'message' 		=> $message,
                'status_code'	=> $code
            ]
        ];
        return $resp;
    }

    protected static function notFoundResponse ($message) {
        return self::errorResponse(Response::HTTP_NOT_FOUND, $message);
    }

    protected static function formatErrorResponse ($message) {
        return self::errorResponse(Response::HTTP_BAD_REQUEST, $message);
    }
}