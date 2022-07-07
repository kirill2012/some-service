<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
        ResourceCollection::withoutWrapping();

        Response::macro('success', function ($data = null, $status = 200) {
            return Response::json([
                'state' => true,
                'data' => $data,
            ], $status);
        });

        Response::macro('success_paginated', function ($data = null, $status = 200) {
            return Response::json($data, $status);
        });

        Response::macro('error', function ($error = ['code' => 666, 'message' => 'some very bad error..'], $status = 400) {
            return Response::json([
                'state' => false,
                'data' => [
                    'code' => $error['code'],
                    'message' => $error['message'],
                ],
            ], $status);
        });
    }
}
