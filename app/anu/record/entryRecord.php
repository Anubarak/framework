<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class entryRecord extends baseRecord
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
        return array_merge(array(
            'slug'          => array(AttributeType::Mixed),
        ), parent::defineAttributes());

    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array(
            'slug' => array(DBIndex::Unique)
        );
    }
}