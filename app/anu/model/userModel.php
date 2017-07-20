<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 10:39
 */

namespace Anu;


class userModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'user_id'           => array(AttributeType::Number),
            'first_name'        => array(AttributeType::Mixed, "required" => "true"),
            'last_name'         => array(AttributeType::Mixed, "required" => "true"),
            'email'             => array(AttributeType::Email, "required" => "true"),
            'password'          => array(AttributeType::Password, "required" => "true"),
            'newPassword'       => array(AttributeType::NewPassword, "ignoreInDatabase" => true),

            'createDate'        => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp),
            'updateDate'        => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp),
            'enabled'           => array(AttributeType::Number, 'default' => '1'),
            'admin'             => array(AttributeType::Number, 'default' => '0'),
        );
    }
}