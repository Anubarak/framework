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
        $this->getVar = $_GET;
        $this->postVar = $_POST;
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

    public function getValue($var = null){
        return $this->_getValue($this->request, $var);
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



    private function castAngular(){
        $entryStd = $this->angularRequest->entry;
        $entry = Anu::getClassByName($entryStd->class, "Model", true);
        $entry->setData((array)$entryStd);
        foreach ($entry as $k => $data){
            if(is_object($data)){
                $criteriaName = Anu::getClassByName($data->class, "Model");
                /* @var elementCriteriaModel */
                $serviceClass = $data->service->class;
                $criteria = new $criteriaName(anu()->$serviceClass);
                $criteria->storeIds($data->ids);
                $criteria->relatedTo = (array)$data->relatedTo;
                $entry->$k = $criteria;
            }
        }
        $this->angularRequest->entry = $entry;
    }

    public function process(){
        $this->angularRequest = json_decode(file_get_contents("php://input"));
        if($this->angularRequest){
            $this->castAngular();
            $this->setPost($this->angularRequest);
        }
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
            $i = (count($arrRoute) == 2)? 0 : 1;
            $controller = $arrRoute[$i];
            $function = $arrRoute[$i+1];
            if(count($arrRoute)){
                $className = Anu::getClassByName($controller, "Controller");
                $class = new $className();
                $class->$function();
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
                if(!anu()->record->getRecordByName($entry)){
                    throw new \Exception($entry . ' is not installed');
                }
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
    public function isAngularRequest(){
        return $this->angularRequest? true : false;
    }
}