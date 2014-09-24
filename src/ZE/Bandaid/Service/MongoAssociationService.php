<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 24/09/14
 * Time: 13:50
 */

namespace ZE\Bandaid\Service;


class MongoAssociationService implements AssociationServiceInterface {

    protected $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAssociation($conditions)
    {
        $iterator = $this->db->association->find($conditions);
        return iterator_to_array($iterator,true);
    }

    public function getBandsWithVacancies()
    {
        return $this->getAssociation(array('band_vacancies' => array('exists' => true )));
    }
}