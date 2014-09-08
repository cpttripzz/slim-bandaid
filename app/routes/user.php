<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 06/09/14
 * Time: 18:29
 */

$app->group('/user', function () use ($app) {
    $userService = new \ZE\Bandaid\Service\UserService($app->pdo);

    $app->map('/register', function ($app,$userService) {
        $params = $app->request()->params();
        $username = empty($params['username']) ? null : $params['username'];
        $password = empty($params['password']) ? null : $params['password'];
        $email = empty($params['email']) ? null : $params['email'];
        returnJson($userService->createUser($username,$password, $email));
    })->via('GET', 'POST');

    $app->post("/login", function () use ($app,$userService) {
        $username = $app->request()->post('username');
        $password = $app->request()->post('password');
        $email = $app->request()->post('email');
        if(!$user = $userService->getUserByCredentials($username,$password, $email)){
            $app->returnJson(array('success' => false, 'User or password do not match'));
        }
        $_SESSION['user'] = $user;
    });

    $app->put('/logout', function () {
        unset($_SESSION['user']);
    });
});