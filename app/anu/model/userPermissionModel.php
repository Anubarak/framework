<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 10:39
 */

namespace Anu;


class userPermissionModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'permission_id'        => array(AttributeType::Mixed),
            'user_id'           => array(AttributeType::Number, "required" => "true"),
            'permission'           => array(AttributeType::Mixed, "required" => "true"),
        );
    }
}