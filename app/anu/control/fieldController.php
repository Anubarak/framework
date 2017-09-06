<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Anu;


class fieldController extends baseController
{
    public function getContent(){
        $records = anu()->record->loadAllRecords();

        anu()->template->addAnuJsObject($records, 'records');

        anu()->template->render('admin/record/index.twig', array(
            'records' => $records
        ));
    }


    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function edit($parameter){
        $this->requireLogin();

        if(count($parameter) != 2){
            return null;
        }
        $class = $parameter[0];
        if(!$field = anu()->field->getEntryById($parameter[1])){
            $field = new fieldModel('field');
            anu()->field->populateModel(null, $field);
        }
        $allRecords = anu()->record->getAllRecords();
        anu()->template->addAnuJsObject($allRecords, 'records');
        echo anu()->entry->renderForm($field, "admin/fields/index.twig");
        die();
    }


    /**
     * save entry
     */
    public function save(){
        $data = anu()->request->postVar('entry');

        $entry = new fieldModel('field');
        $fieldOptions = anu()->request->postVar('fieldOptions', null);
        $fieldOptions[0] = $data['fieldType'];
        if($data['fieldType'] === "relation"){
            $record = anu()->record->getRecordById($fieldOptions['relatedTo']);
            $fieldOptions['relatedTo'] = array(
                'table' => $record->tableName,
                'field' => $record->primary_key,
                'model' => $record->model
            );
        }

        $data['settings'] = json_encode($fieldOptions);
        anu()->field->populateModel($data, $entry);
        $response = array();
        if(!$id = anu()->field->saveEntry($entry)){
            $response['success'] = false;
            $response['errors'] = $entry->getErrors();
        }else {
            $response['success'] = true;
            $response['id']     = $id;
        }

        $this->returnJson($response);

    }

}