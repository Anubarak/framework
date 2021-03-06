<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class recordsRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "records";
    }

    /**
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array(
            'id'            => array(AttributeType::Number),
            'name'          => array(AttributeType::Mixed),
            'table_name'    => array(AttributeType::Mixed),
            'primary_key'   => array(AttributeType::Mixed),
            'model'         => array(AttributeType::Mixed),
            'structure'     => array(AttributeType::Mixed),
            'date'          => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
        );

    }

    /**
     * @param index null
     * @return array
     */
    public function defineIndex($index = null){
        return array_merge(array(
            'id'   => array(DBIndex::Primary),
        ), parent::defineIndex());
    }
}