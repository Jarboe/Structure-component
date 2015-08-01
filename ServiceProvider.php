<?php

namespace Jarboe\Component\Structure;

use Jarboe\Component\Structure\Commands\CreateTableCommand;


class ServiceProvider extends \Illuminate\Support\ServiceProvider 
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/Http/routes.php';
        
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('LaravelLocalization', 'Mcamara\LaravelLocalization\Facades\LaravelLocalization');
    } // end boot
    

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ .'/config/structure.php', 'jarboe.c.structure'
        );
//        $this->app->register('Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider');


        $this->app['command.jarboe.component.structure.create_table'] = $this->app->share(
            function ($app) {
                return new CreateTableCommand();
            }
        );
        $this->commands(array(
            'command.jarboe.component.structure.create_table',
        ));
    } // end register
    

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            //
        );
    }

}