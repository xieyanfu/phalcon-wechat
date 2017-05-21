<?php

static $_globalLoader;

if (!$_globalLoader){
    $_globalLoader = new \Phalcon\Loader();

    /**
     * We're a registering a set of directories taken from the configuration file
     */

    $_globalLoader->registerDirs(
        array(
            $config->application->controllersDir,
            $config->application->modelsDir,
            $config->application->servicesDir,
            $config->application->validatorsDir,
            $config->application->utilitiesDir,
            $config->application->pluginsDir,
            $config->application->IOFactoryDir,
           // $config->application->vendorDir,
        )
    )->register();
}


return $_globalLoader;
