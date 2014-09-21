<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 06/09/14
 * Time: 23:03
 */

namespace ZE\Bandaid\Tests;

use ZE\Bandaid\Service\MongoUserService;
use ZE\DBHelper\MongoDBHelper;

class MongoAssociationTest extends Abstract_TestCase
{
    private $service;

    public function __construct()
    {
        parent::__construct('mongo');
        $this->service = new MongoUserService($this->db);
    }

    public function testUserCreated()
    {
        $dbHelper = new MongoDBHelper($this->db);
        $dbHelper->setMongoIdsMap(array('user_id' => 'user'));
        $this->setDbHelper($dbHelper);
        $this->setFixturePath(getcwd() . '/src/ZE/Bandaid/Tests/fixtures/users/mongo');
        $this->setFixtures(array('user' => array(), 'association' => array(), 'genre' => array(), 'country' => array()));
        $this->loadFixtures(true);
        $this->setJoinTableFixtures(
            array(
                'association_genre' =>
                    array(
                        'dual_reference' => true,
                        'table_name' => 'association_genre',
                        'update_table' => 'association',
                        'update_table_id' => 'association_id',
                        'reference_table' => 'genre',
                        'reference_table_id' => 'genre_id'
                    )
            )
        );
        $this->loadJoinTableFixtures();
        $this->setFixtures(
            array('region' => array(
                'embed' =>
                    array('columns' => array(
                        'country_id' =>
                            array(
                                'dual_reference' => false,
                                'reference_table' => 'country',
                                'reference_table_id' => 'id',
                                'columns_to_embed' => array('code','name')

                            )
                    ))
            ))
        );
        $this->loadFixtures(false);

    }
}