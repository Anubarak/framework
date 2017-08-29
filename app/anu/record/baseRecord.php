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

    /**
     * Define aliases for javascript and such
     *
     * baseRecord constructor.
     */
    public function __construct(){
        $this->name = $this->getRecordName();
        $this->tableName = $this->getTableName();
        $this->installed = $this->isInstalled();
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
        return array(
            'createDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'updateDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'title'         => array(AttributeType::Mixed),
        );
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