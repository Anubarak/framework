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
        $data = (array)anu()->request->getValue('data');

        $parentId = $data['parentId'];
        $position = $data['position'];
        $oldPosition = $data['oldPosition'];
        $oldIds = isset($data['sourceIds'])? $data['sourceIds'] : null;
        $attributes = $entry->defineAttributes();
        foreach ($attributes as $k => $v){
            if($v[0] == AttributeType::Position){
                $entry->$k = $position;
                $entry->oldPosition = $oldPosition;
                $entry->oldIds = $oldIds;
                if(array_key_exists("relatedField", $v)){
                    $key = $v['relatedField'];
                    $entry->$key = array($parentId);
                }
            }
        }

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
            if(!$id = anu()->$class->saveEntry($entry)){
                $this->returnJson($entry->getErrors());
            }

            $this->returnJson(array('success' => true, 'id' => $id));
        }
        //craft()->question->deleteEntry($question);
    }

    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function add($parameter){
        anu()->template->render('forms/edit.twig', array(
            'form' => $parameter[0],
            'entry' => null
        ));
        exit();
    }

    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function edit($parameter){
        $this->requireLogin();

        if(count($parameter) != 2){
            return null;
        }
        $class = $parameter[0];
        if($entry = anu()->$class->getEntryById($parameter[1])){
            anu()->template->render('forms/edit.twig', array(
                'form' => $parameter[0],
                'entry' => $entry
            ));
            exit;
        }
        return null;
    }
}