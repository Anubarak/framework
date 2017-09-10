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
    private $getVar = array();
    private $postVar = array();
    private $request = array();
    private $angularRequest = null;

    public function __construct()
    {
        if(is_array($_POST) && count($_POST)){
            foreach ($_POST as $key => $post){
                $string = json_decode($post ,true);
                $this->postVar[$key] = (json_last_error() == JSON_ERROR_NONE)? (array)$string : $post;
            }
        }
        $this->getVar = $_GET;
        $this->request = array_merge($this->getVar, $this->postVar);
    }

    /**
     * @param $values
     */
    public function setPost($values){
        foreach ($values as $k => $v){
            $this->postVar[$k] = $v;
        }
        $this->request = array_merge($this->getVar, $this->postVar);
    }

    public function getValue($var = null, $default = null){
        return $this->_getValue($this->request, $var, $default);
    }

    public function getVar($var = null, $default = null){
        return $this->_getValue($this->getVar, $var, $default);
    }

    public function postVar($var = null, $default = null){
        return $this->_getValue($this->postVar, $var, $default);
    }

    private function _getValue($array, $key, $default = null){
        if(!$key){
            return $array;
        }
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }

    public function process(){
        /*$this->angularRequest = json_decode(file_get_contents("php://input"));

        if($this->angularRequest){
            $this->castAngular();
            $this->setPost($this->angularRequest);
        }
        */
        $page = $this->getValue('p');
        $stat = $this->getValue('s');
        $entry = $this->getValue('e');
        $slug = $this->getValue('slug');
        $asset = $this->getValue('asset');

        //render only asset....
        if($asset){
            anu()->asset->display($asset);
        }

        //check for actions... -> ajax request
        if($action = $this->getValue('action')){
            $arrRoute = explode('/', $action);
            $i = ($arrRoute[0] !== 'ajax')? 0 : 1;
            $controller = $arrRoute[$i];
            $function = $arrRoute[$i+1];
            if($arrRoute[0] !== 'ajax'){
                $className = Anu::getClassByName($controller, "Controller");
                if($className){
                    $class = new $className();
                }else{
                    $class = new entryController();
                }

                if(count($arrRoute) >= 3){
                    $parameter = $arrRoute;//array_slice($arrRoute, 2, count($arrRoute)-1);
                    if(method_exists($class, $function)){
                        if(!is_array($parameter)){
                            $parameter = array($parameter);
                        }
                        $class->$function($parameter);
                    }
                }else{
                    $class->$function(null);
                }
            }elseif (count($arrRoute) == 3 && $arrRoute[0] === 'ajax'){
                $ajaxController = new ajaxController();
                $ajaxController->service($arrRoute[1], $arrRoute[2]);
            }
        }

        //if nothing is defined => just run default options
        if(!$page && !$stat && (!$entry || !$slug)){
            $baseController = new baseController();
            $baseController->getContent();
        }

        //go to the detail entry page if parameters set
        if($entry && $slug){
            if(isset(anu()->$entry) && is_object(anu()->$entry)){
                //if(!anu()->record->getRecordByName($entry)){
                //    throw new \Exception($entry . ' is not installed');
                //}
                anu()->$entry->renderEntryBySlug($slug);
            }else{
                throw new \Exception('could not found Service with Name = ' . $entry );
            }
        }

        //get Controller Content
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

    /**
     * @return bool
     */
    public function isAjaxRequest(){
        if( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            return true;
        }
        return false;
    }
}