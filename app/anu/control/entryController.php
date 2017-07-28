<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Anu;


class entryController extends baseController
{
    public function validateSlug(){
        if($class = anu()->request->getValue('class')){
            $slug = anu()->request->getValue('slug');
            $inUse = anu()->database->has(anu()->$class->getTable(), array(
                'slug' => $slug,
                anu()->$class->getPrimaryKey() . "[!]" => anu()->request->getValue('id')
            ));

            $this->returnJson(array(
                'isValid' => !$inUse
            ));
        }
    }


    public function saveTree(){
        $entry = anu()->request->getValue('entry');
        $parentId = anu()->request->getValue('parentId');
        $entry->parent_pid = array($parentId);
        $className = $entry->class;
        if(!anu()->$className->saveEntry($entry)){
            $this->returnJson($entry->getErrors());
        }
        $this->returnJson(true);
    }

    /**
     * save entry
     */
    public function save(){
        if($this->isAjaxRequest()){
            $entry = anu()->request->postVar('entry');
            $class = $entry->class;
            if(!anu()->$class->saveEntry($entry)){
                $this->returnJson($entry->getErrors());
            }

            $this->returnJson(true);
        }
        //craft()->question->deleteEntry($question);
    }
}