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
    public      $tableName = null;
    public      $model = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;


    public function init($service = 'null'){
        $record = Anu::getClassByName($this, "Record", true);
        if($record === false){
            throw new \Exception('could not find Record for ' . get_class($this));
        }

        $this->model      = $record->handle;
        $this->handle      = $record->handle;
        $this->tableName = $record->tableName;
        $this->primary_key = $record->primary_key;
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
            anu()->database->insert($this->tableName, $values);
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
                            $relation['class'] = $entry->class;
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

        anu()->database->update($this->tableName, $values, array(
            $this->tableName . "." . $this->primary_key => $entry->id
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