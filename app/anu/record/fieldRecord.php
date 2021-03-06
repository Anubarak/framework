<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class fieldRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "fields";
    }

    public function getRecordName(){
        return 'field';
    }

    public function getModel(){
        return "field";
    }

    /**
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array_merge(array(
            'id'                => array(AttributeType::Number),
            'slug'              => array(AttributeType::Mixed),
            'fieldType'         => array(AttributeType::Mixed),
            'settings'          => array(AttributeType::Text),
            'enabled'           => array(AttributeType::Bool),
        ), parent::defineAttributes());

    }

    /**
     * @param index null
     * @return array
     */
    public function defineIndex($index = null){
        return array_merge(array(
            'id'   => array(DBIndex::Primary)
        ), parent::defineIndex());
    }
}