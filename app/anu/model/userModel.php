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
            'first_name'        => array(AttributeType::Mixed),
            'last_name'         => array(AttributeType::Mixed),
            'email'             => array(AttributeType::Email, "required" => "true", "unique" => true, 'title' => Anu::t('E-mail')),
            'password'          => array(AttributeType::Password, "required" => "true", 'min_len' => 8, 'max_len' => 16),
            'repeatPassword'    => array(AttributeType::Password, 'min_len' => 8, 'max_len' => 16),
            'newPassword'       => array(AttributeType::NewPassword, 'min_len' => 8, 'max_len' => 16),
            'title'             => array(AttributeType::NewPassword, "unique" => true),

            'createDate'        => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp),
            'updateDate'        => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp),
            'enabled'           => array(AttributeType::Number, 'default' => '1'),
            'admin'             => array(AttributeType::Number, 'default' => '0'),
        );
    }
}