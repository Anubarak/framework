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


    /**
     *
     */
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
            $entry = Anu::getClassByName($className, 'Model', true);
            anu()->$className->populateModel($data, $entry);
            $matrix = anu()->request->postVar('matrix', null);
            $matrixArray = array();
            if($matrix && is_array($matrix)){
                foreach ($matrix as $matrixKey => $matrixType){
                    foreach ($matrixType as $matrixElement){
                        $singleMatrix = new matrixModel();
                        $matrixAttributes = anu()->matrix->getMatrixByName('testMatrix')->defineAttributes()[$matrixElement['title']];
                        $content = array();
                        foreach ($matrixAttributes as $k => $v){
                            if(array_key_exists($k, $matrixElement)){
                                $content[$k] = $matrixElement[$k];
                            }
                        }
                        $singleMatrix->content = json_encode($content);
                        $singleMatrix->id = 1;
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


            $this->returnJson($response);

    }

    /**
     * Render Template to add an entry
     *
     * @param $parameter array
     */
    public function add($parameter){
        anu()->template->render('forms/edit.twig', array(
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
        $this->requireLogin();

        if(count($parameter) != 2){
            return null;
        }
        $class = $parameter[0];
        if($entry = anu()->$class->getEntryById($parameter[1])){
            anu()->template->render('forms/edit.twig', array(
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
        $index = anu()->request->getValue('index');
        $atributes = $entryModel->defineAttributes();
        $matrixModel = anu()->matrix->getMatrixByName($atributes[$attributeKey][1]);
        $matrixAttributes = $matrixModel->defineAttributes();

        $html = '';
        foreach ($matrixAttributes[$matrixKey] as $k => $v){
            $variables = array(
                'index'         => $index,
                'key'           => $k,
                'attribute'     => $v,
                'entry'         => $matrixModel,
                'class'         => Anu::getClassName($this)
            );

            $html .= anu()->template->render('forms/matrix/' . $v[0] . '.twig', $variables, true);
        }

        $this->returnJson(array(
            "html" => $html,
            'attributes'    => $matrixAttributes[$matrixKey]
        ));
    }
}