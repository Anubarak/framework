<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:13
 */

namespace Anu;

require_once BASE . 'app\anu\service\entryService.php';
class fieldService extends entryService
{

    public function init($record = NULL){
        $this->tableName = 'fields';
        $this->model    = 'field';
        $this->primary_key = 'id';
    }

    public $handle = '';

    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null){

    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values){

    }

    public function onPopulate($model, $attributes, $data, $key){

    }

    public function onInstall($record, $field){

    }

    /**
     * @param $handle
     * @return fieldService|bool
     */
    public function getField($handle){
        return Anu::getClassByName($handle, "field", true);
    }



    /**
     * Create Criteria Model to find related Entries for $entry
     *
     * @param $entry                baseModel|entryModel        Plain Model of the entry
     * @param $data                 array                       array with the data of the field
     * @param $field                string                      the field in the data
     * @return elementCriteriaModel
     */
    public function getBaseCriteriaModelForPopulatedEntry($entry, $data, $attributes, $field){
        $class = $attributes['model'];
        $record = Anu::getRecordByName($class);

        $criteriaModel = new elementCriteriaModel($record);

        $id = isset($entry->id) ? $entry->id : null;
        //new empty entry at all.... with no id an nothing
        if($data === null){
            $criteriaModel->relatedTo  = array(
                'field' => $field,
                'id'    => $id,
                'model' => $attributes['class']
            );
        }else{
            if(is_array($data)) {
                if (count($data) && !is_array($data[0])) {
                    // user gave an array with all ids
                    /** @var baseRecord $record */
                    $record = anu()->record->getRecordByName($attributes['model'], true);
                    $primary_key = $record->primary_key;
                    $criteriaModel->$primary_key = $data;
                    $criteriaModel->storeIds($data);
                }elseif(array_key_exists('ids', $data)){
                    //user did not change anything and just returned the origianl CriteriaModel of the entry
                    $criteriaModel->relatedTo  = array(
                        'field' => $field,
                        'id'    => $id,
                        'model' => $attributes['class']
                    );
                    $criteriaModel->storeIds($data['ids']);
                }else{
                    //user inserted an array of objects eg matrix elements that contains elements with an id
                    $ids = array();
                    foreach ($data as $populateField){
                        $ids[] = $populateField['id'];
                    }
                    if($ids){
                        $primary_key = anu()->matrix->getPrimaryKey();
                        $criteriaModel->$primary_key = $ids;
                        $criteriaModel->storeIds($ids);
                    }
                }
            }else{
                //no data from user -> just search the original relations from database
                $criteriaModel->relatedTo  = array(
                    'field' => $field,
                    'id'    => $id,
                    'model' => $attributes['class']
                );
                $ids = $criteriaModel->ids();
                $criteriaModel->storeIds($ids);
            }
        }

        return $criteriaModel;
    }


    /**
     * Get all Fields for Entry
     *
     * @param $record
     * @param null $returnIds
     * @param null $entryType
     * @param null $tabId
     * @return array|bool
     */
    public function getAllFieldsForEntry($record, $returnIds = null, $entryType = null, $tabId = null){
        if(is_string($record)){
            if(!$record = anu()->record->getRecordByName($record, true)){
                return array();
            }
        }

        $response = array();

        $handle = $record->handle;
        $join = array(
            '[>]fields' => array('fieldHandle' => 'slug')
        );
        $select = (!$returnIds)? '*' : 'fields.id';

        // Where statement
        $where = array(
            'recordHandle'  => array($handle, 'entry'),
            'ORDER'         => 'fieldlayout.id'
        );
        if($entryType){
            $where['entryType'] = $entryType;
        }else{
            $entryType = anu()->record->getFirstEntryTypeForRecord($record);
            $where['entryType'] = $entryType['handle'];
        }

        if($tabId){
            $where['tabId'] = $tabId;
        }
        $fields = anu()->database->select('fieldlayout', $join, $select, $where);

        if($returnIds){
            return $fields;
        }

        if($fields && is_array($fields) && count($fields)){
            foreach ($fields as $field){
                $response[$field['slug']] = json_decode($field['settings'], true);
            }
        }
        return $response;
    }

    /**
     * @param $record
     * @return array|bool
     */
    public function getAllTabsForEntry($record, $entryType = null){
        if(is_string($record)){
            if(!$record = anu()->record->getRecordByName($record, true)){
                return array();
            }
        }

        $join = array(
            '[>]fieldlayout' => array('id' => 'tabId')
        );
        $type = ($entryType)? $entryType : $record->handle;
        $select = array(
            'handle', 'label', 'fieldlayouttabs.id as id', 'position'
        );
        $tabs = anu()->database->select('fieldlayouttabs', $join, $select, array(
            'fieldlayout.recordHandle'      => $record->handle,
            'fieldlayout.entryType'         => $type,
            'ORDER'                         => 'fieldlayouttabs.position'
        ));

        if(count($tabs)){
            return $tabs;
        }
        return array();
    }

    /**
     * @return array all fields
     */
    public function getAllFields(){
        return anu()->field->find(['enabled' => true]);
    }

    /**
     * return array
     */
    public function getAllPossibleFieldTypes(){
        return array(
            array(
                'id' => AttributeType::Text,
                'label' => 'Text'
            ),
            array(
                'id'    => AttributeType::DropDown,
                'label' => "Dropdown"
            ),
            array(
                'id'    => AttributeType::Relation,
                'label' => 'Verknüpfung'
            ),
            array(
                'id'    => AttributeType::Mixed,
                'label' => Anu::t('Text unformatiert')
            ),
            array(
                'id'    => AttributeType::Number,
                'label' => Anu::t('Zahl')
            ),
            array(
                'id'    => AttributeType::Bool,
                'label' => Anu::t('Lichtschalter')
            )
        );
    }


    /**
     * Return Field by Id
     *
     * @param $fieldId
     * @return mixed
     */
    public function getFieldById($fieldId){
        $field = anu()->database->select('fields', '*', array(
            'id' => $fieldId
        ));
        if($field){
            $fieldModel = new fieldModel('field');
            return anu()->field->populateModel($field[0], $fieldModel);
        }
    }
}