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

    public function save(){

        $question = anu()->question->generateEntryFromPost();


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
        $answer = new answerModel();
        anu()->answer->generateEntryFromPost($answer);

        anu()->answer->saveEntry($answer);
        anu()->database->debugError();

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
}