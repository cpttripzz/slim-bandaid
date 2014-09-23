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
            'band_vacancy' =>array(),
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
                    ))),
            'document' =>
                array(
                    'dual_reference' => true,
                    'table_name' => 'document',
                    'update_table' => 'document',
                    'update_table_id' => 'document_id',
                    'reference_table' => 'association',
                    'reference_table_id' => 'association_id',
                    'embed' =>
                        array('columns' => array(
                            'association_id' =>
                                array(
                                    'dual_reference' => true,
                                    'dual_reference_field' => 'associations',
                                    'dual_reference_ref_field' => 'documents',
                                    'table_name' => 'document',
                                    'update_table' => 'document',
                                    'update_table_id' => 'document_id',
                                    'reference_table' => 'association',
                                    'reference_table_id' => 'id',
                                    'columns_to_embed' => array('name','_id'),
                                    'ref_columns_to_embed' => array('path','_id'),
                                )
                        ))),

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
                'bandvacancy_genre' =>
                    array(
                        'dual_reference' => true,
                        'table_name' => 'bandvacancy_genre',
                        'update_table' => 'band_vacancy',
                        'update_table_id' => 'bandvacancy_id',
                        'reference_table' => 'genre',
                        'reference_table_id' => 'genre_id',
                        'embed' =>
                            array('columns' => array(
                                'genre_id' =>
                                    array(
                                        'dual_reference' => true,
                                        'dual_reference_field' => 'genres',
                                        'dual_reference_ref_field' => 'band_vacancies',
                                        'table_name' => 'bandvacancy_genre',
                                        'update_table' => 'band_vacancy',
                                        'update_table_id' => 'bandvacancy_id',
                                        'reference_table' => 'genre',
                                        'reference_table_id' => 'id',
                                        'columns_to_embed' => array('name','_id')
                                    ),
                            ))
                    ),
                'bandvacancy_instrument' =>
                    array(
                        'dual_reference' => true,
                        'table_name' => 'bandvacancy_instrument',
                        'update_table' => 'band_vacancy',
                        'update_table_id' => 'bandvacancy_id',
                        'reference_table' => 'instrument',
                        'reference_table_id' => 'instrument_id',
                        'embed' =>
                            array('columns' => array(
                                'instrument_id' =>
                                    array(
                                        'dual_reference' => true,
                                        'dual_reference_field' => 'instruments',
                                        'dual_reference_ref_field' => 'band_vacancies',
                                        'table_name' => 'bandvacancy_instrument',
                                        'update_table' => 'band_vacancy',
                                        'update_table_id' => 'bandvacancy_id',
                                        'reference_table' => 'instrument',
                                        'reference_table_id' => 'id',
                                        'columns_to_embed' => array('name','_id')
                                    ),
                            ))
                    ),
                'bandvacancy_association' =>
                    array(
                        'dual_reference' => true,
                        'table_name' => 'bandvacancy_association',
                        'update_table' => 'band_vacancy',
                        'update_table_id' => 'bandvacancy_id',
                        'reference_table' => 'association',
                        'reference_table_id' => 'band_id',
                        'embed' =>
                            array('columns' => array(
                                'band_id' =>
                                    array(
                                        'dual_reference' => true,
                                        'dual_reference_field' => 'bands',
                                        'dual_reference_ref_field' => 'band_vacancies',
                                        'table_name' => 'bandvacancy_association',
                                        'update_table' => 'band_vacancy',
                                        'update_table_id' => 'bandvacancy_id',
                                        'reference_table' => 'association',
                                        'reference_table_id' => 'id',
                                        'columns_to_embed' => array('name','_id')
                                    ),
                            ))
                    ),

            )
        );
        $this->loadJoinTableFixtures();
        $bandVacancy = $this->db->band_vacancy->findOne(array('id' => 1));
        $this->assertEquals('Country', $bandVacancy['genres']['name']);
        $genre = $this->db->genre->findOne(array('_id' => $bandVacancy['genres']['_id']));
        $this->assertEquals('Country', $genre['name']);
        $this->assertEquals($bandVacancy['genres']['_id']->{'$id'},$genre['_id']->{'$id'});
        $this->assertEquals($genre['band_vacancies'][0],$bandVacancy['_id']->{'$id'});

        $bandVacancy = $this->db->band_vacancy->findOne(array('id' => 82));
        $this->assertEquals('Jazz', $bandVacancy['genres']['name']);
        $genre = $this->db->genre->findOne(array('_id' => $bandVacancy['genres']['_id']));
        $this->assertEquals('Jazz', $genre['name']);
        $this->assertEquals($bandVacancy['genres']['_id']->{'$id'},$genre['_id']->{'$id'});
        $this->assertEquals($genre['band_vacancies'][7],$bandVacancy['_id']->{'$id'});

        $bandVacancy = $this->db->band_vacancy->findOne(array('id' => 2));
        $this->assertEquals('Reggae', $bandVacancy['genres']['name']);
        $this->assertEquals('Tuba', $bandVacancy['instruments']['name']);
        $genre = $this->db->genre->findOne(array('_id' => $bandVacancy['genres']['_id']));
        $instrument = $this->db->instrument->findOne(array('_id' => $bandVacancy['instruments']['_id']));
        $this->assertEquals('Reggae', $genre['name']);
        $this->assertEquals('Tuba', $instrument['name']);
        $this->assertEquals($bandVacancy['genres']['_id']->{'$id'},$genre['_id']->{'$id'});
        $this->assertEquals($bandVacancy['instruments']['_id']->{'$id'},$instrument['_id']->{'$id'});
        $this->assertEquals($genre['band_vacancies'][0],$bandVacancy['_id']->{'$id'});
        $this->assertEquals($instrument['band_vacancies'][0],$bandVacancy['_id']->{'$id'});

        $bandVacancy = $this->db->band_vacancy->findOne(array('id' => 12));
        $this->assertEquals('Punk Rock', $bandVacancy['genres']['name']);
        $this->assertEquals('Trumpet', $bandVacancy['instruments']['name']);
        $genre = $this->db->genre->findOne(array('_id' => $bandVacancy['genres']['_id']));
        $instrument = $this->db->instrument->findOne(array('_id' => $bandVacancy['instruments']['_id']));
        $band = $this->db->association->findOne(array('_id' => $bandVacancy['bands']['_id']));
        $document =  $this->db->document->findOne(array('id' => 12 ));
        $this->assertEquals($document['_id']->{'$id'},$band['documents'][0]['_id']->{'$id'});
        $this->assertEquals(12, $band['id']);
        $this->assertEquals('Punk Rock', $genre['name']);
        $this->assertEquals('Trumpet', $instrument['name']);
        $this->assertEquals($bandVacancy['genres']['_id']->{'$id'},$genre['_id']->{'$id'});
        $this->assertEquals($bandVacancy['instruments']['_id']->{'$id'},$instrument['_id']->{'$id'});
        $this->assertEquals($genre['band_vacancies'][1],$bandVacancy['_id']->{'$id'});
        $this->assertEquals($instrument['band_vacancies'][0],$bandVacancy['_id']->{'$id'});
        $this->assertEquals(5,count($band['band_vacancies']));
        $band = $this->db->association->findOne(array('id' => 40 ));
        $this->assertEquals(1, count($band['band_vacancies']));
        $mongoId = new \MongoId($band['band_vacancies'][0]);
        $bandVacancyFromMongoId = $this->db->band_vacancy->findOne(array('_id' =>  $mongoId));
        $bandVacancyFromSqlId = $this->db->band_vacancy->findOne(array('id' => 35 ));
        $this->assertEquals($bandVacancyFromMongoId,$bandVacancyFromSqlId);

    }
}