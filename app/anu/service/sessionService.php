<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 13.07.2017
 * Time: 11:31
 */

namespace Anu;


class sessionService
{
    public function __construct()
    {

    }

    public function get($key,$defaultValue=null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    public function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public function clear(){
        session_destroy();
    }

}