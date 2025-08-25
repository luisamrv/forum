<?php

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\get;

it('should return the correct component', function() {
  Post::factory()->create(); // <-- Create a Post
  get(route('posts.index'))
  ->assertInertia(fn (AssertableInertia $inertia) => $inertia
    ->component('Posts/Index', true)
  );
});

/* it('passes posts to the view', function() {
  get(route('posts.index'))
  ->assertInertia(fn (AssertableInertia $inertia) => $inertia
    ->has('posts')
  );
}); */

it('passes posts to the view', function() {

  // new macros passed to TestingServiceProvider

  $posts = Post::factory(3)->create();

  /* get(route('posts.index'))
  ->assertInertia(fn (AssertableInertia $inertia) => $inertia
    ->hasResource('post', PostResource::make($posts->first()))
    // That means: “In the Inertia response, there should be a prop called post, and it should match exactly the resource PostResource::make($posts->first()).”
    ->hasPaginatedResource('posts', PostResource::collection($posts->reverse()))
  ); */

  get(route('posts.index'))
    ->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
});