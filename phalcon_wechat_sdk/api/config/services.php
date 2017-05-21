<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Cache\Backend\Memory as BackMemory;
use Phalcon\Cache\Backend\Redis as BackRedis;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Crypt;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View;
use Phalcon\Security;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use EasyWeChat\Foundation\Application as WxApp;




/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('switchEN', function () {
    $switch = new Switchzh();
    return $switch;
});

$di->setShared('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});
$di->setShared('logger', function () {
    $logger = new FileAdapter("../api/logs/" . date('Ymd') . ".log");
    return $logger;
});

$di->setShared('wlogger', function () {
    $logger = new FileAdapter("../api/logs/" . date('Ymd') . "wx.log");
    return $logger;
});

$di->set('view', function () use ($config) {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});

function getCacheInstance()
{
    static $_cacheInstance;
    if (!$_cacheInstance) {
        // Cache data for 2 days
        $frontCache = new FrontData([
            'lifetime' => 172800,
        ]);

        // Create the Cache setting redis connection options
        $_cacheInstance = new BackRedis($frontCache, [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => 'your pass',
            'persistent' => false,
            'index' => 0,
        ]);
//		$_cacheInstance = new BackMemory($frontCache);
    }
    return $_cacheInstance;
}

// Set the models cache service
$di->setShared('modelsCache', function () {
    return getCacheInstance();
});
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});
$di->setShared('batch', function () {
    return;
});
$di->set('PHPExcel', function () {
    require_once '../api/plugins/PHPExcel.php';
    return new PHPExcel();
});
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
//todo, how to make it singleton, how to confirm hit is a singleton
function getGlobalDBInstance($config)
{
    static $_globalDBInstance;
    if (!$_globalDBInstance) {
        $dbConfig = $config->database->toArray();
        $adapter = $dbConfig['adapter'];
        unset($dbConfig['adapter']);
        $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;
        $_globalDBInstance = new $class($dbConfig);
    }

    return $_globalDBInstance;
}

function getGlobalDBInfoInstance($config)
{
    static $_globalDBInstance;
    if (!$_globalDBInstance) {
        $dbConfig = $config->databaseInfo->toArray();
        $adapter = $dbConfig['adapter'];
        unset($dbConfig['adapter']);
        $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;
        $_globalDBInstance = new $class($dbConfig);
    }

    return $_globalDBInstance;
}

$di->setShared('db', function () use ($config) {
//    $dbConfig = $config->database->toArray();
    //    $adapter = $dbConfig['adapter'];
    //    unset($dbConfig['adapter']);
    //    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;
    //    return new $class($dbConfig);
    return getGlobalDBInstance($config);
});

$di->setShared('dbInfo', function () use ($config) {

    return getGlobalDBInfoInstance($config);
});


function getRedisCacheInstance($config)
{
    static $_redisCacheInstance;
    if (!$_redisCacheInstance) {
        // Cache data for 2 days
        $frontCache = new FrontData([
            'lifetime' => 172800,
        ]);

        //$_redisCacheInstance = new BackRedis($frontCache, $config->redisCache);
        $_redisCacheInstance = new BackRedis($frontCache, [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => 'your pass',
            'persistent' => false,
            'index' => 0,
        ]);
    }
    return $_redisCacheInstance;
}

//Set the models cache service
$di->setShared('serviceCache', function () use ($config) {
    return getRedisCacheInstance($config);
});

$di->set('security', function () {
    $security = new Security();
    $security->setWorkFactor(12);
    return $security;
});
$di->set('session', function () {
    $session = new \Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});

$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->encryptKey); //Use your own key!
    return $crypt;
});
//todo, config the session and cookie, let's see what will work

$di->set('dispatcher', function () {

    // Create an event manager
    $eventsManager = new EventsManager();

    // Attach a listener for type "dispatch"
    $eventsManager->attach("dispatch:beforeDispatchLoop", function ($event, $dispatcher) {
        // ...
        //echo "\nhere is the hook before dispatch loop\n";  转发前执行的钩子

    });

    $dispatcher = new MvcDispatcher();

    // Bind the eventsManager to the view component
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;

}, true);

$di->set('reqAndResponse', function () {
    return ReqAndResponse::getInstance();
}, true);


$di->set('mobileMessages', function () {
    return MobileMessages::getInstance();
}, true);

$di->set('userAccess', function () {
    return UserAccess::getInstance();
}, true);

$di->set('fangjieRefConversion', function () {
    FangjieRefConversion::getInstance();
}, true);

function getGlobalLogInstance()
{
    static $_globalLogInstance;
    if (!$_globalLogInstance) {
        $_globalLogInstance = new FileAdapter("fjLog.log");
    }

    return $_globalLogInstance;
}

$di->set('fjLog', function () {
    return getGlobalLogInstance();
}, true);

$di->set('fjDataAccess', function () {
    return FangjieDataAccess::getInstance();
}, true);

$di->set('modelsManager', function () {
    return new ModelsManager();
}, true);
$di->set("config", function () use ($config) {
    return $config;
}
);
$di->set('jssdk',function () use ($config) {
    $jssdk =new Jssdk($config->wechat->Appid,$config->wechat->AppSecret);
    return $jssdk;
});

$di->set('wechat', function () {
    $wechat = new Wechat();
    return $wechat;
});

//注册微信服务
$di->setShared('wxapp', function () use ($config) {
    $wechat = $config->wechat->toArray();
    return wechat($wechat);
});


return $di;
