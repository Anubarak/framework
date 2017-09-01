<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Anu;

require_once BASE . 'app\anu\control\baseController.php';
class installController extends baseController
{
    public function getContent(){
        echo "<pre>";
        var_dump("install");
        echo "</pre>";
        die();
    }
}