<?php
$app->group('/user', function () use ($app) {
    $userService = new \ZE\Bandaid\Service\UserService($app->pdo);

    $app->map('/register', function ($app,$userService) {
        $params = $app->request()->params();
        empty($params['email']) ? returnJson(array('success' => false, 'message' => 'User blank')) : $email =$params['email'];
        empty($params['password']) ? returnJson(array('success' => false, 'message' => 'Password blank')) : $password = $params['password'];
        $email = empty($params['email']) ? null : $params['email'];
        returnJson($userService->createUser($email,$password, $email));
    })->via('GET', 'POST');

    $app->post("/login", function () use ($app,$userService) {

        $params = $app->request()->params();
        empty($params['email']) ? returnJson(array('success' => false, 'message' => 'User blank')) : $email =$params['email'];
        empty($params['password']) ? returnJson(array('success' => false, 'message' => 'Password blank')) : $password = $params['password'];
        if(!$user = $userService->getUserByCredentials($email,$password)){
            returnJson(array('success' => false, 'message' => 'User or password do not match'));
        }
        $token = array('id' =>$user['id'], 'timestamp'=> time() );

        returnJson(array('success' => true, 'message' => 'Logged In', 'token' => JWT::encode($token, getJWTSecret())));

    });

    $app->put('/logout', function () {

    });
});