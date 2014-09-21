<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 05/09/14
 * Time: 15:51
 */
namespace ZE\RDBMSToMongo\Adapter;

class YamlRDBMSToMongoAdapter
{
    
    protected $db;
    protected $manyToManyColumnMapping = array();

    public function __construct($db, $manyToManyColumnMapping)
    {
        $this->db = $db;
        $this->manyToManyColumnMapping = $manyToManyColumnMapping;
    }

    public function adapterDocument($arrFixtures){

    }

}