<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 25/09/14
 * Time: 14:29
 */

namespace ZE\Bandaid\Service\Mongo;


abstract class ServiceAbstract
{

    protected $db;
    protected $table;
    protected $sortField='_id';


    protected $sortDirection='-1';
    protected $limit=10;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getMongoDirection()
    {
        if($this->sortDirection < 1) {
            return '$gt';
        } else {
            return '$lt';
        }

    }
    public function getPaginatedFind($conditions=array(),$lastId=null)
    {
        $pageConditions = array();
        if($lastId) {
            $mongoId = new \MongoId($lastId);
            $pageConditions = array($this->sortField => array($this->getMongoDirection() => $mongoId));
        }
        $sort = array($this->sortField => $this->sortDirection);
        $conditions = array_merge($conditions, $pageConditions);
        $iterator = $this->db->{$this->table}->find($conditions)->sort($sort)->limit($this->limit);
        return iterator_to_array($iterator,true);
    }
}