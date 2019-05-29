<?php

namespace Molezinha\Providers;

use Illuminate\Support\ServiceProvider;
use Molezinha\Core\Molezinha;
use Molezinha\Traits\Loaders\AutoLoaderTrait;

/**
 * Class MolezinhaServiceProvider
 * @package Molezinha\Providers
 *
 * Inspired by : https://github.com/apiato/core/blob/master/Providers/ApiatoProvider.php
 *  and https://github.com/santigarcor/laratrust/blob/master/src/LaratrustServiceProvider.php
 */
class MolezinhaServiceProvider extends ServiceProvider
{
  use AutoLoaderTrait;
  /**
   * The commands to be registered.
   *
   * @var array
   */
  protected $commands = [
    'CreateContainer' => 'command.molezinha.createcontainer',
    'CreateModel' => 'command.molezinha.createmodel',
    'CreateMigration' => 'command.molezinha.createmigration'

  ];


  public function boot()
  {
    $this->publishes([
      __DIR__.'\\..\\Config\\molezinha.php' => config_path('molezinha.php'),
    ]);

    //Load Containers and Ship Components
    $this->runBootLoader();


  }

  public function register()
  {
    $this->registerMolezinha();
    $this->registerCommands();

  }

  /**
   * Register the application bindings.
   *
   * @return void
   */
  private function registerMolezinha()
  {
    $this->app->bind('molezinha', function () {
      return new Molezinha();
    });

    $this->app->alias('molezinha', 'Molezinha\Core\Molezinha');
  }

  /**
   * Register the given commands.
   *
   * @return void
   */
  protected function registerCommands()
  {
    foreach (array_keys($this->commands) as $command) {
      $method = "register{$command}Command";

      call_user_func_array([$this, $method], []);
    }

    $this->commands(array_values($this->commands));
  }

  /**
   * Get the services provided.
   *
   * @return array
   */
  public function provides()
  {
    return array_values($this->commands);
  }

  protected function registerCreateContainerCommand()
  {
    $this->app->singleton('command.molezinha.createcontainer', function () {
      return new \Molezinha\Commands\CreateContainerCommand();
    });
  }

  protected function registerCreateModelCommand()
  {
    $this->app->singleton('command.molezinha.createmodel', function ($app) {
      return new \Molezinha\Commands\CreateModelCommand($app['files']);
    });
  }

  protected function registerCreateMigrationCommand()
  {
    $this->app->singleton('command.molezinha.createmigration', function () {
      return new \Molezinha\Commands\CreateMigrationCommand();
    });
  }



}