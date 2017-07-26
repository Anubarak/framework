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
     * @return array
     */
    public function defineAttributes(){
        return array(
            'id'            => array(AttributeType::Number),
            'name'          => array(AttributeType::Mixed),
            'table_name'    => array(AttributeType::Mixed),
            'date'          => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
        );

    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array_merge(array(
            'id'   => array(DBIndex::Primary),
        ), parent::defineIndex());
    }
}