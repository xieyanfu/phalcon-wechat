<?php

static $_globalLoader;

if (!$_globalLoader){
    $_globalLoader = new \Phalcon\Loader();

    /**
     * We're a registering a set of directories taken from the configuration file
     */
    $_globalLoader->registerDirs(
        array(
            $config->application->servicesDir
        )
    )->register();
}


return $_globalLoader;