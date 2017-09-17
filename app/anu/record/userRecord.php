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
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array_merge(array(
            'user_id'           => array(AttributeType::Number),
            'first_name'        => array(AttributeType::Mixed),
            'last_name'         => array(AttributeType::Mixed),
            'email'             => array(AttributeType::Mixed),
            'enabled'           => array(AttributeType::Number, 'default' => '1'),
            'admin'             => array(AttributeType::Number, 'default' => '0'),
            'password'          => array(AttributeType::Mixed)
        ), parent::defineAttributes());

    }

    /**
     * @param null $index
     * @return array|null
     */
    public function defineIndex($index = null){
        return array_merge(array(
            'user_id'   => array(DBIndex::Primary),
            'email'     => array(DBIndex::Unique),
            'title'     => array(DBIndex::Unique),
        ), parent::defineIndex());
    }

    public function getModel()
    {
        return "user";
    }
}