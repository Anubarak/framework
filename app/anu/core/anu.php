<?php

namespace Anu;
/**
 * Class app
 *
 * Include your own classes here for autocompleation in the same way as I have done it
 *
 * @property entryService                           $entry
 * @property fieldService                           $field
 * @property userService                            $user
 * @property assetService                           $asset
 * @property questionService                        $question
 * @property answerService                          $answer
 * @property templateService                        $template
 * @property requestService                         $request
 * @property configService                          $config
 * @property recordService                          $record
 * @property tabService                             $tab
 * @property sessionService                         $session
 * @property database                               $database
 * @property matrixService                          $matrix
 *
 * @package Anu
 */
class app{

    public $database = null;
    public $plugins;
    private $initServices = array();

    public function __construct(){
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();

        $dirs = array(
            Anu::getCoreServiceDirectory()/*, Anu::getPluginServiceDirectory()*/
        );

        foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2){
                for($i = 2; $i < $countFiles; $i++){
                    if(strpos($files[$i], 'Service') !== false) {
                        require_once $dir . '\\' . $files[$i];
                        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
                        $magicOperator = preg_replace('/\\Service.[^.\\s]{3,4}$/', '', $files[$i]);
                        $withNameSpace = 'anu\\' . $withoutExt;
                        $this->$magicOperator = new $withNameSpace();
                        if (method_exists($this->$magicOperator, "init")) {
                            $this->initServices[] = $magicOperator;
                        }
                    }
                }
            }
        }

        $this->plugins = (object)array();
        //include Plugin directories

        /*foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2) {
                for ($i = 2; $i < $countFiles; $i++) {
                    if(strpos($files[$i], 'Plugin') !== false){
                        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);

                        $magicOperator = preg_replace('/Plugin.php/', '', $files[$i]);
                        $withNameSpace = 'anu\\' . $withoutExt;
                        $this->plugins->$magicOperator = new $withNameSpace();
                    }
                }
            }
        }*/
    }

    public function init(){
        try{
            $this->database = new database(anu()->config->get('database'));
        }catch (\Exception $e){
            $this->template->init();
            $config = file_get_contents('config.php');
            anu()->template->render('admin/install/index.twig', array(
                'errorMessage'  => $e->getMessage(),
                'config'        => $config
            ));
            die();
        }



        $records = anu()->record->getAllRecords();
        if(is_array($records) && count($records)){
            foreach ($records as $record){
                $baseRecord = new baseRecord($record);

                $className = Anu::getNameSpace() .$record['handle'] . "Service";
                if(!$record['handle']){
                    throw new \Exception("Record has no Handle, please check Table records for id " . $record['id']);
                }
                $recordName = $record['handle'];
                if(class_exists($className)){
                    $this->$recordName = new $className();
                }else{
                    $this->$recordName = new entryService();
                }

                $this->$recordName->init($baseRecord);
            }
        }

        //TODO check if Table exists and create installer
        /*
        $result = anu()->database->('SHOW TABLES LIKE `records`')->fetch();
        echo "<pre>";
        var_dump($result);
        echo "</pre>";
        die();
        */
        foreach ($this->initServices as $service){
            $this->$service->init();
        }

    }
}


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
    public static function getPluginDirectory(){
        return Anu::$paths['pluginDirectory'];
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

    public static function notice($message, $level = "notice"){

    }

    public static function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && Anu::in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $message string
     * @return string
     */
    public static function t($message){
        return $message;
    }

    /**
     * Search array for value and return parent key
     *
     * @param $needle
     * @param $haystack
     * @return bool|int|string
     */
    public static function array_search_parent($needle, $haystack){
        foreach ($haystack as $k => $v){
            if($v[0] == $needle){
                return $k;
            }
        }
        return false;
    }


    /**
     * @param $class
     * @param null $extention
     * @param bool $returnClass
     * @return bool|string|baseModel|entryModel|baseService|entryService|fieldService
     */
    public static function getClassByName($class, $extention = null, $returnClass = false){
        if(is_string($class)){
            $type = ($extention)? $extention : "Service";
            $className = Anu::getNameSpace() . $class . $type;
            if(class_exists($className)){
                if($returnClass) return new $className($class);
                return $className;
            }
            return null;
        }else{
            $className = get_class($class);
            $match = array();
            preg_match('/Anu\\\([a-zA-Z0-9-]*)(Service|Model|Record)/',$className, $match);
            if(count($match) > 1){
                $type = ($extention)? $extention : $match[2];
                $className = Anu::getNameSpace() . $match[1] . $type;
                if(!$returnClass){
                    return $className;
                }else{
                    if(class_exists($className)){
                        return new $className();
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $model
     * @return entryModel
     */
    public static function getModelByName($model){
        $className = Anu::getNameSpace() . $model . "Model";
        if(class_exists($className)){
            return new $className($model);
        }
        $model = new entryModel($model);
        return $model;
    }

    public static function getRecordByName($record){
        $className = Anu::getNameSpace() . $record . "Record";
        if(class_exists($className)){
            return new $className();
        }

        return anu()->record->getRecordByName($record, true);
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

    /**
     * @param $subject
     * @param array $variables
     * @param string $escapeChar
     * @param null $errPlaceholder
     * @return mixed
     */
    public static function parse(
        /* string */ $subject,
                     array        $variables,
        /* string */ $escapeChar = '@',
        /* string */ $errPlaceholder = null
    ) {
        $esc = preg_quote($escapeChar);
        $expr = "/
        $esc$esc(?=$esc*+{)
      | $esc{
      | {(\w+)}
    /x";

        $callback = function($match) use($variables, $escapeChar, $errPlaceholder) {
            switch ($match[0]) {
                case $escapeChar . $escapeChar:
                    return $escapeChar;

                case $escapeChar . '{':
                    return '{';

                default:
                    if (isset($variables[$match[1]])) {
                        return $variables[$match[1]];
                    }

                    return isset($errPlaceholder) ? $errPlaceholder : $match[0];
            }
        };

        return preg_replace_callback($expr, $callback, $subject);
    }
}

/**
 * returns an app object with all services and methods combined
 * the same object is a global in twig so everything that is accesable in
 * php is accessable in twig as well.
 *
 * @return app
 */
function anu()
{
    return Anu::app();
}