<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 10:44
 */

namespace Anu;


class baseModel
{

    private $errors = null;
    private $data = array();
    public $id = 0;

    public function __construct(){
        $attributes = $this->defineAttributes();
        foreach ($attributes as $k => $v){
            $this->data[$k] = null;
        }
    }

    /**
     * Returns all Data of the model
     *
     * @return array
     */
    public function getData(){
        return $this->data;
    }

    /**
     * @param $value
     * @return null
     */
    public function getAttribute($value){
        if(isset($this->data[$value])){
            return $this->data[$value];
        }
        return null;
    }

    /**
     * Store Data to the modle
     *
     * @param $data
     * @param null $key
     * @return bool
     */
    public function setData($data, $key = null){
        if(!is_array($data) && $key){
            if(array_key_exists($key, $this->data)){
                $this->data[$key] = $data;
                return true;
            }else{
                return false;
            }
        }

        if(is_array($data)){
            $this->data = $data;
            return true;
        }

        return false;
    }

    public  function getErrors(){
        return $this->errors;
    }

    /**
     * @param $attribute
     * @param $message
     * @param array $param
     * @throws \Exception
     */
    public function addError($attribute, $message, $param = array()){
        if(!$attribute || !$message){
            throw new \Exception("addError, no message or attribute given");
        }

        if(!$this->errors) {
            $this->errors = array();
        }

        if($param){
            $message = Anu::parse($message, $param);
        }
        $this->errors[$attribute] = $message;
    }

    public function defineAttributes()
    {
        return array(

        );
    }
}