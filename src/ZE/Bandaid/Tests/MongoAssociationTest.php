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
        $this->setFixtures(array(
            'user' => array(),
            'association' => array(),
            'genre' => array(),
            'country' => array(),
            'instrument' => array(),
            'region' => array(
                'embed' =>
                    array('columns' => array(
                        'country_id' =>
                            array(
                                'dual_reference' => false,
                                'reference_table' => 'country',
                                'reference_table_id' => 'id',
                                'columns_to_embed' => array('code','name')

                            )
                    ))),
            'city' => array(
                'columns_to_delete' => array('region_id'),
                'embed' =>
                    array('columns' => array(
                        'country_id' =>
                            array(
                                'dual_reference' => false,
                                'reference_table' => 'country',
                                'reference_table_id' => 'id',
                                'columns_to_embed' => array('code','name')

                            )
                    ))),
            'address' => array(
                'embed' =>
                    array('columns' => array(
                        'city_id' =>
                            array(
                                'dual_reference' => false,
                                'reference_table' => 'city',
                                'reference_table_id' => 'id',
                                'columns_to_embed' => array('name')

                            ),
                        'region_id' =>
                            array(
                                'dual_reference' => false,
                                'reference_table' => 'region',
                                'reference_table_id' => 'id',
                                'columns_to_embed' => array('long_name')
                            )
                    )))
            ));
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
                        'reference_table_id' => 'genre_id',
                    ),
                'association_address' =>
                    array(
                        'dual_reference' => false,
                        'table_name' => 'association_address',
                        'update_table' => 'association',
                        'update_table_id' => 'association_id',
                        'reference_table' => 'address',
                        'reference_table_id' => 'address_id',
                        'embed' =>
                            array('columns' => array(
                                'address_id' =>
                                    array(
                                        'dual_reference' => false,
                                        'table_name' => 'association_address',
                                        'update_table' => 'association',
                                        'update_table_id' => 'id',
                                        'reference_table' => 'address',
                                        'reference_table_id' => 'id',
                                        'columns_to_embed' => array('address')
                                    ),
                            ))
                    ),
                'band_musician' =>
                    array(
                        'dual_reference' => true,
                        'dual_reference_field' => 'bands',
                        'dual_reference_ref_field' => 'musicians',
                        'table_name' => 'association',
                        'update_table' => 'association',
                        'update_table_id' => 'musician_id',
                        'reference_table' => 'association',
                        'reference_table_id' => 'band_id',
                    ),
                'musician_instrument' =>
                    array(
                        'dual_reference' => true,
                        'dual_reference_field' => 'instruments',
                        'dual_reference_ref_field' => 'musicians',
                        'table_name' => 'association',
                        'update_table' => 'association',
                        'update_table_id' => 'musician_id',
                        'reference_table' => 'instrument',
                        'reference_table_id' => 'instrument_id',
                    ),

            )
        );
        $this->loadJoinTableFixtures();

    }
}