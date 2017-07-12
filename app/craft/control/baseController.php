<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 17:08
 */

namespace Craft;


class baseController
{
    public function __construct()
    {

    }

    /**
     *
     */
    public function getContent(){
        $answer = craft()->answer->getEntryById(1);

        $questionService = craft()->question;
        $question = $questionService->getEntryById(1);
        $question->test_id = array($answer);
        $data = $question->getData();
        $data['text'] = "bitte2";
        $question->setData($data);

        //craft()->question->saveEntry($question);
        craft()->template->render('pages/home.twig', array(
            'title'       => "test Title",
            'headline'    => "headline",
            'subheadline' => "subline",
            'navigation'  => "test",
        ));
    }

    public function test(){
        if($this->isAjaxRequest()){
            $array = array(
                'somedata' => "please word",
                'somedata2' => 2
            );
            $this->returnJson($array);
        }
    }


    /**
     * @throws \Exception
     */
    public function requireAjaxRequest(){
        if(!$this->isAjaxRequest()){
            throw new \Exception("This action requires an Ajax Request");
        }
    }

    /**
     * @return bool
     */
    public function isAjaxRequest(){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        return false;
    }


    /**
     * @param array $var
     */
    public function returnJson($var = array())
    {
        echo json_encode($var);
        exit();
    }
}