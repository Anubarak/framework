<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class boolField extends fieldService
{
    /**
     * @param $model        entryModel|baseModel|fieldModel
     * @param $attributes
     * @param $data
     * @param $key
     * @return mixed
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        $model->$key = (bool)$data;

        return $model;
    }


    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null)
    {
        if(property_exists($entry, $key)){
            $values[$key] = $entry->$key;
        }
    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values)
    {
        if (property_exists($entry, $key)) {
            $values[$key] = $entry->$key;
        }
    }

    public function onInstall($record, $field){
        anu()->database->alterTableAddColumn($record->tableName, $field->slug, " BOOLEAN NULL");
    }

}