<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 13/09/14
 * Time: 16:28
 */

namespace ZE\Bandaid\Service;


interface AssociationServiceInterface {
    public function __construct($db);

    public function getAssociation($conditions);

    public function getBandsWithVacancies();
} 