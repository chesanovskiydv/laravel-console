<?php

namespace BwtTeam\LaravelConsole\Commands;

use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $configPath = __DIR__ . '/../../config/console-commands.php';

        if (function_exists('config_path')) {
            $publishPath = config_path('console-commands.php');
        } else {
            $publishPath = base_path('config/console-commands.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
        $this->mergeConfigFrom($configPath, 'console-commands');
    }
}
