<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class entryRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "";
    }

    /**
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        if($this->baseAttributes && !$baseAttributes){
            return $this->baseAttributes;
        }

        if($baseAttributes){
            $this->baseAttributes = $baseAttributes;
        }

        if(!$this->baseAttributes) {
            $this->baseAttributes = array_merge(array(
                'slug' => array(AttributeType::Mixed),
                'author_id' => array(AttributeType::Number),
                'enabled' => array(AttributeType::Bool),
                'entryType' => array(AttributeType::Mixed),
            ), parent::defineAttributes());
        }

        return $this->baseAttributes;
    }

    /**
     * @param $index null
     * @return array
     */
    public function defineIndex($index = null){
        if($this->index && !$index){
            return $this->index;
        }
        if(!$index){
            $index = array(
                'slug' => array(DBIndex::Unique)
            );
        }
        $this->index = $index;

        return $this->index;
    }
}