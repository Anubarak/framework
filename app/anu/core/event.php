<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 14.08.2017
 * Time: 17:06
 */

namespace Anu;


class event
{
    private static $events = array();

    private $sender = null;
    private $params = null;
    private $plugins = array();

    /**
     * Constructor.
     * @param mixed $sender sender of the event
     * @param mixed $params additional parameters for the event
     */
    public function __construct($sender=null,$params=null)
    {
        $this->sender=$sender;
        $this->params=$params;
    }

    /**
     * Fires a new Event and calls every plugin
     *
     * @param $name
     */
    public function raiseEvent($name){
        $changedParams = null;
        foreach (anu()->plugins as $plugin){
            if(method_exists($plugin, $name)){
                $this->params = $plugin->$name($this->params);
                $this->plugins[] = array(
                    'name'  => $plugin,
                    'param' => $this->params
                );
            }
        }
    }

    /**
     * @return array
     */
    public function getStackTrace():array{
        return $this->plugins;
    }
}