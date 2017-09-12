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
            $field->settings = [];
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
        $fieldOptions = $data['settings'];
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
            $response['db'] = anu()->database->last();
            $response['id']     = $id;
        }

        $this->returnJson($response);

    }

    /**
     * Render Tempalte to bind fields
     *
     * @param $param
     * @throws \Exception
     */
    public function bindFields($param){
        if(is_array($param) && count($param)){
            $newEntryType = false;
            if($param[count($param)-1] === 'add'){
                $record = anu()->record->getRecordById($param[count($param)-2]);
                $entryType = array(
                    'label'         => '',
                    'id'            => null,
                    'handle'        => '',
                    'recordHandle'  => $record->handle
                );
                $newEntryType = true;
            }else{
                if(!$entryType = anu()->record->getEntryTypeById($param[count($param)-1])){
                    throw new \Exception("Could not find Entrytype with id " . $param[count($param)-1]);
                }
                $record = anu()->record->getRecordByName($entryType['recordHandle'], true);
            }


            if(!$newEntryType){
                $tabs = anu()->field->getAllTabsForEntry($record, $entryType['handle']);
                if($entryType['id']){
                    foreach ($tabs as $k => $tab){
                        $tabs[$k]['fields'] = anu()->field->getAllFieldsForEntry($record, true, $entryType['handle'], $tab['id']);
                    }
                }
            }else{
                $tabs = array();
            }
            $allFields = anu()->field->getAllFields();
            anu()->template->addAnuJsObject($allFields, 'fields');

            anu()->template->addAnuJsObject($tabs, 'tabs');
            anu()->template->addAnuJsObject($entryType, 'entryType');
            anu()->template->addAnuJsObject($record, 'record');
            anu()->template->render('admin/fields/bindFields.twig', array(
                'record'    => $record,
                'entryType' => $entryType
            ));
            exit;

        }
    }


    /**
     * Save new binded fields
     *
     * Bind fields to record
     */
    public function bindFieldsSave(){
        $entryType = anu()->request->getValue('entryType');
        if(!$entryType['handle'] || !$entryType['label']){
            $this->returnJson(array(
                'success'   => false
            ));
        }


        $data = anu()->request->getValue('record');
        $record = anu()->record->getRecordById($data['id'], true);
        $tabs = anu()->request->getValue('tabs');
        $tabIds = anu()->tab->updateTabsForEntryType($tabs, $record, $entryType['handle']);

        //Update ids
        foreach ($tabs as $k => $v){
            $tabs[$k]['id'] = $tabIds[$k];
        }

        if($entryType['id']){
            //save
            $oldEntryType = anu()->record->getEntryTypeById($entryType['id']);
            if($oldEntryType['handle'] != $entryType['handle']){
                //Update fields in all tables
                //entry Table
                anu()->database->update($record->tableName, array('entryType' => $entryType['handle'], array(
                    'entryType' => $oldEntryType['handle']
                )));

                anu()->database->update('fieldlayout', array('entryType' => $entryType['handle'], array(
                    'entryType'     => $oldEntryType['handle'],
                    'recordHandle'  => $record->handle,
                )));

            }
            anu()->database->update('entrytypes', $entryType, array(
                'id'    => $entryType['id']
            ));
            $entryTypeId = $entryType['id'];
        }else{
            //insert
            anu()->database->insert('entrytypes', $entryType);
            $entryTypeId = anu()->database->id();
        }

        $response = anu()->record->bindFieldsToRecord($record, $tabs, $entryType['handle']);


        $this->returnJson(array(
            'success'   => $response,
            'id'        => $entryTypeId,
            'tabIds'    => $tabIds
        ));
    }

}