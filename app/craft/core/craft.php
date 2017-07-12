<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 11:06
 *
 */

namespace Craft;

class Craft
{
    public static $_app = null;
    public static $entryService;

    public function __construct()
    {
        craft::$_app = new app();
    }

    /**
     * @return app
     */
    public static function app(){
        if(!craft::$_app){
            craft::$_app = new app();
        }
        return craft::$_app;
    }

    /**
     * @return string
     */
    public static function getNameSpace(){
        return 'craft\\';
    }

    /**
     * @return string
     */
    public static function getCoreServiceDirectory(){
        return BASE . 'app\craft\service';
    }

    /**
     * @return string
     */
    public static function getRecordPluginDirectory(){
        return BASE . 'app\plugins\record';
    }

    /**
     * @return string
     */
    public static function getPluginServiceDirectory(){
        return BASE . 'app\plugins\service';
    }

    public static function getTemplatePath(){
        return BASE . 'app\templates';
    }

    /**
     * @param $class
     */
    public static function getClassByName($class, $extention = null){
        if(is_string($class)){
            $type = ($extention)? $extention : "Service";
            return Craft::getNameSpace() . $class . $type;
        }else{
            $className = get_class($class);
            $match = array();
            preg_match('/Craft\\\([a-zA-Z0-9-]*)(Service|Model)/',$className, $match);
            if(count($match) > 1){
                $type = ($extention)? $extention : $match[2];
                return Craft::getNameSpace() . $match[1] . $type;
            }
        }

        return false;
    }

    /**
     * @param $class
     * @return bool|mixed
     */
    public static function getClassName($class){
        $className = get_class($class);
        $match = array();
        preg_match('/Craft\\\([a-zA-Z0-9-]*)(Service|Model)/',$className, $match);
        if(count($match) > 1){
            return $match[1];
        }
        return false;
    }
}

/**
 * Class app
 * @property entryService                           $entry
 * @property questionService                        $question
 * @property answerService                          $answer
 * @property templateService                        $template
 * @property requestService                         $request
 *
 * @package Craft
 */
class app{

    public $database = null;

    public function __construct(){

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();


        $this->database = new database([
            'database_type' => 'mysql',
            'database_name' => 'anubarak16',
            'server' => 'localhost',
            'username' => 'root',
            'password' => ''
        ]);

        $dirs = array(
            Craft::getCoreServiceDirectory(), Craft::getPluginServiceDirectory()
        );

        foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2){
                for($i = 2; $i < $countFiles; $i++){
                    require_once $dir . '\\' . $files[$i];
                    $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
                    $magicOperator = preg_replace('/\\Service.[^.\\s]{3,4}$/', '', $files[$i]);
                    $withNameSpace = 'craft\\' . $withoutExt;
                    $this->$magicOperator = new $withNameSpace();
                }
            }
        }
    }

    public function test(){
        return "testregreregergre";
    }
}

/**
 * @return app
 */
function craft()
{
    return Craft::app();
}