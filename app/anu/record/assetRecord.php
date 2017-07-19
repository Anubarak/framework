<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:54
 */

namespace Anu;

require_once __DIR__ . '\baseRecord.php';
class assetRecord extends baseRecord
{
    /**
     * Define Attributes
     */
    public function defineAttributes(){
        return array_merge(array(
            'asset_id'                  => array(AttributeType::Number),
            'path'                      => array(AttributeType::Mixed),
            'name'                      => array(AttributeType::Mixed),
            'enabled'       => array(AttributeType::Number, 'default' => '1'),
        ), parent::defineAttributes());
    }

    public function defineIndex(){
        return array_merge(array(
            'asset_id'   => array('primary_key')
        ), parent::defineIndex());
    }

    /**
     * @return string
     */
    public function getTableName(){
        return 'assets';
    }

}