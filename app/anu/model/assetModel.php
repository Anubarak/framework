<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 10:39
 */

namespace Anu;


class assetModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'asset_id'                  => array(AttributeType::Number),
            'path'                      => array(AttributeType::File, 'folder' => '/upload'),
            'name'                      => array(AttributeType::Mixed),
            'createDate'    => array(AttributeType::DateTime, 'default' => 'creationTimestamp'),
            'updateDate'    => array(AttributeType::DateTime, 'default' => 'currentTimestamp'),
            /*'enabled'       => array(AttributeType::Number, 'default' => '1'),*/
            'title'         => array(AttributeType::Mixed, 'required' => true)
        );
    }

    public function getUrl(){
        return BASE_URL . '?asset=2';
    }
}