<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Testing\AssertableInertia;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Testing\TestResponse;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if( ! $this->app->runningUnitTests()) {
            return;
        }

        // This is extending the AssertableInertia class (used in Inertia testing) with a custom method called hasResource
        AssertableInertia::macro('hasResource', function( string $key, JsonResource $resource) {
            $props = $this->toArray()['props'];
            // props are the data passed from Laravel to your frontend page/component (post and posts)

            $compiledResource = $resource->response()->getData(true);
            // This takes the JsonResource (PostResource in this case) and compiles it into the actual JSON array that Laravel would normally send in an API response.

            expect($props)
            ->toHaveKey($key, message: "Key \"{$key}\" not passed as a property to Inertia.")
            ->and($props[$key])
            ->toEqual($compiledResource);

            return $this;
        });

        AssertableInertia::macro('hasPaginatedResource', function( string $key, ResourceCollection $resource) {
            $props = $this->toArray()['props'];

            $compiledResource = $resource->response()->getData(true);

            expect($props)
            ->toHaveKey($key, message: "Key \"{$key}\" not passed as a property to Inertia.")
            ->and($props[$key])
            ->toHaveKeys(['data', 'links', 'meta']) // we will assume that if this keys exist then it is paginated
            ->data
            ->toEqual($compiledResource);

            return $this;
        });

        TestResponse::macro('assertHasResponse', function( string $key, JsonResource $resource) {
            /** @var AssertableInertia $this */
            return $this->assertInertia(fn (AssertableInertia $inertia) => $inertia->hasResource($key, $resource));
        });
        
        TestResponse::macro('assertHasPaginatedResource', function (string $key, ResourceCollection $resourceCollection) {
            /** @var AssertableInertia $this */
            return $this->assertInertia(fn (AssertableInertia $page) => $page->hasPaginatedResource($key, $resourceCollection));
        });

    }
}
