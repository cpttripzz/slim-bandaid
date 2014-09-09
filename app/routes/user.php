<?php
use \ZE\Bandaid\Security\Oauth\ClientModel;
use \ZE\Bandaid\Security\Oauth\SessionModel;
use \ZE\Bandaid\Security\Oauth\ScopeModel;


$app->group('/user', function () use ($app) {
    $userService = new \ZE\Bandaid\Service\UserService($app->pdo);

    $app->map('/register', function ($app,$userService) {
        $params = $app->request()->params();
        empty($params['username']) ? returnJson(array('success' => false, 'message' => 'User blank')) : $username =$params['username'];
        empty($params['password']) ? returnJson(array('success' => false, 'message' => 'Password blank')) : $password = $params['password'];
        $email = empty($params['email']) ? null : $params['email'];
        returnJson($userService->createUser($username,$password, $email));
    })->via('GET', 'POST');

    $app->post("/login", function () use ($app,$userService) {

        $params = $app->request()->params();
        empty($params['username']) ? returnJson(array('success' => false, 'message' => 'User blank')) : $username =$params['username'];
        empty($params['password']) ? returnJson(array('success' => false, 'message' => 'Password blank')) : $password = $params['password'];
        if(!$user = $userService->getUserByCredentials($username,$password)){
            returnJson(array('success' => false, 'message' => 'User or password do not match'));
        }
        $_SESSION['user'] = $user;
        $request = new \League\OAuth2\Server\Util\Request();
        $server = new \League\OAuth2\Server\Authorization(new ClientModel(), new SessionModel($app->db), new ScopeModel());

        $server->addGrantType(new \League\OAuth2\Server\Grant\AuthCode());
        $server->addGrantType(new \League\OAuth2\Server\Grant\Implicit());
        $authParams = $server->getGrantType('implicit')->completeFlow($params);
//        $redirectUri = $server->getGrantType('authorization_code')->newAuthorizeRequest('user', $user['id'], $authParams);

        echo json_encode($p);
        returnJson(array('success' => true, 'message' => 'Logged In', 'token' => $p));

    });

    $app->put('/logout', function () {
        unset($_SESSION['user']);
    });
});