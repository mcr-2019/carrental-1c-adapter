<?php

namespace Dataloft\Carrental;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Psr\Log\LoggerInterface;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $config_path = __DIR__.'/config/carrental.php';

        if (function_exists('config_path')) {
            $publishPath = config_path('carrental.php');
        } else {
            $publishPath = base_path('config/carrental.php');
        }

        $this->publishes([$config_path => $publishPath], 'config');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('carrental', function ($app) {

            /** @var Store $cacheInstance */
            $cacheInstance = $this->app->make('cache')
                ->driver(config('carrental.cache.driver', 'array'));

            /** @var LoggerInterface $loggerInstance */
            $loggerInstance = $this->app->make('log');

            return new ConnectionManager($cacheInstance, $loggerInstance);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['carrental'];
    }
}
