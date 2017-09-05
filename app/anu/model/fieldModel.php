<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:21
 */

namespace Anu;

use Exception;

class fieldModel extends baseModel
{
    public function defineAttributes()
    {
        return array_merge(array(
            'id'            => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'title'         => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Titel')),
            'slug'          => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Handle'), DBIndex::Unique => true),
            'createDate'    => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp, 'title' => Anu::t('Erstellungsdatum')),
            'updateDate'    => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp, 'title' => Anu::t('Bearbeitungsdatum')),
            'enabled'       => array(AttributeType::Bool, 'default' => '1', 'title' => Anu::t('Aktiv')),
            'settings'      => array(AttributeType::JSON, 'title' => Anu::t('Attributes'))
        ), parent::defineAttributes());
    }

    public function getUrl(){
        $className = Anu::getClassName($this);
        return BASE_URL . $className . "/" . $this->getAttribute('slug');
    }
}