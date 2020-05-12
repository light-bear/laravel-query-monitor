<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/5/12
 * Time: 10:42
 */

namespace LightBear\LaravelQueryMonitor;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MonitorCommand::class,
            ]);
        }

        $path = realpath(__DIR__ . '/../config.php');

        $this->publishes([$path => config_path('laravel-query-monitor.php')], 'config');

        $this->mergeConfigFrom($path, 'laravel-query-monitor');
        /*
         * Setting
         */
        $host = config('laravel-query-monitor.host');
        $port = config('laravel-query-monitor.port');

        if ($host && $port) {

            $dispatchQueries = new DispatchQueries($host, $port);

            DB::listen(function ($query) use ($dispatchQueries) {

                if (config('laravel-query-monitor.enable')) {

                    $dispatchQueries->send($query);
                }

            });
        }
    }
}