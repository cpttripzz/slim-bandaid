<?php
session_start();
require 'vendor/autoload.php';


$config['database'] = array(
    'driver' => 'mysql',
    'dsn' => 'mysql:dbname=bandaid;host=localhost',
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
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC
        );

        $pdo = new \PDO($config['database']['dsn'], $config['database']['username'], $config['database']['password'], $options);
        return $pdo;
    });
} catch (PDOException $exception) {
    var_dump($exception);
}

foreach ($config['routes'] as $route) {
    require 'app/routes/' . $route . '.php';
}

$app->run();