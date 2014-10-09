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
    public function getAssociation($conditions,$lastElement=null,$direction=null)
    {
        return $this->getPaginatedFind($conditions,$lastElement,$direction);
    }

    public function getBandsWithVacancies($lastElement=null,$direction=null)
    {
        return $this->getAssociation(array('band_vacancies' => array('$exists' => true )),$lastElement,$direction);
    }

}