<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Anu;

require_once BASE . 'app\anu\control\baseController.php';
class ajaxController extends baseController
{
    /**
     * @param $service
     * @param $function
     */
    public function service($service, $function){
        $this->returnJson(anu()->$service->$function());
    }
}