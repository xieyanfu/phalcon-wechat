<?php
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;
error_reporting(E_ALL);

define('APP_PATH', realpath('..'));

try {

    /**
     * Read the configuration
     */
    $config = include APP_PATH . "/../config/config.php";

    /**
     * Include services
     */
   // require __DIR__ . '/../config/loader.php';

    require __DIR__ . '/../config/services.php';

    /**
     * Handle the request
     */
    $application = new Application($di);

    /**
     * Include modules
     */
    require __DIR__ . '/../config/modules.php';

    /**
     * Include routes
     */
    require __DIR__ . '/../config/routes.php';



    echo $application->handle()->getContent();
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
