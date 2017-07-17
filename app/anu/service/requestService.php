<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 10.07.2017
 * Time: 16:51
 */

namespace Anu;

/**
 * Class requestService
 * @package Anu
 */
class requestService
{
    private $getVar = null;
    private $postVar = null;

    public function __construct()
    {
        $this->getVar = $_GET;
        $this->postVar = $_POST;
    }


    public function getValue($var = null){
        $request = array_merge($this->getVar, $this->postVar);
        return $this->_getValue($request, $var);
    }

    public function getVar($var = null){
        return $this->_getValue($this->getVar, $var);
    }

    public function postVar($var = null){
        return $this->_getValue($this->postVar, $var);
    }

    private function _getValue($array, $key){
        if(!$key){
            return $array;
        }
        if(isset($array[$key])){
            return $array[$key];
        }
        return null;
    }

    public function process(){
        $page = $this->getValue('p');
        $stat = $this->getValue('s');
        $entry = $this->getValue('e');
        $slug = $this->getValue('slug');

        //check for actions...
        if($action = $this->getValue('action')){
            $arrRoute = explode('/', $action);
            if(count($arrRoute)){
                $className = Anu::getClassByName($arrRoute[0], "Controller");
                $class = new $className();
                $function = $arrRoute[1];
                $class->$function();
            }
        }


        if(!$page && !$stat && (!$entry || !$slug)){
            $baseController = new baseController();
            $baseController->getContent();
        }

        if($entry && $slug){
            if(isset(anu()->$entry) && is_object(anu()->$entry)){
                if(!anu()->record->getRecordByName($entry)){
                    throw new \Exception($entry . ' is not installed');
                }
                anu()->$entry->renderEntryBySlug($slug);
            }else{
                throw new \Exception('could not found Service with Name = ' . $entry );
            }
        }

        if($page){
            $controllerName = Anu::getNameSpace() . $page . "Controller";
            if(class_exists($controllerName)){
                $class = new $controllerName();
                if($stat && method_exists($class, $stat)){
                    $class->$stat();
                }else{
                    $class->getContent();
                }
            }
        }
    }
}