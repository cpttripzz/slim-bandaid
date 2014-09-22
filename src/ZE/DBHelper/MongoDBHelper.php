<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 20/09/14
 * Time: 21:56
 */

namespace ZE\DBHelper;


class MongoDBHelper
{
    protected $db;
    protected $mongoIdsMap;

    /**
     * @return mixed
     */
    public function getMongoIdsMap()
    {
        return $this->mongoIdsMap;
    }

    /**
     * @param mixed $mongoIdsMap
     */
    public function setMongoIdsMap($mongoIdsMap)
    {
        $this->mongoIdsMap = $mongoIdsMap;
    }

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getMongoIdsByValue($table, $column, $values)
    {
        $arrValues = (is_array($values)) ? $values : explode(',', $values);
        $arrIds = array();
        foreach ($arrValues as $id) {
            $query = array($column => (int)$id);
            $document = $this->db->$table->findOne($query);
            $arrIds[] = $document['_id']->{'$id'};
        }
        return $arrIds;
    }

    public function saveRow($row, $table, $dualReference = true,$mongoIdsMap=null, $query=null,$options=array())
    {
        $dualRefMap = array();

        if(!$mongoIdsMap){
            $mongoIdsMap = $this->mongoIdsMap;
        }
        foreach ($row as $column => &$colValue) {
            if (isset($options['embed']['columns'][$column])){
                if(!empty($row[$column])) {
                    $innerQuery = array($options['embed']['columns'][$column]['reference_table_id'] => (int)$row[$column]);
                    $document = $this->db->$options['embed']['columns'][$column]['reference_table']->findOne($innerQuery);
                    if (isset($options['embed']['columns'][$column]['columns_to_embed'])) {
                        if ($options['embed']['columns'][$column]['dual_reference']) {
                            $dualRefMap[$options['embed']['columns'][$column]['reference_table']][] = $document['_id']->{'$id'};
                        }
                        $document = array_intersect_key($document, array_flip($options['embed']['columns'][$column]['columns_to_embed']));
                    }
                    $row[$options['embed']['columns'][$column]['reference_table']] = $document;
                }
                unset($row[$column]);

            } elseif (isset($mongoIdsMap[$column])) {
                $mongoIds = $this->getMongoIdsByValue($mongoIdsMap[$column], 'id', $colValue);
                unset($row[$column]);
                $row[ empty($options['dual_reference_field'] ) ? $mongoIdsMap[$column] : $options['dual_reference_field'] ] = array_values($mongoIds);
                foreach($mongoIds as $strMongoId){
                    if (!empty($options['dual_reference_ref_field'])){
                        $dualRefMap[$mongoIdsMap[$column]]['dual_reference_ref_field'] = $options['dual_reference_ref_field'];
                    }
                    $dualRefMap[$mongoIdsMap[$column]][] = $strMongoId;
                }
            }
        }
        try {
            if(empty($query)){
                $query = $row;
            }
            $newDocument = $this->db->$table->findAndModify($query, $row, null, array('new' => true, 'upsert' => true));
            if ($dualReference) {
                $dualReferenceRefField = false;
                foreach ($dualRefMap as $refTable => $mongoIds) {
                    if(!empty($mongoIds['dual_reference_ref_field'])) {
                        $dualReferenceRefField = $mongoIds['dual_reference_ref_field'];
                        unset($mongoIds['dual_reference_ref_field']);
                    }
                    foreach($mongoIds as $strMongoId){
                        $mongoId = new \MongoId($strMongoId);
                        $query = array('_id' => $mongoId);
                        $refs = $this->db->$refTable->findOne($query);
                        if($dualReferenceRefField){
                            $refs = isset($refs[$dualReferenceRefField]) ? $refs[$dualReferenceRefField] : array();
                        } else {
                            $refs = isset($refs[$table . 's']) ? $refs[$table . 's'] : array();
                        }
                        $flippedRefs = array_flip($refs);
                        if(empty($flippedRefs[$newDocument['_id']->{'$id'}])) {
                            $refs[] = $newDocument['_id']->{'$id'};
                        }
                        $this->db->$refTable->update(
                            $query, array('$set' => array( $dualReferenceRefField ? $dualReferenceRefField : $table . 's' => $refs))
                        );
                    }

                }
            }

        } catch
        (\Exception $e) {
            echo($e->getMessage());
        }
    }

    public function saveJoinTableReferences($joinTableMapping,$rows)
    {
        $tableReferences = array();
        $dualReference= isset($joinTableMapping['dual_reference'])  ? $joinTableMapping['dual_reference'] : false;
        foreach($rows as $row){
            $tableReferences[$row[$joinTableMapping['update_table_id']] ][] = $row[$joinTableMapping['reference_table_id']];
        }
        foreach($tableReferences as $updateTableIdValue => $referenceTableIds){
            $updateMongoId = $this->getMongoIdsByValue($joinTableMapping['update_table'], 'id', $updateTableIdValue);
            $mongoId = new \MongoId(reset($updateMongoId));
            $query = array('_id' => $mongoId);
            $row = $this->db->$joinTableMapping['update_table']->findOne($query);
            $row[$joinTableMapping['reference_table_id'] ] = $referenceTableIds;
            $mongoIdsMap = array($joinTableMapping['reference_table_id'] => $joinTableMapping['reference_table']);
            $this->saveRow($row, $joinTableMapping['update_table'], $dualReference,$mongoIdsMap, $query, $joinTableMapping);
        }
    }
} 