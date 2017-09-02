<?php

/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 17:04
 */
namespace Anu;

class homeController extends baseController
{
    public function getContent()
    {
        anu()->template->render('pages/home.twig', array(
            'title'       => "test Title",
            'headline'    => "headline",
            'subheadline' => "subline",
            'navigation'  => "test",
        ));
    }

    public function test($template = 'admin/forms/index.twig'){
        $entry = anu()->question->getElementById(2);

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


        $template = anu()->template->render($template, array(
            'entry' => $entry,
            'attributes' => $entry->defineAttributes()
        ));

        $this->returnJson(array('template' => $template));
    }

    public function blub(){
        $entry = anu()->question->getElementById(2);

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
        anu()->template->addAnuJsObject($entry, 'entry');

        anu()->template->addJsCode('
            var entry = ' . json_encode($entry) . ';
        ');
        anu()->template->addJsCode('
            var attributes = ' . json_encode($entry->defineAttributes()) . ';
        ');

        anu()->template->render('pages/test.twig', array());
        die();
    }

    public function save(){
        $question = anu()->question->generateEntryFromPost();
        $answer = anu()->answer->getEntryById(3);
        $question->test_id = $answer;

        anu()->question->saveEntry($question);
        anu()->database->debugError();
        if($question->getErrors()){
            echo "<pre>";
            var_dump($question->getErrors());
            echo "</pre>";
            die();
        }
        //craft()->question->deleteEntry($question);
    }

    public function saveQuestion(){
        $answer = anu()->answer->generateEntryFromPost();

        anu()->answer->saveEntry($answer);
        anu()->database->debugError();

        if($answer->getErrors()){
            echo "<pre>";
            var_dump($answer->getErrors());
            echo "</pre>";
            die();
        }
        //craft()->question->deleteEntry($question);
    }

    public function savePage(){
        $page = anu()->page->generateEntryFromPost();
        anu()->page->saveEntry($page);

        anu()->database->debugError();

        if($page->getErrors()){
            echo "<pre>";
            var_dump($page->getErrors());
            echo "</pre>";
            die();
        }
    }


    public function saveAsset(){
        $asset = anu()->asset->generateEntryFromPost();
        anu()->asset->saveAsset($asset);
        anu()->database->debugError();

        if($asset->getErrors()){
            echo "<pre>";
            var_dump($asset->getErrors());
            echo "</pre>";
            die();
        }
        echo "<pre>";
        var_dump($asset);
        echo "</pre>";
        die();
    }

    public function saveUser(){
        $user = anu()->user->generateEntryFromPost();

        anu()->user->saveUser($user);
        anu()->database->debugError();

        if($user->getErrors()){
            echo "<pre>";
            var_dump($user->getErrors());
            echo "</pre>";
            die();
        }
        echo "<pre>22";
        var_dump($user);
        echo "</pre>";
        die();
    }

    /**
     * @param $params
     */
    public function onBeforeSaveEntry($params){
        echo "<pre>";
        var_dump("blub");
        echo "</pre>";
        die();
    }
}