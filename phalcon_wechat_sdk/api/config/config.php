<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

static $_globalConfig;

if (!$_globalConfig) {
    $_globalConfig = new \Phalcon\Config(array(
        'productionDb' => 0,
        'productionCache' => 0,
        'database' => array(
            'adapter' => 'Mysql',
            "dsn" => "mysql:host=localhost;dbname=yourdaname;port=3306;charset=utf8",
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'your db pass',
            'dbname' => 'yourdaname',
            'charset' => 'utf8',
            'persistent' => 'true',
        ),
        'databaseInfo' => array(
            'adapter' => 'Mysql',
            "dsn" => "mysql:host=localhost;dbname=information_schema;port=3306;charset=utf8",
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'your db pass',
            'dbname' => 'information_schema',
            'charset' => 'utf8',
            'persistent' => 'true',
        ),
        'application' => array(
            'controllersDir' => APP_PATH . '/api/controllers/',
            'modelsDir' => APP_PATH . '/api/models/',
            'viewsDir' => APP_PATH . '/api/views/',
            'servicesDir' => APP_PATH . '/api/services/',
            'validatorsDir' => APP_PATH . '/api/validators/',
            'utilitiesDir' => APP_PATH . '/api/utilities/',
            'pluginsDir' => APP_PATH . '/api/plugins/',
            'IOFactoryDir' => APP_PATH . '/api/plugins/PHPExcel/',
          //  'vendorDir'     =>APP_PATH."/vendor/overtrue/wechat/src/Foundation/"
//            'baseUri' => '/phalcon_swoole_xb/',
            //'encryptKey' => 'yinxiantech_@#!$%^&'
        ),
        'redisCache' => array(
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => '',
            'persistent' => false,
            'index' => 0
        ),
        //短信接口配置
        'mobileMessage' => array(
            //服务器参数设置
            'server_url' => 'http://127.0.0.1:8030',   // 服务器接口路径
            'username' => '',    // 账号
            'password' => '',    // 密码
            'veryCode' => ''    // 通讯认证Key
        ),
        'wechat' =>array(
            'Appid'                              => '',
            'AppSecret'                      => '',
            'deploy_addr'                   => '',
            'Token'                              => '',
            'EncodingAESKey'          => '',
            'scopes'                           => 'snsapi_base',
            'merchant_id'                   => '',
            'mer_key'                         => '',
            'cert_path'                        => '',
            'key_path'                        => '',
            'debug'                             =>1,
            'template_id'                    =>'',

        )
    ));
}

return $_globalConfig;
