<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 12.07.2017
 * Time: 09:29
 */

namespace Craft;


use Whoops\Exception\ErrorException;

class elementCriteriaModel
{
    private $attributes = null;
    private $defaultAttributes = array(
        'limit'     => 100,
        'offset'    => 0,
    );

    private $service = null;

    /**
     * elementCriteriaModel constructor.
     * @param $service entryService
     * @throws \Exception
     */
    public function __construct($service)
    {
        if(!$service || !is_object($service)){
            throw new \Exception("Parameter must be kind of Service");
        }
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function find($attributes = null){

        if($attributes){
            $this->attributes = $attributes;
        }else{
            $this->attributes = getPublicObjectVars($this);
        }
        $tables = $this->service->getTable();
        if(!$tables){
            $tables = craft()->record->getAllRecords();
        }elseif(!is_array($tables)){
            $tables = array($tables);
        }

        $where = array();
        foreach ($this->attributes as $k => $v){
            $where[$k] = $v;
        }
        $entries = array();
        foreach ($tables as $record){
            $className = $record['name'];
            $rows = craft()->database->select($record['table_name'], craft()->$className->getPrimaryKey(), $where);
            craft()->database->debugError();

            if($rows){
                foreach ($rows as $row){
                    $entries[] = craft()->$className->getEntryById((int)$row);
                }
            }
        }

        return $entries;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    private function stripAttribute($key){
        $value = null;
        if(is_array($this->attributes) && count($this->attributes)){
            if(isset($this->attributes[$key])){
                $value = $this->attributes[$key];
                unset($this->attributes[$key]);
            }
        }

        //try to get default value
        if(!$value){
            if(isset($this->defaultAttributes[$key])){
                $value = $this->defaultAttributes[$key];
            }
        }
        return $value;
    }

}

function getPublicObjectVars($obj) {
    return get_object_vars($obj);
}