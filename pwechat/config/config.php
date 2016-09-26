<?php
return new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'yourpassword',
        'dbname' => 'databasename',
        'charset' => 'utf8'
    ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir' => __DIR__ . '/../models/',
        'migrationsDir' => __DIR__ . '/../migrations/',
        'viewsDir' => __DIR__ . '/../views/',
        'cacheDir' => APP_PATH . '/apps/cache/',
        'servicesDir' =>APP_PATH .'/apps/services',
        'encryptKey'  =>'your own cryptKey',
        'baseUri' => '/pwechat/'
    )
));
