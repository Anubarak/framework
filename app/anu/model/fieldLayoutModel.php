<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:21
 */

namespace Anu;

class fieldLayoutModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'id'            => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'fieldHandle'      => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Feld Id')),
            'recordHandle'     => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Record Id')),
            'tabHandle'     => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Tab Name'))
        );
    }

    public function getUrl(){
        $className = Anu::getClassName($this);
        return BASE_URL . $className . "/" . $this->getAttribute('slug');
    }
}