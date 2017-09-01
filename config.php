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
        'pluginDirectory'           => 'app\plugins',
        'coreRecordDirectory'       => 'app\plugins\record',
        'pluginRecordDirectory'     => 'app\plugins\record',
        'pluginServiceDirectory'    => 'app\plugins\service',
        'customTemplateDirectory'   => 'app\public\templates\\',
        'adminTemplateDirectory'    => 'app\anu\templates\\',
        'assetPath'                 => 'app\public\assets',
        'imgPath'                   => 'app\public\assets\img',
        'internalAssets'            => 'app/storage/internal/assets/',
        'namespace'                 => 'anu\\'
    ),
    'mode' => 'dev'
);
