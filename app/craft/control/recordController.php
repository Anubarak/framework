<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Craft;


class recordController extends baseController
{
    public function getContent(){
        $records = craft()->record->loadAllRecords();


        /*foreach ($records as $record){
            $record->installRecord();
        }*/
        craft()->template->render('test.twig', array(
            'records' => $records
        ));
    }

    /**
     * @param $recordId
     */
    public function installRecord(){
        $recordId = craft()->request->getValue('recordId');
        $this->requireAjaxRequest();
        if($recordId){

        }
    }
}