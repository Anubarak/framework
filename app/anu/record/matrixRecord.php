<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class matrixRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "matrix";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        return array(
            'matrix_id'         => array(AttributeType::Number),
            'page_id'           => array(AttributeType::Number),
            'position'          => array(AttributeType::Number),
            'content'           => array(AttributeType::Text),
            'createDate'        => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'updateDate'        => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
        );
    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array(
            'matrix_id' => array(DBIndex::Primary)
        );
    }
}