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
        //$record = craft()->record->getRecordByName('page');
        anu()->record->installRecord('page');

        /*foreach ($records as $record){
            $record->installRecord();
        }*/
        anu()->template->render('home.twig', array(
            'records' => $records
        ));
    }


    /**
     * @param $recordId
     */
    public function installRecord(){
        $recordId = anu()->request->getValue('recordId');
        $this->requireAjaxRequest();
        if($recordId){

        }
    }
}