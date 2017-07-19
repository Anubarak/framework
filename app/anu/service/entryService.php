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

class entryService extends baseService
{
    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;


    public function init(){
        $class = Anu::getClassByName($this, "Record", true);
        $this->table = $class->getTableName();
        $this->primary_key = $class->getPrimaryKey();
    }

    /**
     * @param $entry            entryModel
     * @return bool|int|string
     */
    public function saveEntry($entry){

        $this->checkSavePermission($entry);
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
                    if(isset($value['relatedTo'])){
                        if(isset($entry->$key)){
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
    }

    /**
     * Alias from getEntryById
     *
     * @param $entryId
     * @return baseModel
     */
    public function id($entryId){
        return $this->getEntryById($entryId);
    }

    /**
     * @param $entryId
     * @return baseModel|entryModel|null
     * @throws Exception
     */
    public function getEntryById($entryId){
        if(!isset($entryId) || !is_numeric($entryId))
        {
            throw new Exception('ID is not specified.');
        }

        $this->id = $entryId;
        if($model = Anu::getClassByName($this, 'Model', true)){
            $attributes = $model->defineAttributes();

            $select = $this->iterateDBSelect($attributes, $this->table);

            $join = array(
                "[>]relation" => array($this->table . "." . $this->primary_key => 'id_1')
            );

            $where = array($this->table . "." . $this->primary_key => $entryId);

            $row = anu()->database->select($this->table, $join, $select, $where);

            if(!empty($row) && is_array($row)){
                return $this->populateModel($row[0], $model);
            }
        }else{
            throw new Exception('could not find ' . Anu::getClassName($this));
        }
        return null;
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