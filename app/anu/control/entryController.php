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
            $id = anu()->request->getValue('id', 0);
            $record = Anu::getRecordByName($class);
            $inUse = anu()->database->has($record->tableName, array(
                'slug' => $slug,
                $record->primary_key . "[!]" => $id
            ));

            $this->returnJson(array(
                'isValid' => !$inUse
            ));
        }
    }


    /**
     *
     */
    public function saveTree(){
        $entryData = anu()->request->getValue('entry');
        $data = (array)anu()->request->getValue('data');
        $class = $entryData['class'];
        $entry = Anu::getClassByName($class, 'Model', true);
        anu()->$class->populateModel($entryData, $entry);
        $parentId = $data['parentId'];
        $position = $data['position'];
        $oldPosition = $data['oldPosition'];
        $oldIds = isset($data['sourceIds'])? $data['sourceIds'] : null;
        $attributes = $entry->defineAttributes();
        foreach ($attributes as $k => $v){
            if($v[0] == AttributeType::Position){
                $entry->$k = $position;
                $entry->oldPosition = $oldPosition;
                $entry->oldSiblings = $oldIds;
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
            $data = anu()->request->postVar('entry');
            $className = $data['class'];
            $entry = Anu::getModelByName($className);
            anu()->$className->populateModel($data, $entry);

            $matrix = anu()->request->postVar('matrix', null);
            $matrixArray = array();
            if($matrix && is_array($matrix) && count($matrix)){
                foreach ($matrix as $matrixKey => $matrixType){
                    foreach ($matrixType as $matrixElement){
                        if($matrixElement['id']){
                            $singleMatrix = anu()->matrix->getMatrixById($matrixElement['id']);
                        }else{
                            $singleMatrix = new matrixModel('matrix');
                        }
                        $matrixAttributes = anu()->matrix->getMatrixByName($matrixElement['matrixId'])->defineAttributes()[$matrixElement['type']];
                        $content = array();
                        foreach ($matrixAttributes as $k => $v){
                            if(array_key_exists($k, $matrixElement)){
                                switch ($v[0]){
                                    case AttributeType::Relation:
                                        $singleMatrix->$k = $matrixElement[$k];
                                        $content[$k] = $matrixElement[$k];
                                        break;
                                    default:
                                        $content[$k] = $matrixElement[$k];
                                        break;
                                }
                            }else{
                                $content[$k] = "";
                            }
                        }
                        $singleMatrix->type = $matrixElement['type'];
                        $singleMatrix->handle = $matrixElement['matrixId'];
                        $singleMatrix->content = json_encode($content);
                        $matrixArray[] = $singleMatrix;
                    }
                    $entry->$matrixKey = $matrixArray;
                }
            }

            $response = array();
            if(!$id = anu()->$className->saveEntry($entry)){
                $response['success'] = false;
                $response['errors'] = $entry->getErrors();
            }else {
                $response['success'] = true;
                $response['id']     = $id;
            }

            anu()->database->debugError();
            $this->returnJson($response);

    }

    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function add($parameter){
        anu()->template->render('admin/forms/edit.twig', array(
            'form' => $parameter[0],
            'entry' => null,
        ));
        exit();
    }

    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function edit($parameter){
        //$this->requireLogin();

        $entry = null;
        if($parameter){
            if(ctype_digit($parameter[count($parameter)-1])){
                $class = $parameter[0];
                $entry = anu()->$class->getEntryById($parameter[count($parameter)-1]);
            }
        }
        if(!$entry){
            $entry = Anu::getModelByName($parameter[0]);
            $class = $parameter[0];
            anu()->$class->populateModel(null, $entry);
        }

        if($entry){
            anu()->template->render('admin/forms/edit.twig', array(
                'form' => $parameter[0],
                'entry' => $entry,
                'class' => $class
            ));
            exit;
        }
        return null;
    }

    public function getMatrixHtml(){
        $entryModel = Anu::getClassByName(anu()->request->getValue('entryType'), 'Model', true);
        $matrixKey = anu()->request->getValue('matrixKey');
        $attributeKey = anu()->request->getValue('attributeKey');
        $atributes = $entryModel->defineAttributes();
        $matrixModel = anu()->matrix->getMatrixByName($atributes[$attributeKey][1]);
        $matrixAttributes = $matrixModel->defineAttributes();

        $this->returnJson(array(
            'attributes'    => $matrixAttributes[$matrixKey]
        ));
    }


    public function getForm(){
        $class = anu()->request->getValue('class');
        $entry = Anu::getClassByName($class, "Model", true);
        //just to add relationModels
        anu()->$class->populateModel(null, $entry);

        //store titles for modules...
        $attributes = $entry->defineAttributes();
        foreach ($entry->defineAttributes() as $k => $v){
            if($v[0] == AttributeType::Relation && $entry->$k){
                $entry->$k = $entry->$k->find(null, true);
            }

            if($v[0] == AttributeType::Bool){
                $entry->$k = property_exists($entry, $k)? (bool)$entry->$k : false;
            }

            if($v[0] == AttributeType::Position){
                $entry->$k = null;
            }
            if($v[0] == AttributeType::Matrix){
                $matrixAttributes = anu()->matrix->getMatrixByName($v[1])->defineAttributes();
                $attributes[$k]['attributes'] = $matrixAttributes;
                $matrixArray = array();
                $index = 0;
                foreach ($entry->$k as $matrix){
                    $matrixArray[$index] = json_decode($matrix->content, true);
                    $matrixArray[$index]['type'] = $matrix->type;
                    $matrixArray[$index]['title'] = $matrix->type;
                    $matrixArray[$index]['attributes'] = $matrixAttributes[$matrix->type];
                    $matrixArray[$index]['matrixId']    = $v[1];
                    $matrixArray[$index]['id']    = $matrix->id;
                    $index++;
                }
                $entry->$k = $matrixArray;
            }
        }

        $entry->attributes = $attributes;


        $template = anu()->template->render('admin/forms/index.twig', array(
            'entry' => $entry,
            'attributes' => $entry->defineAttributes(),
            'inModal' => true
        ), true);

        $this->returnJson(array(
            'template'  => $template,
            'entry'     => $entry
        ));
    }
}