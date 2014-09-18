<?php
session_start();
require 'vendor/autoload.php';
/*if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // return only the headers and not the content
    // only allow CORS if we're doing a GET - i.e. no saving for now.
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) ) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Requested-With');
    }
    exit;
}*/
$logger = new \Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => array(
        new \Monolog\Handler\StreamHandler('logs/'.date('Y-m-d').'.log'),
    ),
));
$app = new \Slim\Slim(array(
    'log.writer' => $logger));

$app->dbType = 'mongo';
$config['dbType'] = $app->dbType;
$config['databases'] = array(
    'mysql' => array(
        'dsn' => 'mysql:dbname=test_bandaid;host=localhost',
        'username' => 'root',
        'password' => '123456',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ),
    'mongo' => array(
        'dbHost' => 'localhost',
        'dbName' => 'bandaid'
    )
);

$config['routes'] = array('bands', 'auth', 'user');

function getJWTSecret()
{
    $secret = 'f8916451dab8ccdcfb28158383fd8783c0dcf4b05c5d69cea9b2188fbf62a92';
    return $secret;
}

$getRandomUuid = function ($app) {
    return function () use ($app) {
        return bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    };
};


$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
//            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            returnJson(array('success' => false, 'statusCode' => 401));
        }
    };
};
function returnJson($returnArray, $success = true, $statusCode = 200)
{
    $app = \Slim\Slim::getInstance();

    $app->response()->status($statusCode);
    $callback = (empty($returnArray['callback'])) ? null : $returnArray['callback'];
    if ($callback) {
        unset($returnArray['callback']);
        echo $callback . '(' . json_encode($returnArray) . ');';
        exit;
    } else {
        if (!$success) {
            $app->log->debug(print_r(headers_list(),true));
            $app->status($statusCode);
            $app->response()->header('Content-Type', 'application/json');
            $app->response->write(json_encode($returnArray));

            header('Access-Control-Allow-Origin: '. $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Headers: X-Requested-With');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

            $app->stop();
        } else {

            $app->response()->header('Content-Type', 'application/json');
            $app->response->write(json_encode($returnArray));

        }
    }


}

try {
    $app->container->singleton('db', function () use ($config) {
        switch ($config['dbType']) {
            case 'mysql':
                $options = array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC
                );
                $pdo = new \PDO($config['database']['dsn'], $config['database']['username'], $config['database']['password'], $options);
                return $pdo;
                break;
            case 'mongo':
                $dbHost = $config['databases']['mongo']['dbHost'];
                $dbName = $config['databases']['mongo']['dbName'];
                $server = "mongodb://$dbHost:27017";
                $m = new \MongoClient($server);
                $db = $m->$dbName;
                return $db;
                break;
        }
    });
} catch (PDOException $exception) {
    var_dump($exception);
}

foreach ($config['routes'] as $route) {
    require 'app/routes/' . $route . '.php';
}

$app->run();