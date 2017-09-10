<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class jsonField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        $jsonData = json_decode($data, true);
        if(json_last_error() == JSON_ERROR_NONE){
            if(is_array($jsonData) && count($jsonData)){
                foreach ($jsonData as $jsonKey => $json){
                    if(property_exists($model, $jsonKey)){
                        //throw new \Exception($jsonKey . ' is a reserved key an must not be used as an index for matrixcontent');
                    }else{
                        $model->$jsonKey = $json;
                    }
                }
            }
        }else{
            //throw new \Exception("could not json_decode data for entry with class ".$model->class. " and id " . $model->id);
        }

        $model->$key = $jsonData;
    }


    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null)
    {
        if(property_exists($entry, $key)){
            $values[$key] = json_encode($entry->$key);
        }
    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values)
    {
        if (property_exists($entry, $key)) {
            $values[$key] = json_encode($entry->$key);
        }
    }

    public function onInstall($record, $field){
        anu()->database->alterTableAddColumn($record->tableName, $field->slug, "text NULL default ''");
    }

}