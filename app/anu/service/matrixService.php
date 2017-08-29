<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 16:39
 */

namespace Anu;


class matrixService extends entryService
{
    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;


    public function init(){
        $class = Anu::getClassByName($this, "Record", true);
        if($class === false){
            throw new \Exception('could not find Record for ' . get_class($this));
        }
        $this->table = $class->getTableName();
        $this->primary_key = $class->getPrimaryKey();
    }


    /**
     * @param $entry            baseModel|matrixModel
     * @return bool|int
     */
    public function saveEntry($entry){
        $this->defineDefaultValues($entry);
        $this->generateSlugForEntry($entry);
        if(!$this->validate($entry) || !$this->checkSavePermission($entry)){
            return false;
        }
        if(!$this->table || !$this->primary_key){
            $className = Anu::getClassName($entry);
            $this->table = anu()->$className->table;
            $this->primary_key = anu()->$className->primary_key;
        }

        //check if its a new entry of if we should update an existing one
        $record = Anu::getClassByName($this, "Record", true);
        $recordAttributes = $record->defineAttributes();

        $attributes = $entry->defineAttributes();
        $matrixAttributes = anu()->matrix->getMatrixByName($entry->handle)->defineAttributes()[$entry->type];
        foreach ($matrixAttributes as $k=>$v){
            $matrixAttributes[$k]['#ignore'] = true;
        }
        $attributes = array_merge($attributes, $matrixAttributes);

        if(!$entry->id){
            //sets a slug if there is no, does nothing if there is one
            $data = $entry->getData();
            $values = array();
            $relationsToSave = array();
            foreach ($attributes as $key => $value){
                if(isset($data[$key])){
                    if ($data[$key] !== 'now()') {
                        switch ($value[0]) {
                            case AttributeType::Relation:
                                if (property_exists($entry, $key)) {
                                    if (!isset($value['relatedTo'])) {
                                        throw new Exception("Error: missing relatedTo Attribute in " . Anu::getClassName($this) . " Service");
                                    }
                                    $relations = $this->getRelationsFromEntryByKey($entry, $key);
                                    $relation = $value['relatedTo'];
                                    foreach ($relations as $rel) {
                                        $relationsToSave[] = $this->getRelationData($relation, $key, $rel);
                                    }
                                }
                                break;
                            default:
                                if (array_key_exists($key, $recordAttributes)) {
                                    $values[$key] = $entry->$key;
                                }
                                break;
                        }
                    } else {
                        $values["#" . $key] = $data[$key];
                    }
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
        }
        $data = $entry->getData();
        $values = array();
        if($entry->id){
            $prim = $this->getPrimaryKey();
            $entry->$prim = $entry->id;
        }

        foreach ($attributes as $key => $value){
            if($data[$key] !== 'now()'){
                //relations
                switch ($value[0]){
                    case AttributeType::Relation:
                        if(property_exists($entry, $key)){
                            if(!isset($value['relatedTo'])){
                                throw new Exception("Error: missing relatedTo Attribute in " . Anu::getClassName($this) . " Service");
                            }

                            $relation = $value['relatedTo'];
                            $relations = $this->getRelationsFromEntryByKey($entry, $key);
                            $this->updateRelations($entry, $key, $relation, $relations);
                        }
                        break;
                    default:
                        if(array_key_exists($key, $recordAttributes)){
                            $values[$key] = $entry->$key;
                        }
                        break;
                }
            }else{
                $values["#".$key] = $data[$key];
            }
        }

        anu()->database->update($this->table, $values, array(
            $this->table . "." . $this->primary_key => $entry->id
        ));

        if($entry->getErrors() == null){
            return $entry->id;
        };
        return false;
    }

    /**
     * @param $matrixId
     * @return baseModel|null
     */
    public function getMatrixById($matrixId){
        return $this->getEntryById($matrixId);
    }

    public function getMatrixByName($name){
        $matrix = Anu::getClassByName($name, "Model", true);
        return $matrix;
    }

    /**
     * Populate the entryModel, add relations
     *
     * @param $data         array
     * @param $model        baseModel|entryModel
     * @return mixed
     */
    public function populateModel($data, $model, $attributes = null){
        if($attributes === null){
            $attributes = $model->defineAttributes();
        }
        $matrixAttributes = anu()->matrix->getMatrixByName($data['handle'])->defineAttributes()[$data['type']];
        $attributes = array_merge($attributes, $matrixAttributes);
        return parent::populateModel($data, $model, $attributes);
    }
}