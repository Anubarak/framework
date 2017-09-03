<?php
    namespace Anu;
    error_reporting(E_ALL);
    DEFINE("BASE", __DIR__ . "\\");

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    $server = $protocol . $host . '/';
    DEFINE("BASE_URL", $server . "anu   /");

    $dirs = array(
        'app\anu\service', 'app\anu\field', 'app\anu\model', 'app\anu\control', 'app\anu\core', 'app\anu\record', 'app\anu\twig',
        'app\plugins', 'app\plugins\model', 'app\plugins\service', 'app\plugins\control', 'app\plugins\record',
    );

    $baseFiles = array(
        'app\anu\service\baseService.php',
        'app\anu\model\baseModel.php',
        'app\anu\service\fieldService.php'
    );

    foreach ($baseFiles as $file){
        if(file_exists(BASE . $file)){
            require_once BASE . $file;
        }
    }

    foreach ($dirs as $dir){
        $files = scandir($dir);
        $countFiles = count($files);
        if($countFiles > 2) {
            for ($i = 2; $i < $countFiles; $i++) {
                $path = BASE . $dir. '\\' . $files[$i];
                if(!is_dir($path)){
                    require_once $path;
                }
            }
        }
    }

    session_start();
    anu()->request->process();
?>