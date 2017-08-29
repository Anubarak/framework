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
        $records = anu()->record->loadAllRecords();

        anu()->template->addAnuJsObject($records, 'records');

        anu()->template->render('admin/record/index.twig', array(
            'records' => $records
        ));
    }


    /**
     * @param $recordId
     */
    public function toggleInstallation(){
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
}