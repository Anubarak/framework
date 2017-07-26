<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 17:08
 */

namespace Anu;


use function Sodium\crypto_aead_aes256gcm_is_available;

class baseController
{
    /**
     *
     */
    public function getContent(){
        /*
        $answer = craft()->answer->getEntryById(1);
        $questionService = craft()->question;
        $question = $questionService->getEntryById(1);
        $question->test_id = array($answer);
        $data = $question->getData();
        $data['text'] = "bitte2";*/
        //$question->setData($data);
        //craft()->template->addJsCode('alert("test");');
        //craft()->question->saveEntry($question);

        //$record = anu()->record->getRecordByName('answer');

        //anu()->record->installRecord("matrix", true);
        //anu()->record->installRecord("question");

        //anu()->record->installRecord("userPermission", true);
        //anu()->record->installRecord("user", true);

        /*
                echo "<pre>";
                var_dump(anu()->user->login(null, 'Bludb@Blaa.ded', 'Warpten5000'));
                echo "</pre>";
                die();
                $user = anu()->user->getUserById(1);
                echo "<pre>";
                var_dump($user);
                echo "</pre>";
                die();
        */

        //anu()->user->login("blub", "anubarak1993@gmail.com");
        //anu()->user->login("blub", "Friedl@Uwe.de");
        //$user = anu()->user->getUserById(2);
        //$loggedIn = anu()->user->login('Fischer@Fisch.de', 'Fischer@Fisch.de');
        //anu()->user->addPermission(anu()->user->getCurrentUser(), 'question.update');

        //$matrix = anu()->matrix->getMatrixById(4);
        //$matrix->position = 0;
        //anu()->matrix->saveMatrix($matrix);

        anu()->template->render('pages/home.twig', array(
            'title'       => "test Title",
            'headline'    => "headline",
            'subheadline' => "subline",
            'navigation'  => "test",
        ));
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
        if( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')  || anu()->request->isAngularRequest()) {
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