<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class textField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        $model->$key = $data;

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
        anu()->database->alterTableAddColumn($record->tableName, $field->slug, "text NULL default ''");
    }

}