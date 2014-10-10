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


    protected $sortDirection=1;
    protected $limit=12;

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

    public function getMongoDirection($sortDirection)
    {
        if($sortDirection > 0) {
            return '$gt';
        } else {
            return '$lt';
        }

    }
    public function getPaginatedFind($conditions=array(),$fields=array(),$lastElement=null,$pageDirection=null)
    {
        $pageConditions = array();

        if($lastElement) {
            $mongoId = new \MongoId($lastElement);
            if($pageDirection=='back'){
                $this->sortDirection = -1;
            }
            $pageConditions = array($this->sortField => array($this->getMongoDirection($this->sortDirection) => $mongoId));
        }
        $sort = array($this->sortField => $this->sortDirection);

        $total = $this->db->{$this->table}->find($conditions)->count();
        $conditions = array_merge($conditions, $pageConditions);
        if($fields){
            $cursor = $this->db->{$this->table}->find($conditions,$fields)->sort($sort)->limit($this->limit);
        } else {
            $cursor = $this->db->{$this->table}->find($conditions)->sort($sort)->limit($this->limit);
        }

        $data = iterator_to_array($cursor,true);
        if($pageDirection=='back'){
            $data = array_reverse($data);
        }
        $meta = array('total' => $total, 'last_element'=>$lastElement);
        return(array('data' => $data, 'meta'=>$meta));
    }
}