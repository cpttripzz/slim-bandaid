<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 13/09/14
 * Time: 16:37
 */

namespace ZE\Bandaid\Factory;
use ZE\Bandaid\Service;

class ServiceFactory {
    public static function create($dbType, $db, $className)
    {
        switch($dbType){
            case 'pdo':
                $class ='ZE\Bandaid\Service\PDO' . $className.'Service';

            break;
            case 'mongo':
                $class ='ZE\Bandaid\Service\Mongo' . $className.'Service';
            break;
        }
        $object = new $class($db);
        return $object;
    }
} 