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


    public function overview(){
        anu()->template->render('admin/fields/overview.twig', array());
        exit();
    }


    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function edit($parameter){
        //$this->requireLogin();
        $field = null;
        if($parameter){
            if(ctype_digit($parameter[count($parameter)-1])){
                $field = anu()->field->getEntryById($parameter[count($parameter)-1]);
            }
        }
        if(!$field){
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
                'model' => $record->handle
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

    /**
     * Bind fields to record, create database Tables
     *
     * @param $param
     */
    public function bindFields($param){
        //if(is_array($param) && count($param)){
            $record = anu()->record->getRecordById(26/*$param[0]*/);
            $allFields = anu()->field->getAllFields();
            anu()->template->addAnuJsObject($allFields, 'fields');

            $tabs = anu()->field->getAllTabsForEntry($record);

            $fieldsForEntry = array();
            foreach ($tabs as $tab){
                $fieldsForEntry[$tab] = anu()->field->getAllFieldsForEntry($record, true, $record->handle, $tab);
            }
            if(count($fieldsForEntry) == 0){
                $fieldsForEntry['tab1'] = array();
            }

            anu()->template->addAnuJsObject($fieldsForEntry, 'fieldsForRecord');
            anu()->template->addAnuJsObject($record, 'record');
            anu()->template->render('admin/fields/bindFields.twig', array(
                'record'    => $record
            ));
            exit;
        //}
    }


    /**
     * Bind fields to record
     */
    public function bindFieldsSave(){
        $data = anu()->request->getValue('record');

        $record = anu()->record->getRecordById($data['id'], true);
        $tabs = $data['fields'];
        $response = false;
        foreach ($tabs as $tabHandle => $ids){
            $fields = array();
            foreach ($ids as $fieldId){
                $fields[] = anu()->field->getFieldById($fieldId);;
            }

            $response = anu()->record->bindFieldsToRecord($record, $fields, $tabHandle, $record->handle);
        }


        $this->returnJson(array(
            'success'   => $response
        ));
    }

}