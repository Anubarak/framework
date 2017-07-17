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

        $criteria = anu()->question->getCriteria();
        $criteria->LIMIT =  1;
        //$entries = $criteria->find();

        anu()->template->render('pages/home.twig', array(
            'title'       => "test Title",
            'headline'    => "headline",
            'subheadline' => "subline",
            'navigation'  => "test",
        ));
    }

    public function save(){
        $question = new questionModel();
        anu()->question->setDataFromPost($question);
        anu()->question->saveEntry($question);
        anu()->database->debugError();
        //craft()->question->deleteEntry($question);
    }

    public function saveQuestion(){
        $answer = new answerModel();
        anu()->answer->setDataFromPost($answer);

        anu()->answer->saveEntry($answer);
        anu()->database->debugError();

        //craft()->question->deleteEntry($question);
    }

    public function savePage(){
        $page = new pageModel();
        anu()->page->setDataFromPost($page);
        anu()->page->saveEntry($page);

        anu()->database->debugError();

        if($page->getErrors()){
            echo "<pre>";
            var_dump($page->getErrors());
            echo "</pre>";
            die();
        }
    }
}