<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 13/09/14
 * Time: 16:37
 */

namespace ZE\Bandaid\Factory;
use ZE\Bandaid\Service;

class UserServiceFactory {
    public static function create($dbType, $db)
    {
        switch($dbType){
            case 'pdo':
                return new Service\PDOUserService($db);
            break;
            case 'mongo':
                return new Service\MongoUserService($db);
            break;
        }
    }
} 