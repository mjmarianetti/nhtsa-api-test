<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Nhtsa\NhtsaApi;
use App\Modules\Nhtsa\NhtsaResponseTransform;
use App\Modules\Nhtsa\Helpers\UrlHelper;
use GuzzleHttp\Client as Client;

class NhtsaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NhtsaApi::class, function ($app) {
            return new NhtsaApi(
                new Client(['base_uri' => config('nhtsa.baseUri')]),
                new NhtsaResponseTransform(),
                new UrlHelper()
            );
        });
    }
}
