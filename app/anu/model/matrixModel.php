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
            'position'          => array(AttributeType::Number),
            'content'           => array(AttributeType::JSON),
            'type'              => array(AttributeType::Text),
            'handle'            => array(AttributeType::Text),
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


    /**
     * Returns all Data of the model
     *
     * @return array
     */
    public function getData(){
        $values = array();
        $attributes = $this->defineAttributes();
        if(property_exists($this, 'handle') && property_exists($this, 'type')){
            $matrixAttributes = anu()->matrix->getMatrixByName($this->handle)->defineAttributes()[$this->type];
            $attributes = array_merge($attributes, $matrixAttributes);
        }

        foreach ($attributes as $k => $v){
            if(property_exists($this, $k)){
                $values[$k] = $this->$k;
            }
        }
        return $values;
    }
}