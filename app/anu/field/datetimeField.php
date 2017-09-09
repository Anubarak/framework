<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class datetimeField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        $UTC = new \DateTimeZone("UTC");
        $date = new \DateTime( $data );
        $model->$key = $date->format('Y-m-d H:i:s');
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
        anu()->database->alterTableAddColumn($record->tableName, $field->slug, "DATETIME NULL DEFAULT CURRENT_TIMESTAMP");
    }

}