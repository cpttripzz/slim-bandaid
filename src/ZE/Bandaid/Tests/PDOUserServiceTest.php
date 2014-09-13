<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 06/09/14
 * Time: 23:03
 */

namespace ZE\Bandaid\Tests;

use ZE\Bandaid\Service\PDOUserService;

class PDOUserServiceTest extends Abstract_TestCase
{
    private $service;

    public function __construct()
    {
        parent::__construct('pdo');
        $this->service = new PDOUserService($this->db);
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
        $this->assertEquals($user['id'], 1);
    }

}