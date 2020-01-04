<?php

namespace Demo\SendPush\Providers;

use Illuminate\Support\ServiceProvider;

class SendPushServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
    	$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    	$this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
    }
}
