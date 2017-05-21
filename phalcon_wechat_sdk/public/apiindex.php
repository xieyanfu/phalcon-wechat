<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('..'));
define('UPLOAD_PATH', substr( __DIR__, 0, strrpos(__DIR__,DIRECTORY_SEPARATOR)) .DIRECTORY_SEPARATOR."Uploads/images".DIRECTORY_SEPARATOR );
try {

    /**
     * Read the configuration
     */
    //$config = include APP_PATH . "/app/config/config.php";
    $config = include APP_PATH . "/api/config/config.php";
    /**
     * Read auto-loader
     */
    //include APP_PATH . "/app/config/loader.php";
    $loader = include APP_PATH . "/api/config/loader.php";

    /**
     * Read services
     */
    //include APP_PATH . "/app/config/services.php";
    $di = include APP_PATH . "/api/config/services.php";

    //创建phalcon实例
    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
