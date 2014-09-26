<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 24/09/14
 * Time: 13:50
 */

namespace ZE\Bandaid\Service\Mongo;

class AssociationService extends ServiceAbstract
{

    public function __construct($db)
    {
        $this->table = 'association';
        parent::__construct($db);
    }
    public function getAssociation($conditions,$lastId=null)
    {
        return $this->getPaginatedFind($conditions,$lastId);
    }

    public function getBandsWithVacancies($lastId=null)
    {
        return $this->getAssociation(array('band_vacancies' => array('$exists' => true )),$lastId);
    }

}