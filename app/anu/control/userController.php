<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 03.08.2017
 * Time: 15:20
 */

namespace Anu;


class userController extends baseController
{

    public function login(){
        $data = (array)anu()->request->getValue('user');
        $user = anu()->user->login($data['username'], $data['username'], $data['password']);
        $response = array();
        if($user === true){
            $response['success']    = true;
            $response['message']    = 'Erfolgreich eingeloggt';
        }else{
            $response['success'] = false;
            $response['errors'] = $user;
            $response['message']    = 'Fehler bei der anmeldung';
        }

        if($this->isAjaxRequest()){
            $this->returnJson($response);
        }else{
            return $response;
        }
    }

    public function reset(){
        //TODO reset
        echo "<pre>";
        var_dump("res");
        echo "</pre>";
        die();
    }

    public function register(){
        $user = new userModel();
        $data = (array)anu()->request->getValue('user');
        $user->title = $data['username'];
        $user->password = $data['password'];
        $user->repeatPassword = $data['repeatPassword'];
        $user->email = $data['email'];

        $response = array();
        if(anu()->user->saveUser($user)){
            $response['success']    = true;
            $response['message']    = 'Erfolgreich Registriert';
        }else{
            $response['success'] = false;
            $response['errors'] = $user->getErrors();
            $response['message']    = 'Fehler bei der Registrierung';
        }
        if($this->isAjaxRequest()){
            $this->returnJson($response);
        }else{
            return $response;
        }
    }

}