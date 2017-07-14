<?php

/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 17:04
 */
namespace Craft;

class homeController extends baseController
{
    public function getContent()
    {

        $criteria = craft()->question->getCriteria();
        $criteria->LIMIT =  1;
        //$entries = $criteria->find();

        craft()->template->render('pages/home.twig', array(
            'title'       => "test Title",
            'headline'    => "headline",
            'subheadline' => "subline",
            'navigation'  => "test",
        ));
    }

    public function save(){
        $question = new questionModel();
        craft()->question->setDataFromPost($question);
        craft()->question->saveEntry($question);
        craft()->database->debugError();
        //craft()->question->deleteEntry($question);
    }

    public function saveQuestion(){
        $answer = new questionModel();
        craft()->entry->setDataFromPost($answer);

        craft()->entry->saveEntry($answer);
        craft()->database->debugError();
        echo "<pre>";
        var_dump(craft()->database->error());
        echo "</pre>";
        die();
        //craft()->question->deleteEntry($question);
    }
}