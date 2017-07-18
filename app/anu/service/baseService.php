<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 11:10
 */

namespace Anu;


class baseService
{
    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;

    /**
     * @param $entry            entryModel
     */
    protected function defineDefaultValues(&$entry){
        $data = $entry->getData();
        $defaults = $entry->defineAttributes();
        foreach ($defaults as $k => $v){
            if(isset($v['default']) && $v['default'] && array_key_exists($k, $data)){
                $default = $v['default'];
                switch ($default){
                    case 'creationTimestamp':
                        if(!$data[$k]){
                            $data[$k] = "now()";
                        }
                        break;
                    case 'currentTimestamp':
                        $data[$k] = "now()";
                        break;
                    default:
                        $data[$k] = $default;
                        break;
                }
            }
        }

        $entry->setData($data);
    }

    /**
     * @param $entry    baseModel
     * @return bool
     */
    protected function validate($entry){
        $attributes = $entry->defineAttributes();

        $data = $entry->getData();

        foreach ($attributes as $k => $v){
            //check if isset
            if(!array_key_exists($k, $data)){
                $entry->addError($k, 'Value not set');
            }

            //set slug to title by default if there is no slug further validation comes later...
            if($k === 'slug' && $data[$k] == null){
                $data[$k] = str_replace(" ", "-", $data['title']);
                $entry->setData($data[$k] , 'slug');
            }

            //required value => set but 0
            if(isset($v['required'])){
                if(array_key_exists($k, $data) && !$data[$k]){
                    $entry->addError($k, 'Value must be set, required value');
                }
            }
        }

        if($entry->getErrors() == null){
            return true;
        }
        return false;
    }

    /**
     * @param $data     array
     * @param $model    baseModel
     * @return null|baseModel
     */
    protected function populateModel($data, $model){
        if($model->setData($data)){
            $attributes = $model->defineAttributes();
            foreach ($attributes as $k => $v){
                if(isset($v['relatedTo']) && $relation = $v['relatedTo']){
                    //TODO check this
                    if(strpos($data[$k], ',') !== false){
                        $parts = explode(',', $data[$k]);
                    }else{
                        $parts = $data[$k];
                    }
                    if(is_array($parts) && $parts){
                        $model->$k = $parts;
                    }

                    $class = $relation['model'];
                    $criteriaModel = new elementCriteriaModel(anu()->$class);
                    $criteriaModel->relatedTo  = array(
                        'field' => $k,
                        'id'    => $data['id'],
                        'model' => Anu::getClassName($this)
                    );
                    $model->$k = $criteriaModel;
                }else{
                    $model->$k = $data[$k];
                }
            }
            $model->id = $data['id'];
            return $model;
        }
        throw new \Exception('Could not populate Model');
    }


    /**
     * @param $entry baseModel|entryModel|assetModel
     */
    public function setDataFromPost($entry){
        $post = anu()->request->getValue('data');
        $attributes = $entry->defineAttributes();
        foreach ($attributes as $k => $v){
            if(array_key_exists($k, $post)){
                if(isset($v['relatedTo']) && $relation = $v['relatedTo']){
                    $relations = $post[$k];
                    if(!is_array($relations)){
                        $relations = array($relations);
                    }
                    $className = $relation['model'];
                    $entry->$k = array();
                    foreach ($relations as $rel){
                        $entryRelation = anu()->$className->getEntryById((int)$rel);
                        if($entryRelation){
                            $entry->$k[] = $entryRelation;
                        }
                    }
                }else{
                    $entry->setData($post[$k], $k);
                }
            }
        }
    }

    /**
     * @param $attributes
     * @param $join
     * @param null $parentTable
     * @return array
     */
    public function iterateDBSelect($attributes, $parentTable){
        $select = array();
        $primaryKey = $this->primary_key;

        $select[] = $parentTable . "." . $primaryKey . "(id)";
        foreach ($attributes as $k => $v){
            if(isset($v['relatedTo'])){
                $relation = $v['relatedTo'];
                if(is_array($relation)){
                    if(isset($relation['table'], $relation['field'])){
                        $select[] = '#GROUP_CONCAT(relation.id SEPARATOR \',\') as ' . $k;
                    }
                }
            }else{
                $select[] = $parentTable . "." . $k;
            }
        }

        return $select;
    }
}