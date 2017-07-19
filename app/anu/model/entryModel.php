<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:21
 */

namespace Anu;

use Exception;

class entryModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'createDate'    => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp),
            'updateDate'    => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp),
            'enabled'       => array(AttributeType::Number, 'default' => '1'),
            'title'         => array(AttributeType::Mixed, 'required' => true),
            'slug'          => array(AttributeType::Mixed, 'required' => true),
            'author_id'     => array(AttributeType::Number, 'default' => Defaults::currentUserId)
        );
    }

    public function getUrl(){
        $className = Anu::getClassName($this);
        $url = BASE_URL . $className . "/" . $this->getAttribute('slug');
        return $url;
    }
}