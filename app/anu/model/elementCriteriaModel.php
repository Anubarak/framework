<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 12.07.2017
 * Time: 09:29
 */

namespace Anu;


use Whoops\Exception\ErrorException;

class elementCriteriaModel implements \IteratorAggregate, \JsonSerializable
{
    private $attributes = null;
    private $defaultAttributes = array(
        'limit'     => 100,
        'offset'    => 0,
    );

    private $ids = array();
    private $titles = array();

    private $service = null;
    private $class = null;

    /**
     * @return array
     */
    public function jsonSerialize() {
        $this->class = Anu::getClassName($this);
        return get_object_vars($this);
    }

    /**
     * elementCriteriaModel constructor.
     * @param $service entryService|baseService
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
    public function find($attributes = null, $justIds = null){
        if($attributes){
            $this->attributes = $attributes;
        }else{
            $this->attributes = getPublicObjectVars($this);
        }

        $tables = $this->service->getTable();
        if(!$tables){
            $tables = anu()->record->getAllRecords();
        }elseif(!is_array($tables)){
            $tables = array(array(
                'table_name' => $tables,
                'name'       => Anu::getClassName($this->service)
            ));
        }

        $where = array('enabled' => '1');
        $join = array();
        $relations = $this->stripAttribute('relatedTo');

        foreach ($this->attributes as $k => $v){
            $where[$k] = $v;
        }

        $entries = array();
        foreach ($tables as $record){
            $className = $record['name'];
            //check for relations...
            $select = array();
            $select[] = anu()->$className->getPrimaryKey() . '(id)';
            if($relations){
                $join = array(
                    '[>]relation(relation1)' => array(
                        anu()->$className->getPrimaryKey() => 'id_1'
                    ),
                    '[>]relation(relation2)' => array(
                        anu()->$className->getPrimaryKey() => 'id_2'
                    )
                );
                $whereFirstTable = array();
                $whereSecondTable = array();
                $whereThirdTable = array();
                $whereFourthTable = array();
                $id = null;
                $field = null;
                $model = null;
                if(is_object($relations)){
                    $id = $relations->id;
                    $model = Anu::getClassName($relations);
                }else{
                    $field = isset($relations['field'])? $relations['field'] : null;
                    $id = array_key_exists("id", $relations)? $relations['id'] : 0;
                    $model = isset($relations['model'])? $relations['model'] : null;
                }

                //if id of entry or index == null return nothing since there are no relations if there is no id
                if($id === null){
                    return array();
                }

                if($className == $model ){
                    $where[anu()->$className->getTable() . '.' . anu()->$className->getPrimaryKey() . '[!]'] = $id;
                }

                if($field){
                    $whereFirstTable['relation1.field_1'] = $field;
                    $whereSecondTable['relation2.field_2'] = $field;
                    $whereThirdTable['relation1.field_2'] = $field;
                    $whereFourthTable['relation2.field_1'] = $field;
                }

                if($id){
                    $whereFirstTable['relation1.id_1'] = $id;
                    $whereSecondTable['relation2.id_2'] = $id;
                    $whereThirdTable['relation1.id_2'] = $id;
                    $whereFourthTable['relation2.id_1'] = $id;
                }

                if($model){
                    $whereFirstTable['relation1.model_1'] = $model;
                    $whereSecondTable['relation2.model_2'] = $model;
                    $whereThirdTable['relation1.model_2'] = $model;
                    $whereFourthTable['relation2.model_1'] = $model;
                }

                $where['OR # joins'] = array(
                    'AND # first' => $whereFirstTable,
                    'AND # second' => $whereSecondTable,
                    'AND # third' => $whereThirdTable,
                    'AND # fourth' => $whereFourthTable
                );
            }

            if($join){
                $rows = anu()->database->select(anu()->$className->getTable(), $join, $select , $where);
            }else{
                $rows = anu()->database->select(anu()->$className->getTable(), $select , $where);
            }

            $rows = array_unique($rows,SORT_REGULAR);
            //anu()->database->debugError();
            if($rows){
                foreach ($rows as $row){
                    if(!$justIds){
                        if(method_exists(anu()->$className, "getEntryById")){
                            $entries[] = anu()->$className->getEntryById((int)$row['id']);
                        }elseif(method_exists(anu()->$className, "getUserById")){
                            $entries[] = anu()->$className->getUserById((int)$row['id']);
                        }elseif (method_exists(anu()->$className, "getElementById")){
                            $entries[] = anu()->$className->getElementById((int)$row['id']);
                        }
                    }else{
                        $entries[] = $row['id'];
                    }
                }
            }
        }

        return $entries;
    }

    /**
     * @return array
     */
    public function ids($attributes = null){
        return $this->find($attributes, true);
    }

    /**
     * Get First found Element from CriteriaModel
     *
     * @return entryModel
     */
    public function first(){
        $limit = $this->LIMIT;
        $this->LIMIT = 1;
        $element = $this->find();
        $this->LIMIT = $limit;
        return $element[0];
    }

    public function count(){
        return count($this->ids());
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

    /**
     * Returns an iterator for traversing over the elements.
     *
     * Required by the IteratorAggregate interface.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->find());
    }


    /**
     * @param $ids
     */
    public function storeIds($ids){
        $this->ids = array_values($ids);
    }

    /**
     * @return array
     */
    public function getStoredIds(){
        return $this->ids;
    }

    /**
     *  save titles for javascript
     */
    public function storeTitles(){
        if(!$this->service){
            return null;
        }

        if(is_array($this->ids) && count($this->ids)){
            $this->titles = anu()->database->select($this->service->getTable(), 'title', array(
                $this->service->getPrimaryKey() => $this->ids,
                'ORDER' => array(
                    $this->service->getPrimaryKey() => $this->ids
                )
            ));

            return true;
        }
        return false;
    }

}

function getPublicObjectVars($obj) {
    return get_object_vars($obj);
}