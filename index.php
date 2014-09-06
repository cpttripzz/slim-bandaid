<?php
session_start();
require 'vendor/autoload.php';

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$config['database'] = array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'bandaid',
    'username' => 'root',
    'password' => '123456',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
);
$config['routes'] = array('bands', 'auth');

// initialize app
$app = new \Slim\Slim();


try {
    $app->container->singleton('db', function () use ($config) {
        $capsule = new Illuminate\Database\Capsule\Manager;
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->addConnection($config['database']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule->getConnection();
    });

} catch (PDOException $exception) {
    var_dump($exception);
}
foreach ($config['routes'] as $route) {
    require 'app/routes/' . $route . '.php';
}

$app->run();