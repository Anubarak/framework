<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:21
 */

namespace Anu;


class matrixModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'page_id'           => array(AttributeType::Number),
            'position'          => array(AttributeType::Position, "relatedField" => 'page_id'),
            'content'           => array(AttributeType::Text),
            'createDate'        => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp),
            'updateDate'        => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp),
        );
    }

    public function getUrl(){
        $className = Anu::getClassName($this);
        $url = BASE_URL . $className . "/" . $this->getAttribute('slug');
        return $url;
    }
}