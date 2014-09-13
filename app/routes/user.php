<?php
$app->group('/user', function () use ($app) {

    $userService = ZE\Bandaid\Factory\UserServiceFactory::create($app['activeDb'], $app->db);

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
            returnJson(array('message' => 'User or password do not match'),false,403);
        }
        $token = array('id' =>$user['id'], 'timestamp'=> time() );

        $user['token'] = JWT::encode($token, getJWTSecret());
        returnJson(array('message' => 'Logged In', 'user' =>$user ));

    });

    $app->put('/logout', function () {

    });
});