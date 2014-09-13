<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 06/09/14
 * Time: 23:03
 */

namespace ZE\Bandaid\Tests;

use ZE\Bandaid\Service\MongoUserService;

class MongoUserServiceTest extends Abstract_TestCase
{
    private $service;

    public function __construct()
    {
        parent::__construct('mongo');
        $this->service = new MongoUserService($this->db);
    }
    public function testUserCreated()
    {
//        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users');
//        $this->setFixtures(array('users'), true);
//        $this->loadFixtures(true);
        $this->truncateTables(array('users'));
        $username = 'bboplifa@gmail.com';
        $password = '123456';
        $email = 'bboplifa@gmail.com';
        $this->service->createUser($username,$password,$email);
        $user = $this->service->getUserByCredentials($username,$password);
        $this->assertEquals($user['email'], $username);
    }

}