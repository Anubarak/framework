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
            if(!$cRecord->installed){
                $installedRecords[] = $cRecord;
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
            if(is_array($parameter)){
                $id = $parameter[0];
            }else{
                $id = $parameter;
            }
            $newRecord = anu()->record->getRecordById($id);
            $isNewRecord = false;
        }else{
            $newRecord = new baseRecord();
            $newRecord->populate();
        }


        anu()->template->addAnuJsObject($newRecord, 'record');
        anu()->template->render('admin/record/add.twig', array(
            'record' => $newRecord
        ));
        exit;
    }

    public function save(){
        $data = anu()->request->getValue('record', null);

        $record = new baseRecord($data);
        $recordAttributes = array(
            $data['primary_key'] => array(
                AttributeType::Number
            )
        );

        $recordAttributes = array_merge($recordAttributes, $record->defineAttributes());
        $record->defineAttributes($recordAttributes);
        $record->defineIndex(array($data['primary_key'] => DBIndex::Primary));

        $response = anu()->record->installRecord($record);

        $this->returnJson(array(
            'success' => $response
        ));
    }
}