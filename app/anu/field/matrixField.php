<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class matrixField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        $model->$key = anu()->field->getBaseCriteriaModelForPopulatedEntry($model, $data, array(
            'model' => 'matrix',
            'class' => $model->class
        ), $key);
    }


    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null)
    {
        if(property_exists($entry, $key)){
            if(!isset($attributes['1'])){
                throw new \Exception("Error: missing related Matrix Attribute in " . $entry->class . "Service");
            }
            //$matrix = anu()->matrix->getMatrixByName($value[1]);
            //array of matrixes...
            $oldIds = array();
            if($oldEntry && property_exists($oldEntry, $key)){
                $oldIds = $oldEntry->$key->ids();
            }
            $matrixIds = array();
            $i = 0;
            if(is_array($entry->$key) && count($entry->$key)){
                foreach ($entry->$key as $matrix){
                    $matrix->position = $i;
                    if($id = anu()->matrix->saveEntry($matrix)){
                        $matrixIds[] = $id;
                    }
                    $i++;
                }
            }

            if($max = count($oldIds)){
                $idsToDelete = array();
                for($i = 0; $i < $max; $i++){
                    if(!in_array($oldIds[$i], $matrixIds)){
                        $idsToDelete[] = $oldIds[$i];
                    }
                }
                if(count($idsToDelete)){
                    anu()->database->delete('matrix', array(
                        'matrix_id' => $idsToDelete
                    ));
                }
            }

            $relation = array(
                'table' => 'matrix',
                'field' => 'matrix_id',
                'model' => 'matrix',
                'class' => $entry->class
            );
            anu()->entry->updateRelations($entry, $key, $relation, $matrixIds);

        }
    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values)
    {
        if(property_exists($entry, $key)){
            if(!isset($attributes['1'])){
                throw new \Exception("Error: missing related Matrix Attribute in " . $entry->class . "Service");
            }
            //$matrix = anu()->matrix->getMatrixByName($value[1]);
            //array of matrixes...
            $matrixIds = array();
            $i = 0;
            $relation = array(
                'table' => 'matrix',
                'field' => 'matrix_id',
                'model' => 'matrix',
                'class' => $entry->class
            );
            if(is_array($entry->$key) && count($entry->$key)){
                foreach ($entry->$key as $matrix){
                    $matrix->position = $i;
                    if($id = anu()->matrix->saveEntry($matrix)){
                        $matrixIds[] = $id;
                        $relationsToSave[] = anu()->entry->getRelationData($relation, $key, $id);
                    }
                    $i++;
                }
            }
        }
    }

}