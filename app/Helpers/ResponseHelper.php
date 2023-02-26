<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ResponseHelper
{
    const OK           = 200;
    const CREATED      = 201;
    const NO_CONTENT   = 204;
    const UNAUTHORIZED = 401;
    const FORBIDDEN            = 403;
    const NOT_FOUND            = 404;
    const METHOD_NOT_SUPPORTED = 405;

    public static function oK($resource) : Response
    {
        return response($resource, self::OK);
    }

    public static function noContent() : Response
    {
        return response('', self::NO_CONTENT);
    }

    public static function notFound(string $message='Resource not found.') : Response
    {
        return response(['message' => $message], self::NOT_FOUND);
    }

    public static function unauthorized(string $message='This action is unauthorized') : Response
    {
        return response(['message' => $message], self::UNAUTHORIZED);
    }

    public static function denied(string $message='Access Denied') : Response
    {
        return response(['message' => $message], self::FORBIDDEN);
    }

    public static function methodNotSupported(string $message='Method not supported') : Response
    {
        return response(['message' => $message], self::METHOD_NOT_SUPPORTED);
    }

    public static function unauthenticated(string $message='Unauthenticated.') : Response
    {
        return response(['message' => $message], self::UNAUTHORIZED);
    }

    public static function forbidden(string $message='The action is forbidden.') : Response
    {
        return response(['message' => $message], self::FORBIDDEN);
    }
}