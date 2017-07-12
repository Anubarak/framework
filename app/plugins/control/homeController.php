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

        //craft()->question->saveEntry($question);

        //craft()->question->deleteEntry($question);
    }
}