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
}