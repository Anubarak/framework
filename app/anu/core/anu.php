<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 11:06
 *
 */

namespace Anu;

class Anu
{
    public static $_app = null;
    public static $entryService;
    private static $paths = '';

    /**
     * @return app
     */
    public static function app(){
        if(!anu::$_app){
            include BASE . 'config.php';
            Anu::$paths = $config['paths'];
            Anu::$_app = new app();
            Anu::$_app->init();
        }
        return anu::$_app;
    }

    /**
     * @return string
     */
    public static function getNameSpace(){
        return Anu::$paths['namespace'];
    }

    /**
     * @return string
     */
    public static function getCoreServiceDirectory(){
        return Anu::$paths['coreServiceDirectory'];
    }

    /**
     * @return string
     */
    public static function getRecordPluginDirectory(){
        return Anu::$paths['pluginRecordDirectory'];
    }

    /**
     * @return string
     */
    public static function getCoreRecordDirectory(){
        return Anu::$paths['coreRecordDirectory'];
    }

    /**
     * @return string
     */
    public static function getPluginServiceDirectory(){
        return Anu::$paths['pluginServiceDirectory'];
    }


    /**
     * @param $class
     */
    public static function getClassByName($class, $extention = null){
        if(is_string($class)){
            $type = ($extention)? $extention : "Service";
            return Anu::getNameSpace() . $class . $type;
        }else{
            $className = get_class($class);
            $match = array();
            preg_match('/Anu\\\([a-zA-Z0-9-]*)(Service|Model)/',$className, $match);
            if(count($match) > 1){
                $type = ($extention)? $extention : $match[2];
                return Anu::getNameSpace() . $match[1] . $type;
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
        preg_match('/Anu\\\([a-zA-Z0-9-]*)(Service|Model)/',$className, $match);
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
 * @property pageService                            $page
 * @property database                               $database
 *
 * @package Anu
 */
class app{

    public $database = null;

    public function __construct(){

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();

        $dirs = array(
            Anu::getCoreServiceDirectory(), Anu::getPluginServiceDirectory()
        );

        foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2){
                for($i = 2; $i < $countFiles; $i++){
                    require_once $dir . '\\' . $files[$i];
                    $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
                    $magicOperator = preg_replace('/\\Service.[^.\\s]{3,4}$/', '', $files[$i]);
                    $withNameSpace = 'anu\\' . $withoutExt;
                    $this->$magicOperator = new $withNameSpace();
                }
            }
        }
    }

    public function init(){
        $this->template->init();
        $this->database = new database(anu()->config->get('database'));
    }
}

/**
 * @return app
 */
function anu()
{
    return Anu::app();
}