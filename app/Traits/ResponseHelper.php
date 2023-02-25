<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

trait ResponseHelper
{
    /**
     * Unauthorized
     */
    const UNAUTHORIZED_STATUS_CODE = 401;

    /**
     * Resource not found 
     */
    const NOT_FOUND_STATUS_CODE = 404;

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
    public function respondToGet(JsonResource|ResourceCollection $resource) : JsonResponse
    {
        return response($resource, self::GET_STATUS_CODE)->json();
    }

    /**
     * Return a POST response
     */
    public function respondToPost(JsonResource|ResourceCollection $resource) : JsonResponse
    {
        return response($resource, self::POST_STATUS_CODE)->json();
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