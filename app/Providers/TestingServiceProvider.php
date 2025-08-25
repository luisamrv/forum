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
            $this->has($key);
            expect($this->prop($key))->toEqual($resource->response()->getData(true));
            return $this;
        });

        AssertableInertia::macro('hasPaginatedResource', function( string $key, ResourceCollection $resource) {
            $this->hasResource("{$key}.data", $resource);

            expect($this->prop($key))->toHaveKeys(['data', 'links', 'meta']); // we will assume that if this keys exist then it is paginated

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
