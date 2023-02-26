<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Models\Post;

class PostController extends Controller
{
     /**
     * Fetch a listing of the resource.
     */
    public function index()
    {
        $user = $this->user();

        $posts = Post::query()
        ->fromFollowsFor($user)
        ->includeTotalLikes()
        ->includeLikeStatusFor($user)
        ->fromRecent()
        ->paginate();

        return new PostCollection($posts);
    }

    /**
     * Fetch the specified resource.
     */
    public function show(int $id)
    {
        $user = $this->user();

        $post = Post::query()
        ->byId($id)
        ->includeTotalLikes()
        ->includeLikeStatusFor($user)
        ->firstOrFail();

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->updateContent($request->validated('content'));

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }

     /**
     * Like a post.
     */
    public function like(Post $post)
    {
        $this->user()->likePost($post);
        
        return response()->noContent();
    }

    /**
     * Unlike a post.
     */
    public function unlike(Post $post)
    {
        $this->user()->unlikePost($post);

        return response()->noContent();
    }
}
