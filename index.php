<?php
session_start();
require 'vendor/autoload.php';
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // return only the headers and not the content
    // only allow CORS if we're doing a GET - i.e. no saving for now.
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Requested-With');
    }
    exit;
}

$config['database'] = array(
    'driver' => 'mysql',
    'dsn' => 'mysql:dbname=test_bandaid;host=localhost',
    'username' => 'root',
    'password' => '123456',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
);
$config['routes'] = array('bands', 'auth', 'user');

$app = new \Slim\Slim();

$app->add(new \Slim\Middleware\SessionCookie(array('secret' => $app->getRandomUuid)));

$getRandomUuid = function ($app) {
    return function () use ($app) {
        return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    };
};



$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
//            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            returnJson(array('success'=>false, 'statusCode'=>401));
        }
    };
};
function returnJson ($returnArray) {
        $app = \Slim\Slim::getInstance();

        if ($returnArray['success'] === true){
            $app->response()->status(200);
        } else {
            $statusCode = (empty($returnArray['statusCode'])) ? 400 : $returnArray['statusCode'];
            $app->response()->status($statusCode);
        }
        $callback = (empty($returnArray['callback'])) ? null : $returnArray['callback'];
        if ($callback) {
            echo $callback . '(' . json_encode($returnArray) .');';
            exit;
        } else {
            $app->response()->header('Content-Type', 'application/json');
            echo json_encode($returnArray);
            exit;
        }

};


try {
    $app->container->singleton('pdo', function () use ($config) {
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