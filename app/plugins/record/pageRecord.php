<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:54
 */

namespace Anu;


class pageRecord extends baseRecord
{

    /**
     * Define Attributes
     */
    public function defineAttributes(){
        return array_merge(array(
            'page_id'   => array(AttributeType::Number ),
            'linkName'  => array(AttributeType::Mixed),
        ), parent::defineAttributes());
    }

    public function defineIndex(){
        return array_merge(array(
            'page_id'   => array('primary_key')
        ), parent::defineIndex());
    }

    /**
     * @return string
     */
    public function getTableName(){
        return 'page';
    }

}