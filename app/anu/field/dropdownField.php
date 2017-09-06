<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class dropdownField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        $model->$key = "";
    }


    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null)
    {

    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values)
    {

    }

}