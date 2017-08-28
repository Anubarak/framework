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
            'position'          => array(AttributeType::Number, "relatedField" => 'page_id'),
            'content'           => array(AttributeType::JSON),
            'type'              => array(AttributeType::Text),
            'createDate'        => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp),
            'updateDate'        => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp),
            'enabled'           => array(AttributeType::Bool, 'default' => '1', 'title' => Anu::t('Aktiv'))
        );
    }

    public function getUrl(){
        $className = Anu::getClassName($this);
        $url = BASE_URL . $className . "/" . $this->getAttribute('slug');
        return $url;
    }
}