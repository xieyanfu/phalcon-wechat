<?php
return new \Phalcon\Config(array(

    'database' => array(
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'pang123',
        'dbname' => 'yii2',
        'charset' => 'utf8'
    ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir' => __DIR__ . '/../models/',
        'migrationsDir' => __DIR__ . '/../migrations/',
        'viewsDir' => __DIR__ . '/../views/',
        'cacheDir' => APP_PATH . '/apps/cache/',
        'servicesDir' =>APP_PATH .'/apps/services',
        'encryptKey'  =>'_PeterPang2016%$#$@^&',
        'baseUri' => '/pwechat/'
    ),
    'redis'  =>array(
        'host'       =>'127.0.0.1',
        'port'       =>'6379',
        'auth'       =>'pang123',
    ),
    'weixin'  =>array(
        'base_address'      =>'http://www.unclepang.com:2333/pwechat/home/wechat/index/',
        'appid'             =>'wx038a0dc5a52a97df',
        'appsecret'         =>'cf8a6fc628c00ecedb657b5b97bc4362',
    )


));


?>
