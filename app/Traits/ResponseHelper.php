<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    /**
     * Fetch a resources or collection of resouces
     */
    const GET_STATUS_CODE = 200;

    /**
     * A new resource was created
     * Return created content
     */
    const POST_STATUS_CODE = 201;

    /**
     * Existing resource was modified.
     * Return no content
     */
    const UPDATE_STATUS_CODE = 204;

    /**
     * Existing resource was deleted.
     * Return no content
     */
    const DELETE_STATUS_CODE = 204;

    /**
     * Return a GET response
     */
    public function respondToGet($response) : JsonResponse
    {
        return response($response, self::GET_STATUS_CODE)->json();
    }

    /**
     * Return a POST response
     */
    public function respondToPost($response) : JsonResponse
    {
        return response($response, self::POST_STATUS_CODE)->json();
    }

    /**
     * Return a PUT/PATCH response
     */
    public function respondToUpdate() : JsonResponse
    {
        return response('', self::UPDATE_STATUS_CODE)->json();
    }

    /**
     * Return a DELETE response
     */
    public function respondToDelete() : JsonResponse
    {
        return response('', self::DELETE_STATUS_CODE)->json();
    }
}