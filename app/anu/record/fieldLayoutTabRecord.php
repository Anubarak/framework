<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class fieldLayoutTabRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "fieldlayouttabs";
    }

    /**
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array(
            'id'                => array(AttributeType::Number),
            'handle'            => array(AttributeType::Mixed),
            'label'             => array(AttributeType::Mixed),
            'position'          => array(AttributeType::Number)
        );

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