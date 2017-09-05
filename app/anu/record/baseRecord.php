<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class baseRecord
{
    public $name = '';
    public $tableName = '';
    public $primary_key;

    /**
     * Define aliases for javascript and such
     *
     * baseRecord constructor.
     */
    public function __construct($record = array()){
        $this->name = (isset($record['name']))? $record['name'] : $this->getRecordName();
        $this->tableName = (isset($record['table_name']))? $record['table_name'] : $this->getTableName();
        $this->installed = (isset($record['name']))? true : $this->isInstalled();
        $this->primary_key = (isset($record['primary_key']))? $record['primary_key'] : $this->getPrimaryKey();
    }

    /**
     * @return string
     */
    public function getTableName(){
        return "";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        $baseAttributes = array(
            'createDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'updateDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'title'         => array(AttributeType::Mixed),
        );
        return array_merge($baseAttributes, anu()->field->getAllFieldsForEntry($this->tableName));

    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array(

        );
    }

    /**
     * @return int|string
     */
    public function getPrimaryKey(){
        $indexes = $this->defineIndex();
        foreach ($indexes as $k  => $v){
            if(in_array(DBIndex::Primary, $v)){
                return $k;
            }
        }
    }

    public function getRecordName(){
        return '';
    }

    public function isInstalled(){
        return anu()->record->isRecordInstalled($this);
    }
}