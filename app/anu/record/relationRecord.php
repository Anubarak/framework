<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class relationRecord extends baseRecord
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
            'field_1'          => array(AttributeType::Mixed),
            'id_1'          => array(AttributeType::Number),
            'model1'          => array(AttributeType::Mixed),
            'field_2'          => array(AttributeType::Mixed),
            'id_2'          => array(AttributeType::Number),
            'model2'          => array(AttributeType::Mixed)
        );

    }

    /**
     * @param null $index
     * @return array|null
     */
    public function defineIndex($index = null){
        return array_merge(array(
            'id'   => array(DBIndex::Primary),
        ), parent::defineIndex());
    }
}