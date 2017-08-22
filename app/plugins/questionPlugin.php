<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 14.08.2017
 * Time: 17:17
 */

namespace Anu;


class questionPlugin
{
    public function onBeforeSaveEntry($param){
        $param['entry']->title = "test";
        return $param;
    }
}