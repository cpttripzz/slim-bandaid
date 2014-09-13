<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 13/09/14
 * Time: 16:28
 */

namespace ZE\Bandaid\Service;


interface UserServiceInterface {
    public function __construct($db);

    public function getUserByCredentials($username,$password,$columns=null);

    public function createUser($username,$password, $email);
} 