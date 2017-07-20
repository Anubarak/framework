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
    public $id = 0;

    public function __construct(){
        $attributes = $this->defineAttributes();
        foreach ($attributes as $k => $v){
            $this->$k = null;
        }
    }

    /**
     * Returns all Data of the model
     *
     * @return array
     */
    public function getData(){
        $values = array();
        $attributes = $this->defineAttributes();
        foreach ($attributes as $k => $v){
            $values[$k] = $this->$k;
        }
        return $values;
    }

    /**
     * @param $value
     * @return null
     */
    public function getAttribute($value){
        if(isset($this->$value)){
            return $this->$value;
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
            if(array_key_exists($key, $this->defineAttributes())){
                $this->$key = $data;
                return true;
            }
            return false;
        }

        if(is_array($data)){
            foreach ($data as $k => $v){
                $this->$k = $v;
            }
            return true;
        }

        return false;
    }

    public  function getErrors(){
        return $this->errors;
    }

    /**
     * clear Errors from Entry
     */
    public  function clearErrors(){
        $this->errors = null;
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