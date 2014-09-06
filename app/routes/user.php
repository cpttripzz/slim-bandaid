<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 06/09/14
 * Time: 18:29
 */

$app->group('/user', function () use ($app) {
    $app->post('/register', function ($id) {

    });
    $app->post("/login", function () use ($app) {
        $email = $app->request()->post('email');
        $password = $app->request()->post('password');

        if ($email != "brian@nesbot.com") {
            $errors['email'] = "Email is not found.";
        } else if ($password != "aaaa") {
            $app->flash('email', $email);
            $errors['password'] = "Password does not match.";
        }

        if (count($errors) > 0) {
            $app->flash('errors', $errors);
            $app->redirect('/login');
        }

        $_SESSION['user'] = $email;

        if (isset($_SESSION['urlRedirect'])) {
            $tmp = $_SESSION['urlRedirect'];
            unset($_SESSION['urlRedirect']);
            $app->redirect($tmp);
        }

        $app->redirect('/');
    });

    $app->put('/logout', function ($id) {

    });
});