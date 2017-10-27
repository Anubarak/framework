<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:21
 */

namespace Anu;

use Exception;

class entryModel extends baseModel {
    public $class = '';

    public function __construct($handle){
        $this->class = $handle;
        $this->attributes= null;
    }


    public $attributes = null;

    public function defineAttributes(){
        if(!$this->attributes){
            $baseAttributes = $this->baseAttributes();

            $className = $this->class;

            $merged = array_merge($baseAttributes, anu()->$className->getFieldsForEntry($this));
            $parent = parent::defineAttributes();
            $this->attributes = array_merge($parent, $merged);
        }

        return $this->attributes;
    }

    public function getUrl(){
        $className = Anu::getClassName($this);
        return BASE_URL . $className . "/" . $this->getAttribute('slug');
    }

    public function defineStructure(){
        return StructureType::Channel;
    }

    public function baseAttributes(){
        $entryTypes = anu()->record->getEntryTypesForRecord($this->class);
        $options = array();
        foreach($entryTypes as $entryType){
            $options[] = array(
                'id' => $entryType['handle'],
                'label' => $entryType['label']
            );
        }

        $attributes = array(
            'title' => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Titel')),
            'slug' => array(AttributeType::Mixed, 'required' => true, 'title' => Anu::t('Slug'), DBIndex::Unique => true),
            'createDate' => array(AttributeType::DateTime, 'default' => Defaults::creationTimestamp, 'title' => Anu::t('Erstellungsdatum')),
            'updateDate' => array(AttributeType::DateTime, 'default' => Defaults::currentTimestamp, 'title' => Anu::t('Bearbeitungsdatum')),
            'enabled' => array(AttributeType::Bool, 'default' => '1', 'title' => Anu::t('Aktiv')),
            'author_id' => array(AttributeType::Hidden, 'default' => Defaults::currentUserId, 'title' => Anu::t('Author')),
            'entryType' => array(AttributeType::DropDown, 'title' => Anu::t('Eintragstyp'),
                'options' => $options
            ),
        );

        return $attributes;
    }
}