<?php
$app->group('/user', function () use ($app) {

    $userService = ZE\Bandaid\Factory\ServiceFactory::create($app->dbType, $app->db, 'User');

    $app->map('/register', function ($app,$userService) {
        $params = $app->request()->params();
        empty($params['email']) ? returnJson(array('success' => false, 'message' => 'User blank')) : $email =$params['email'];
        empty($params['password']) ? returnJson(array('success' => false, 'message' => 'Password blank')) : $password = $params['password'];
        $email = empty($params['email']) ? null : $params['email'];
        returnJson($userService->createUser($email,$password, $email));
    })->via('GET', 'POST');

    $app->post("/login", function () use ($app,$userService) {
        $params = $app->request()->params();
        empty($params['email']) ? returnJson(array('message' => 'User blank'),false,500) : $email = $params['email'];
        empty($params['password']) ? returnJson(array('message' => 'Password blank'),false,500) : $password = $params['password'];
        if(!$user = $userService->getUserByCredentials($email,$password)){
            returnJson(array('reasons' => 'User or password do not match'),false,401);
        }
        $token = array('email' =>$user['email'], 'timestamp'=> time() );
        $user['token'] = JWT::encode($token, getJWTSecret());
        returnJson(array('message' => 'Logged In', 'user' =>$user ));
    });
    $app->delete("/logout", function () use ($app,$userService) {
        $params = $app->request()->params();

        returnJson(array('message' => 'Logged Out'));
    });


    $app->put('/logout', function () {

    });
});