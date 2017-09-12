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
    public      $tableName = null;
    public      $model      = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;
    protected   $fields = null;

    /**
     * @param string $service
     * @throws Exception
     */
    public function init($record = null){
        /** @var baseRecord $record */
        if($record !== null){
            $this->tableName = $record->tableName;
            $this->model = $record->handle;
            $this->primary_key = $record->primary_key;
        }
    }

    /**
     * @param $entry            entryModel|fieldModel
     * @return bool|int|string
     */
    public function saveEntry($entry){
        $this->defineDefaultValues($entry);
        $this->generateSlugForEntry($entry);
        if(!$this->validate($entry) || !$this->checkSavePermission($entry)){
            return false;
        }

        $event = new event($this, array(
           'entry'  => &$entry
        ));
        $event->raiseEvent('onBeforeSaveEntry');

        //check if its a new entry of if we should update an existing one
        $record = Anu::getRecordByName($entry->class);
        $recordAttributes = $record->defineAttributes();

        if(!$entry->id){
            //sets a slug if there is no, does nothing if there is one
            $data = $entry->getData();
            $values = array();
            $relationsToSave = array();
            $attributes = $entry->defineAttributes();
            foreach ($attributes as $key => $value){
                if($data[$key] !== 'now()'){
                    if($field = anu()->field->getField($value[0])){
                        $field->onInsert($entry, $key, $value, $relationsToSave, $values);
                    }else{
                        if(array_key_exists($key, $recordAttributes)){
                            $values[$key] = $entry->$key;
                        }
                    }
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->insert($this->tableName, $values);
            $id = anu()->database->id();
            $entry->id = $id;
            if($relationsToSave && $id){
                foreach ($relationsToSave as $relation){
                    if($relation['id_1'] == 0){
                        $relation['id_1'] = $id;
                    }
                    if($relation['id_2'] == 0){
                        $relation['id_2'] = $id;
                    }
                    anu()->database->insert('relation', $relation);
                }
            }
            return $id;
        }else{
            $this->defineSiblingsFromEntry($entry, $oldEntry);
            $attributes = $entry->defineAttributes();
            $data = $entry->getData();
            $values = array();
            if($entry->id){
                $prim = $this->getPrimaryKey();
                $entry->$prim = $entry->id;
            }

            foreach ($attributes as $key => $value){
                if($data[$key] !== 'now()'){
                    //relations
                    if($field = anu()->field->getField($value[0])){
                        $field->onUpdate($entry, $key, $value, $values, $oldEntry);
                    }else{
                        if(array_key_exists($key, $recordAttributes)){
                            $values[$key] = $entry->$key;
                        }
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
        if($model = Anu::getModelByName($this->model)){

            $where = array();
            $select = $this->iterateDBSelect();

            $where[$this->tableName . "." . $this->primary_key] = $entryId;

            $row = anu()->database->get($this->tableName, $select, $where);

            anu()->database->debugError();
            if($row){
                return $this->populateModel($row, $model);
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

        anu()->database->delete($this->tableName, array(
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
     * @return elementCriteriaModel
     */
    public function getCriteria()
    {
        return new elementCriteriaModel($this);
    }


    /**
     * Renders the detail template
     *
     * @param $slug
     * @throws Exception
     */
    public function renderEntryBySlug($slug){
        if($entry = $this->getEntryBySlug($slug)){
            $template = $entry->class . "/index.twig";
            anu()->template->render($template, array(
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
        $id = anu()->database->select($this->tableName, $this->primary_key, array(
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
    public function generateSlugForEntry($entry){
        if(!$entry->id && property_exists($entry, 'slug')){
            $slug = $entry->slug;
            $slugInUse = anu()->database->has($this->tableName, array('slug' => $slug));
            if(!$slugInUse && $slug != null){
                return true;
            }

            $counter = 0;
            $originalSlug = $slug;
            while ($slugInUse){
                $counter++;
                $slug = $originalSlug . "-" . $counter;
                $slugInUse = anu()->database->has($this->tableName, array('slug' => $slug));

            }
            $slug = $originalSlug . "-" . $counter;

            $entry->slug = $slug;
            return $slug;
        }
        return true;
    }

    /**
     * Get all Relations from array or criteriamodel
     *
     * @param $entry    entryModel
     * @param $key
     * @return array|int
     * @throws Exception
     */
    public function getRelationsFromEntryByKey($entry, $key){
        $relations = array();
        if(!$entry->$key instanceof elementCriteriaModel){
            if(!is_array($entry->$key)){
                $entry->$key = array($entry->$key);
            }
            foreach ($entry->$key as $item){
                if($item instanceof entryModel) {
                    if($item->id){
                        $relations[] = $item->id;
                    }else{
                        throw new Exception("no id given for subentry with title = " . $item->title);
                    }
                }else{
                    $relations[] = $item;
                }
            }
        }else{
            $relations = $entry->$key->getStoredIds();
        }
        return $relations;
    }

    /**
     * Get Data for Relation Table
     *
     * @param $relation
     * @param $field_1
     * @param $id_2
     * @param int $id_1
     * @return array
     */
    public function getRelationData($relation, $field_1, $id_2, $id_1 = 0, $revert = false){
        if(!$revert){
            return array(
                'fieldHandle' => $field_1,
                'id_1' => $id_1,
                'id_2' => $id_2,
                'record_1' => $relation['class'],
                'record_2'=> $relation['model']
            );
        }

        return array(
            'fieldHandle' => $field_1,
            'id_1' => $id_2,
            'id_2' => $id_1,
            'record_1' => $relation['model'],
            'record_2'=> $relation['class']
        );
    }


    /**
     * Set new position if relations are deledeted/changed
     *
     * @param $positionField
     * @param $relatedField
     * @return mixed
     */
    public function setNewPosition($positionField, $relatedField, $relationId = 'nothing', $excludeId = null){
        $criteria = new elementCriteriaModel($this);
        $criteria->relatedTo = array(
            'field' => $relatedField,
            'id'    => $relationId,
            'model' => Anu::getClassName($this)
        );
        if($excludeId){
            $string = $this->primary_key . "[!]";
            $criteria->$string = $excludeId;
        }

        $parent = $criteria->last();
        if(!$parent){
            return 0;
        }
        return $parent->$positionField + 1;
    }


    /**
     * Get Children from Entry if the relation side is the opposite
     *
     * @param $relatedField
     * @param string $relationId
     * @param null $excludeId
     * @return array
     */
    public function getChildrenFromEntry($relatedField, $relationId = 'nothing', $excludeId = null){
        $record = Anu::getRecordByName($this->model);
        $criteria = new elementCriteriaModel($record);
        $criteria->relatedTo = array(
            'field' => $relatedField,
            'id'    => $relationId,
            'model' => Anu::getClassName($this)
        );
        if($excludeId){
            $string = $this->primary_key . "[!]";
            $criteria->$string = $excludeId;
        }
        return $criteria->ids();
    }

    /**
     * Used as a helper before updating
     *
     * @param $entry    baseModel|entryModel
     * @param null $oldEntry baseModel|entryModel
     * @return bool
     */
    public function defineSiblingsFromEntry($entry, &$oldEntry = null){
        $attributes = $entry->defineAttributes();
        if(property_exists($entry, 'oldSiblings')){
            return true;
        }
        if($key = Anu::array_search_parent(AttributeType::Position, $attributes)){
            $relatedField = array_key_exists('relatedField', $attributes[$key])? $attributes[$key]['relatedField'] : null;
            $oldEntry = $this->getEntryById($entry->id);
            if($relatedField !== null && $oldParent = $oldEntry->$relatedField->first()){
                $relationId = $oldParent->id;
                $relField = $attributes[$relatedField]['relatedTo']['field'];
            }else{
                $relationId = 'nothing';
                $relField = $relatedField;
            }
            $entry->oldSiblings =  $this->getChildrenFromEntry($relField, $relationId, $entry->id);
        }
    }

    /**
     * Update Relations in an existing Entry
     *
     * @param $entry
     * @param $field
     * @param $relationInformation
     * @param $relationIds
     */
    public function updateRelations($entry, $field, $relationInformation, $relationIds){
        //delete previous relations if there are any
        anu()->database->delete('relation', array(
            "OR #or" => array(
                'AND #first' => array(
                    'fieldHandle'   => $field,
                    'record_1'      => $entry->class,
                    'id_1'          => $entry->id
                ),
                'AND #second' => array(
                    'fieldHandle'   => $field,
                    'record_2'      => $entry->class,
                    'id_2'          => $entry->id
                )
            )
        ));

        if(count($relationIds)){
            foreach ($relationIds as $rel){
                if($rel){
                    anu()->database->insert('relation', $this->getRelationData($relationInformation, $field, $rel, $entry->id));

                    anu()->database->insert('relation', $this->getRelationData($relationInformation, $field, $rel, $entry->id, true));
                }
            }
        }
    }

    /**
     * @return array|null
     */
    public function getFieldsForEntry($entry = null){
        if(!$this->fields){
            $entryType = ($entry && property_exists($entry, 'entryType') && $entry->entryType)? $entry->entryType : null;
            $this->fields = anu()->field->getAllFieldsForEntry($this->model, false, $entryType);

        }
        return $this->fields;
    }

    /**
     * @param $entry
     * @param bool $excludeMetaData
     * @return array|bool
     */
    public function getFieldLayout($entry, $excludeMetaData = false){
        $record = anu()->record->getRecordByName($entry->class, true);
        $fieldLayout = array();
        if($record){
            $tabs = anu()->field->getAllTabsForEntry($record, $entry->entryType);

            foreach ($tabs as $k => $tab){
                $tabs[$k]['fields'] = anu()->field->getAllFieldsForEntry($record, false, $entry->entryType, $tab['id']);
            }
            if(!$excludeMetaData){
                $tabs[] = array(
                    'fields'    => $entry->baseAttributes(),
                    'label'     => 'Metadaten',
                    'id'        => 'base',
                    'position'  => 99,
                );
                if(count($tabs) === 1){
                    $tabs[]['fields'] = $entry->defineAttributes();
                }
            }
            $fieldLayout = $tabs;
        }


        return $fieldLayout;
    }

    public function getEntryTypes(){
        $record = Anu::getRecordByName($this->model);
    }
}