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
     * @param $class                string                      className of the related Entry -> matrix|class eg page, answer...
     * @param $field                string                      the field in the data
     * @return elementCriteriaModel
     */
    public function getBaseCriteriaModelForPopulatedEntry($entry, $data, $attributes, $field){
        $class = $attributes['model'];
        $criteriaModel = new elementCriteriaModel(anu()->$class);
        $id = isset($entry->id) ? $entry->id : null;
        //new empty entry at all.... with no id an nothing
        if($data === null || !array_key_exists($field, $data) || $data[$field] === null){
            $criteriaModel->relatedTo  = array(
                'field' => $field,
                'id'    => $id,
                'model' => $attributes['class']
            );
        }else{
            if(array_key_exists($field, $data) && is_array($data[$field])) {
                if (count($data[$field]) && array_key_exists(0, $data[$field]) && !is_array($data[$field][0])) {
                    // user gave an array with all ids
                    $primary_key = $attributes['field'];
                    $criteriaModel->$primary_key = $data[$field];
                    $criteriaModel->storeIds($data[$field]);
                }elseif(array_key_exists('ids', $data[$field])){
                    //user did not change anything and just returned the origianl CriteriaModel of the entry
                    $criteriaModel->relatedTo  = array(
                        'field' => $field,
                        'id'    => $id,
                        'model' => $attributes['class']
                    );
                    $criteriaModel->storeIds($data[$field]['ids']);
                }else{
                    //user inserted an array of objects eg matrix elements that contains elements with an id
                    $ids = array();
                    foreach ($data[$field] as $populateField){
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
     * @param $entry
     */
    public function getAllFieldsForEntry($recordHandle){
        $response = array();
        if($record = anu()->record->getRecordByName($recordHandle)){
            $handle = $record['name'];
            $join = array(
                '[>]fields' => array('fieldHandle' => 'slug')
            );
            $fields = anu()->database->select('fieldlayout', $join, '*', array(
                'recordHandle'  => array($handle, 'entry')
            ));

            if($fields && is_array($fields) && count($fields)){
                foreach ($fields as $field){
                    $response[$field['slug']] = json_decode($field['settings'], true);
                }
            }
        }
        return $response;
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
}