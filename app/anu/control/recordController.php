<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Anu;


class recordController extends baseController
{
    public function getContent(){
        $classRecords = anu()->record->loadAllRecords();
        $installedRecords = anu()->record->getAllRecords(true);

        foreach ($classRecords as $cRecord){
            $cRecord->notEditable = true;
            if(!$cRecord->installed){
                $installedRecords[] = $cRecord;
            }
        }
        foreach ($installedRecords as $record){
            $class = Anu::getNameSpace() . $record->handle . "Record";
            if(!class_exists($class)){
                $record->editable = true;
            }
        }

        anu()->template->addAnuJsObject($installedRecords, 'records');

        anu()->template->render('admin/record/index.twig', array(
            'records' => $installedRecords
        ));
    }




    /**
     * @param $recordId
     */
    public function toggleInstallation(){
        $this->requireLogin();
        $recordName = anu()->request->getValue('record');

        $response = array();
        if($recordName){
            if(anu()->record->isRecordInstalled($recordName)){
                //deinstall
                $response['installed'] = false;
                $response['success'] = anu()->record->deleteRecord($recordName);
            }else{
                //install
                $response['success'] = anu()->record->installRecord($recordName);
                $response['installed'] = true;
            }
        }else{
            $response['success'] = false;
        }

        $this->returnJson($response);
    }

    public function edit($parameter = null){
        $this->requireLogin();
        $isNewRecord = true;
        if($parameter){
            /**@var \Anu\baseRecord $newRecord */
            $newRecord = anu()->record->getRecordById($parameter[count($parameter)-1]);
            $newRecord->attributes = $newRecord->defaultRecordAttributes();

            $isNewRecord = false;
        }else{
            $newRecord = new baseRecord();
            $newRecord->populate();
        }


        anu()->template->addAnuJsObject($newRecord, 'record');
        anu()->template->render('admin/record/add.twig', array(
            'record' => $newRecord,
            'isNewRecord' => $isNewRecord
        ));
        exit;
    }

    /**
     * save and install if new
     */
    public function save(){
        $data = anu()->request->getValue('record', null);

        $record = new entryRecord($data);
        if(!$record->id){
            $recordAttributes = array(
                $data['primary_key'] => array(
                    AttributeType::Number
                )
            );

            $recordAttributes = array_merge($recordAttributes, $record->defineAttributes());
            $record->defineAttributes($recordAttributes);
            $record->defineIndex(array($data['primary_key'] => DBIndex::Primary));

            $response = anu()->record->installRecord($record);
        }else{
            anu()->database->update('records', $data, array(
                'id'   => $record->id
            ));
            $response = true;
        }

        $this->returnJson(array(
            'success' => $response,
            'id'      => $record->id
        ));
    }
}