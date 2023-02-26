<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends Controller
{
     /**
     * Fetch a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        //
    }

    /**
     * Fetch the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }

     /**
     * Like a post.
     */
    public function like(Post $post)
    {
        $this->user()->unlike($post);
        
        return response()->noContent();
    }

    /**
     * Unlike a post.
     */
    public function unlike(Post $post)
    {
        $this->user()->like($post);

        return response()->noContent();
    }
}
