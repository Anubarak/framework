<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 13.07.2017
 * Time: 10:58
 */

namespace Craft;


class configService
{
    private $config = array();

    public function __construct()
    {
        include BASE . 'config.php';
        if(isset($config) && is_array($config)){
            $this->config = $config;
        }
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key){
        return (isset($this->config[$key]))? $this->config[$key] : null;
    }
}