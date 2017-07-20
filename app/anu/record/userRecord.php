<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class userRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "users";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        return array_merge(array(
            'user_id'           => array(AttributeType::Number),
            'first_name'        => array(AttributeType::Mixed),
            'last_name'         => array(AttributeType::Mixed),
            'email'             => array(AttributeType::Mixed),
            'enabled'           => array(AttributeType::Number, 'default' => '1'),
            'admin'             => array(AttributeType::Number, 'default' => '0'),
            'password'          => array(AttributeType::Mixed),
        ), parent::defineAttributes());

    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array_merge(array(
            'user_id'   => array(DBIndex::Primary),
            'email'     => array(DBIndex::Unique),
            'title'     => array(DBIndex::Unique),
        ), parent::defineIndex());
    }
}