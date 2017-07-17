<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        return array(
            'createDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'updateDate'   => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'slug'          => array(AttributeType::Mixed),
            'title'          => array(AttributeType::Mixed),
        );
    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array(
            'slug' => array('unique')
        );
    }
}