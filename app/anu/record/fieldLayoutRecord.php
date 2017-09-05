<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class fieldLayoutRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "fieldLayout";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        return array(
            'id'              => array(AttributeType::Number),
            'field_id'        => array(AttributeType::Number),
            'record_id'       => array(AttributeType::Number)
        );

    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array_merge(array(
            'id'   => array(DBIndex::Primary)
        ), parent::defineIndex());
    }
}