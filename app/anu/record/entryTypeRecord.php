<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class entryTypeRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "entryTypes";
    }

    /**
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array(
            'id'                 => array(AttributeType::Number),
            'recordHandle'       => array(AttributeType::Mixed),
            'entryType'          => array(AttributeType::Mixed)
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