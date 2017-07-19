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
                    case Defaults::creationTimestamp:
                        if(!$data[$k]){
                            $data[$k] = "now()";
                        }
                        break;
                    case Defaults::currentTimestamp:
                        $data[$k] = "now()";
                        break;
                    case Defaults::currentUserId:
                        if(anu()->user->getCurrentUser() && anu()->user->getCurrentUser()->id && $data[$k] == null){
                            $data[$k] = (int)anu()->user->getCurrentUser()->id;
                        }
                        break;
                    default:
                        if($k !== 'slug' && !$entry->id){
                            $data[$k] = $default;
                        }
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
            if($k === 'slug' && $data[$k] == null && !$entry->id){
                $data[$k] = str_replace(" ", "-", $data['title']);
                $entry->setData($data[$k] , 'slug');
            }

            //required value => set but 0
            if(isset($v['required']) && ($k !== 'slug') && !$entry->id){
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
     * Generate Model from by Post variables
     *
     * @return baseModel|baseService|entryModel|entryService|bool|null|string
     */
    public function generateEntryFromPost(){
        $post = anu()->request->getValue('data');
        if(isset($post[$this->primary_key])){
            $entry = $this->getElementById($post[$this->primary_key]);
        }else{
            $entry = Anu::getClassByName($this, "Model", true);
        }

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
                    $entry->$k = $post[$k];
                }
            }
        }
        return $entry;
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

    /**
     * Permissions
     *
     * @return array
     */
    public function definePersmissions(){
        return array(
            Permission::Insert  =>  array(Permission::LoggedIn),
            Permission::Update  =>  array(Permission::Admin, Permission::Author),
            Permission::Delete  =>  array(Permission::Admin),
            Permission::Read    =>  array(Permission::All)
        );
    }


    /**
     * @param $userId
     * @return baseModel|null
     */
    public function getElementById($elementId){
        if(!is_numeric($elementId) || !$elementId){
            return null;
        }
        $this->id = $elementId;
        if($model = Anu::getClassByName($this, 'Model', true)){
            $attributes = $model->defineAttributes();

            $select = $this->iterateDBSelect($attributes, $this->table);

            $join = array(
                "[>]relation" => array($this->table . "." . $this->primary_key => 'id_1')
            );
            $where = array($this->table . "." . $this->primary_key => $elementId);

            $row = anu()->database->select($this->table, $join, $select, $where);

            if(!empty($row) && is_array($row)){
                return $this->populateModel($row[0], $model);
            }
        }else{
            throw new \Exception('could not find ' . Anu::getClassName($this));
        }
        return null;
    }

    /**
     * @param $element            baseModel
     * @return bool|int
     */
    public function saveElement($element){
        $this->defineDefaultValues($element);

        if(!$this->validate($element)){
            return false;
        }

        if(!$this->table || !$this->primary_key){
            $className = Anu::getClassName($this);
            $this->table = anu()->$className->table;
            $this->primary_key = anu()->$className->primary_key;
        }

        //check if its a new entry of if we should update an existing one
        if(!$element->id){
            $data = $element->getData();
            $values = array();
            foreach ($element->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    $values[$key] = ($data[$key])? $data[$key] : 0;
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->insert($this->table, $values);
            $id = anu()->database->id();
            $element->id = $id;
            return $id;
        }else{
            $data = $element->getData();
            $values = array();
            foreach ($element->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    $values[$key] = ($data[$key])? $data[$key] : 0;
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->update($this->table, $values, array(
                $this->table . "." . $this->primary_key => $element->id
            ));

            return anu()->database->id();
        }
    }


    /**
     * @param $element baseModel
     * @return bool
     */
    public function checkSavePermission($element){
        if($element->id){
            if(!anu()->user->can(Anu::getClassName($this), Permission::Update, $element)){
                $element->addError('permission', "Currentuser has no permissions");
                return false;
            }
        }else{
            if(!anu()->user->can(Anu::getClassName($this), Permission::Insert)){
                $element->addError('permission', "user has no permissions");
                return false;
            }
        }
        return true;
    }
}