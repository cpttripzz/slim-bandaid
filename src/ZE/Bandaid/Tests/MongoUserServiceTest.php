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

    public function testUsersLoaded()
    {
        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users/mongo');
        $this->setFixtures(array('user'=>array()));
        $data = $this->returnFixturesData(true);
        foreach($data['user'] as $user){
            $this->service->createUser($user['email'],$user['password'],$user['email']);
        }

    }
//    public function testUserCreated()
//    {
//        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users');
//        $this->setFixtures(array('user'), true);
//        $this->loadFixtures(true);
//        $this->truncateTables(array('user'));
//        $username = 'bboplifa@gmail.com';
//        $password = '1';
//        $email = 'bboplifa@gmail.com';
//        $this->service->createUser($username,$password,$email);
//        $user = $this->service->getUserByCredentials($username,$password);
//        $this->assertEquals($user['email'], $username);
//    }
//
//    public function testReturnsUserOnValidData()
//    {
//        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users');
//        $this->setFixtures(array('user'));
//        $this->loadFixtures(true);
//        $username = 'admin@admin.com';
//        $password = '123456';
//
//        $user = $this->service->getUserByCredentials($username,$password);
//        $this->assertEquals($user['email'], $username);
//    }
//
//    public function testDoesNotReturnUserOnInvalidData()
//    {
//        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users');
//        $this->setFixtures(array('user'));
//        $this->loadFixtures(true);
//        $username = 'admin@admin.com';
//        $password = '1234534c6';
//
//        $user = $this->service->getUserByCredentials($username,$password);
//        $this->assertEquals($user, null);
//    }

}