<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 13.07.2017
 * Time: 10:57
 */

$config = array(
    'database'      => array(
        'database_type' => 'mysql',
        'database_name' => 'anubarak16',
        'server' => 'localhost',
        'username' => 'root',
        'password' => ''
    ),
    'paths' => array(
        'coreServiceDirectory'      => 'app\anu\service',
        'coreRecordDirectory'       => 'app\plugins\record',
        'pluginRecordDirectory'     => 'app\plugins\record',
        'pluginServiceDirectory'    => 'app\plugins\service',
        'customTemplateDirectory'   => 'app\templates',
        'namespace'                 => 'anu\\'
    ),
);