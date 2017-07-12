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
        $records = baseRecord::getAllRecords();


        /*foreach ($records as $record){
            $record->installRecord();
        }*/
        craft()->template->render('records.twig', array(
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