<?php

/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:18
 */
namespace Anu;

use Exception;
use function Sodium\crypto_aead_aes256gcm_is_available;

class entryService
{
    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;

    /**
     * @param $entry            entryModel
     * @return bool|int|string
     */
    public function saveEntry($entry){
        $this->defineDefaultValues($entry);

        if(!$this->validate($entry)){
            return false;
        }

        if(!$this->table || !$this->primary_key){
            $className = Anu::getClassName($entry);
            $this->table = anu()->$className->table;
            $this->primary_key = anu()->$className->primary_key;
        }

        //check if its a new entry of if we should update an existing one
        if(!$entry->id){
            //sets a slug if there is no, does nothing if there is one
            $this->getSlugForEntry($entry);
            $data = $entry->getData();

            $values = array();
            $relationsToSave = array();
            foreach ($entry->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    if(isset($value['relatedTo'])){
                        if(isset($entry->$key) && is_array($entry->$key)){
                            $relations = $entry->$key;
                            $relation = $value['relatedTo'];
                            foreach ($relations as $rel){
                                $relationsToSave[] = array(
                                    'field_1' => $key,
                                    'field_2' => $relation['field'],
                                    'id_2' => $rel->id,
                                    'model_1' => Anu::getClassName($this),
                                    'model_2'=> $relation['model']
                                );
                            }
                        }
                    }else{
                        $values[$key] = ($data[$key])? $data[$key] : 0;
                    }
                }else{
                    $values["#".$key] = $data[$key];
                }
            }
            anu()->database->insert($this->table, $values);
            $id = anu()->database->id();
            $entry->id = $id;
            if($relationsToSave && $id){
                foreach ($relationsToSave as $relation){
                    $relation['id_1'] = $id;
                    anu()->database->insert('relation', $relation);
                }
            }
            return $id;
        }else{
            $data = $entry->getData();
            $values = array();
            foreach ($entry->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    if(isset($value['relatedTo'], $entry->$key)){
                        $relation = $value['relatedTo'];
                        //save only if its not a criteriaModel...
                        if(!$entry->$key instanceof elementCriteriaModel){
                            if(!is_array($entry->$key) && $entry->$key instanceof entryModel){
                                //no array but at least a entryModel...
                                $entry->$key = array($entry->$key);
                            }

                            //delete prevoius relations if there are any
                            if($data[$key]){
                                $parts = explode(',', $data[$key]);
                                anu()->database->delete('relation', array(
                                    'id' => $parts
                                ));
                            }

                            $relations = $entry->$key;
                            foreach ($relations as $rel){
                                anu()->database->insert('relation', array(
                                    'field_1' => $key,
                                    'field_2' => $relation['field'],
                                    'id_1' => $entry->id,
                                    'id_2' => $rel->id,
                                    'model_1' => Anu::getClassName($this),
                                    'model_2'=> $relation['model']
                                ));
                            }
                        }
                    }else{
                        $values[$key] = ($data[$key])? $data[$key] : 0;
                    }
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->update($this->table, $values, array(
                $this->table . "." . $this->primary_key => $entry->id
            ));

            return anu()->database->id();
        }
        return false;
    }

    /**
     * Alias from getEntryById
     *
     * @param $entryId
     * @return null
     */
    public function id($entryId){
        return $this->getEntryById($entryId);
    }

    /**
     * @param $entryId
     * @return null
     * @throws Exception
     */
    public function getEntryById($entryId){
        if(!isset($entryId) || !is_numeric($entryId))
        {
            throw new Exception('ID is not specified.');
        }
        else
        {
            $this->id = $entryId;
            $match = array();
            $className = get_class($this);
            preg_match('/Anu\\\([a-zA-Z0-9-]*)Service/',$className, $match);
            if(count($match) > 1){
                $modelName = Anu::getNameSpace() . $match[1] . "Model";
                $model = new $modelName();
                $attributes = $model->defineAttributes();
                $relations = array();

                $select = $this->iterateDBSelect($attributes, $relations, $this->table);

                $join = array(
                    "[>]relation" => array($this->table . "." . $this->primary_key => 'id_1')
                );

                $where = array($this->table . "." . $this->primary_key => $entryId);

                $row = anu()->database->select($this->table, $join, $select, $where);

                if(!empty($row) && is_array($row)){
                    return $this->populateModel($row[0], $model);
                }
            }
            return null;
        }
    }

