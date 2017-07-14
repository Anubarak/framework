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
    private static $paths = '';

    /**
     * @return app
     */
    public static function app(){
        if(!craft::$_app){
            include BASE . 'config.php';
            Craft::$paths = $config['paths'];
            craft::$_app = new app();
            craft::$_app->init();
        }
        return craft::$_app;
    }

    /**
     * @return string
     */
    public static function getNameSpace(){
        return Craft::$paths['namespace'];
    }

    /**
     * @return string
     */
    public static function getCoreServiceDirectory(){
        return Craft::$paths['coreServiceDirectory'];
    }

    /**
     * @return string
     */
    public static function getRecordPluginDirectory(){
        return Craft::$paths['pluginRecordDirectory'];
    }

    /**
     * @return string
     */
    public static function getCoreRecordDirectory(){
        return Craft::$paths['coreRecordDirectory'];
    }

    /**
     * @return string
     */
    public static function getPluginServiceDirectory(){
        return Craft::$paths['pluginServiceDirectory'];
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
 * @property configService                          $config
 * @property recordService                          $record
 * @property database                               $database
 *
 * @package Craft
 */
class app{

    public $database = null;

    public function __construct(){

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();

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

    public function init(){
        $this->template->init();
        $this->database = new database(craft()->config->get('database'));
    }
}

/**
 * @return app
 */
function craft()
{
    return Craft::app();
}