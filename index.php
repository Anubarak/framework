<?php

    /*
     * 
     * index.php is always our main entrance for our application.
     * 
     * In this case we instantiate our application wrapper
     * and call the run() method to start.
     * 
     * see application.controller.php for more information
     * 
     * You also can call start_session() here, if you need it. 
     * 
     */

    namespace Anu;
    DEFINE("BASE", __DIR__ . "\\");

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    $server = $protocol . $host . '/';
    DEFINE("BASE_URL", $server . "framework/");

    $dirs = array(
        'app\anu\service', 'app\anu\model', 'app\anu\control', 'app\anu\core', 'app\anu\record', 'app\anu\twig',
        'app\plugins\model', 'app\plugins\service', 'app\plugins\control', 'app\plugins\record',
    );
    foreach ($dirs as $dir){
        $files = scandir($dir);
        $countFiles = count($files);
        if($countFiles > 2) {
            for ($i = 2; $i < $countFiles; $i++) {
                require_once BASE . $dir. '\\' . $files[$i];
            }
        }
    }

    $app = new CApplication('Just a placeholder', 'I wont use this picture in final release Copyright by Terrabattleforum.com');
    session_start();
    $app->run();
?>