    /**
     * @param $attributes
     * @param $join
     * @param null $parentTable
     * @return array
     */
    private function iterateDBSelect($attributes, &$relations, $parentTable){
        $select = array();
        $primaryKey = $this->primary_key;

        $select[] = $parentTable . "." . $primaryKey . "(id)";
        foreach ($attributes as $k => $v){
            if(isset($v['relatedTo'])){
                $relation = $v['relatedTo'];
                if(is_array($relation)){
                    if(isset($relation['table'], $relation['field'])){
                        $maxRelations = (isset($relation['limit']))? $relation['limit'] : 100;
                        $selects = array(
                            'id'
                        );
                        $rows = anu()->database->select('relation', $selects, array(
                            'field_1'   => $k,
                            'field_2'   => $relation['field'],
                            'id_1'      => $this->id,
                            'model_1'   => Anu::getClassName($this),
                            'model_2'   => $relation['model'],
                            'LIMIT'     => $maxRelations
                        ));

                        //found relations
                        if($rows){
                            foreach ($rows as $row){
                                $relations[] = (int)$row['id'];
                            }
                        }
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
     * @param $entry    entryModel
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
                $data[$k] = $data['title'];
                $entry->setData($data['title'] , 'slug');
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
     * @param $data
     * @param $model    entryModel
     * @return null
     * @throws Exception
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
                    /*
                    $rows = craft()->database->select('relation', 'id', array(
                        'id' => $parts
                    ));


                    foreach ($rows as $row){
                        $className = Craft::getClassByName($row['model_2']);
                        $class = new $className;
                        $entry = $class->getEntryById((int)$row['id_2']);
                        $model->$k[] = $entry;
                    }
                    */

                }else{
                    $model->$k = $data[$k];
                }
            }
            $model->id = $data['id'];
            return $model;
        }else{
            throw new Exception('Could not populate Model');
        }
        return null;
    }

    /**
     * @param $entry    entryModel
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
     * @param $entry    entryModel
     */
    public function setDataFromPost($entry){
        $post = anu()->request->getValue('data');
        $attributes = $entry->defineAttributes();
        foreach ($attributes as $k => $v){
            if(array_key_exists($k, $post)){
                if(isset($v['relatedTo']) && $relation = $v['relatedTo']){
                    $relations = $post[$k];
                    $className = Anu::getClassByName($relation['model']);
                    $class = new $className();
                    $entry->$k = array();
                    foreach ($relations as $rel){
                        $entryRelation = $class->getEntryById((int)$rel);
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
     * @param $entry
     * @return bool
     */
    public function deleteEntry($entry){
        if(!$entry->id){
            return false;
        }

        anu()->database->delete($this->table, array(
            $this->primary_key => $entry->id
        ));

        anu()->database->delete('relation', array(
            'OR' => array(
                'AND #field1' => array(
                    'id_1' => $entry->id,
                    'model_1' => Anu::getClassName($this)
                ),
                'AND #field2' => array(
                    'id_2' => $entry->id,
                    'model_2' => Anu::getClassName($this)
                )
            )
        ));
        return true;
    }


    /**
     * @return string
     */
    public function getTable(){
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(){
        return $this->primary_key;
    }

    /**
     * @return elementCriteriaModel
     */
    public function getCriteria()
    {
        return new elementCriteriaModel($this);
    }


    /**
     * @param $attributes
     */
    public function find($attributes = null){
        $criteria = new elementCriteriaModel($this);
        return $criteria->find($attributes);
    }

    /**
     * Renders the detail template
     *
     * @param $slug
     * @throws Exception
     */
    public function renderEntryBySlug($slug){
        if($entry = $this->getEntryBySlug($slug)){
            anu()->template->render($this->template, array(
                'entry' => $entry
            ));
        }else{
            throw new Exception('could not find entry with slug ' . $slug);
        }
    }

    /**
     * @param $slug
     * @return null|entryModel
     */
    public function getEntryBySlug($slug){
        $id = anu()->database->select($this->table, $this->primary_key, array(
            'slug' => $slug
        ));

        if($id){
            return $this->getEntryById($id[0]);
        }
        return null;
    }

    /**
     * @param $entry entryModel
     */
    public function getSlugForEntry($entry){
        $slug = $entry->getAttribute('slug');
        $slugInUse = anu()->database->has($this->table, array('slug' => $slug));
        if(!$slugInUse){
            return true;
        }

        $counter = 0;
        $originalSlug = $slug;
        while ($slugInUse){
            $counter++;
            $slug = $originalSlug . "-" . $counter;
            $slugInUse = anu()->database->has($this->table, array('slug' => $slug));

        }
        $slug = $originalSlug . "-" . $counter;

        $entry->setData($slug, 'slug');
        return $slug;

    }
}