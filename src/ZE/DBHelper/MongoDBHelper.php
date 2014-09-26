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
    protected $columnsModifier;

    /**
     * @return mixed
     */
    public function getColumnsModifier()
    {
        return $this->columnsModifier;
    }

    /**
     * @param mixed $columnsModifier
     */
    public function setColumnsModifier($columnsModifier)
    {
        $this->columnsModifier = $columnsModifier;
    }
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

    public function getModifiedColumn($type,$value)
    {
        $returnValue = null;
        switch ($type){
            case 'MongoDate':
                if ($value instanceof \MongoDate){
                    return $value;
                }
                $returnValue = new \MongoDate(strtotime($value));
        }
        return $returnValue;
    }
    public function saveRow($row, $table, $dualReference = true, $mongoIdsMap = null, $query = null, $options = array())
    {
        $dualRefMap = array();

        if (!$mongoIdsMap) {
            $mongoIdsMap = $this->mongoIdsMap;
        }
        foreach ($row as $column => &$colValue) {
            $altTableName = false;
            if (isset($this->columnsModifier[$column])) {
                $row[$column] = $this->getModifiedColumn($this->columnsModifier[$column],$colValue);
            } elseif (isset($options['embed']['columns'][$column])) {
                if (!empty($row[$column])) {
                    $columns = array();
                    if (!is_array($row[$column])) {
                        $columns = array($row[$column]);
                    } else {
                        $columns = $row[$column];
                    }
//                    $rangeQuery = array('x' => array( '$gt' => 5, '$lt' => 20 ));
                    $innerQuery = array(
                        $options['embed']['columns'][$column]['reference_table_id'] => array(
                            '$in' => $columns
                        )
                    );
                    $documents = $this->db->$options['embed']['columns'][$column]['reference_table']->find($innerQuery);
                    $documents = iterator_to_array($documents, true);
                    if ($options['embed']['columns'][$column]['dual_reference']) {
                        $docIds = array();
                        foreach ($documents as $doc) {
                            $docIds[] = $doc['_id']->{'$id'};
                        }

                        $dualRefMap[$options['embed']['columns'][$column]['reference_table']][] = $docIds;
                        $dualRefMap[$options['embed']['columns'][$column]['reference_table']]['dual_reference_ref_field'] = $options['embed']['columns'][$column]['dual_reference_ref_field'];
                        if (!empty($options['embed']['columns'][$column]['ref_columns_to_embed'])) {
                            $dualRefMap[$options['embed']['columns'][$column]['reference_table']]['ref_columns_to_embed'] = $options['embed']['columns'][$column]['ref_columns_to_embed'];
                        }
                        $altTableName = $options['embed']['columns'][$column]['dual_reference_field'];

                    }
                    if (isset($options['embed']['columns'][$column]['columns_to_embed'])) {
                        $columnsToEmbed = array_flip($options['embed']['columns'][$column]['columns_to_embed']);
                        array_walk($documents, function (&$value, $key) use ($columnsToEmbed) {
                            $value = array_intersect_key($value, $columnsToEmbed);
                        });
                    }
                    $row[$altTableName ? $altTableName : $options['embed']['columns'][$column]['reference_table']] = $documents;
                }
                unset($row[$column]);

            } elseif (isset($mongoIdsMap[$column])) {
                $arrMongoIds = $this->getMongoIdsByValue($mongoIdsMap[$column], 'id', $colValue);
                unset($row[$column]);
                $row[empty($options['dual_reference_field']) ? $mongoIdsMap[$column] : $options['dual_reference_field']] = array_values($arrMongoIds);
                foreach ($arrMongoIds as $strMongoId) {
                    if (!empty($options['dual_reference_ref_field'])) {
                        $dualRefMap[$mongoIdsMap[$column]]['dual_reference_ref_field'] = $options['dual_reference_ref_field'];
                    }
                    $dualRefMap[$mongoIdsMap[$column]][] = $strMongoId;
                }
            }
        }
        try {
            if (empty($query)) {
                $query = $row;
            }
            $newDocument = $this->db->$table->findAndModify($query, $row, null, array('new' => true, 'upsert' => true));
            if ($dualReference) {
                $dualReferenceRefField = false;
                $dualReferenceFields = false;
                foreach ($dualRefMap as $refTable => $mongoIds) {
                    if (!empty($mongoIds['dual_reference_ref_field'])) {
                        $dualReferenceRefField = $mongoIds['dual_reference_ref_field'];
                        unset($mongoIds['dual_reference_ref_field']);
                    }
                    if (!empty($mongoIds['ref_columns_to_embed'])) {
                        $dualReferenceFields = $mongoIds['ref_columns_to_embed'];
                        unset($mongoIds['ref_columns_to_embed']);
                    }
                    foreach ($mongoIds as $id) {
                        if (!is_array($id)) {
                            $id = array($id);
                        }
                        foreach ($id as $refId) {
                            $mongoId = new \MongoId($refId);
                            $query = array('_id' => $mongoId);

                            if (!$dualReferenceFields) {
                                $dualReferenceFields = array('_id');
                            }


                            $refDoc = array_intersect_key($newDocument, array_flip($dualReferenceFields));
                            $this->db->$refTable->update(
                                $query, array('$addToSet' =>
                                    array($dualReferenceRefField ? $dualReferenceRefField : $table . 's' => $refDoc))
                            );


                        }
                    }

                }
            }

        } catch
        (\Exception $e) {
            echo($e->getMessage());
        }
    }

    public function saveJoinTableReferences($joinTableMapping, $rows)
    {
        $tableReferences = array();
        $dualReference = isset($joinTableMapping['dual_reference']) ? $joinTableMapping['dual_reference'] : false;
        foreach ($rows as $row) {
            $tableReferences[$row[$joinTableMapping['update_table_id']]][] = $row[$joinTableMapping['reference_table_id']];
        }
        foreach ($tableReferences as $updateTableIdValue => $referenceTableIds) {
            $updateMongoId = $this->getMongoIdsByValue($joinTableMapping['update_table'], 'id', $updateTableIdValue);
            $mongoId = new \MongoId(reset($updateMongoId));
            $query = array('_id' => $mongoId);
            $row = $this->db->$joinTableMapping['update_table']->findOne($query);
            $row[$joinTableMapping['reference_table_id']] = $referenceTableIds;
            $mongoIdsMap = array($joinTableMapping['reference_table_id'] => $joinTableMapping['reference_table']);
            $this->saveRow($row, $joinTableMapping['update_table'], $dualReference, $mongoIdsMap, $query, $joinTableMapping);
        }
    }
} 