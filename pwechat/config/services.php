<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Di\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Cache\Backend\Memory as BackMemory;
use Phalcon\Cache\Frontend\Data as FrontData;
use Redis;
use Phalcon\Crypt;
use Phalcon\Security;

/**
 * The FactoryDefault Dependency Injector automatically registers the right services to provide a full stack framework
 */
$di = new FactoryDefault();

$config = include __DIR__ . "/config.php";
/**
 * Registering a router
 */
$di->setShared('router', function () {
    $router = new Router();

    $router->setDefaultModule('frontend');
    $router->setDefaultNamespace('Wechat\Frontend\Controllers');

    return $router;
});

/**
 * The URL component is used to generate all kinds of URLs in the application
 */
$di->setShared('url', function () use($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->encryptKey); //Use your own key!
    return $crypt;
});
$di->set('test',function() {
    $test = new Test();
    return $test;
});
$di->setShared('jssdk',function() use ($config){
      $jssdk = new Jssdk($config->weixin->appid,$config->weixin->appsecret,'http');
      return $jssdk;
});
/**
 * Setting up the view component
 */
$di->setShared('view', function () use($config) {

    $view = new View();
    echo $config->application->cacheDir;
  //  exit();
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use($config) {

            $volt = new VoltEngine($view, $di);
            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use($config) {
    $dbConfig = $config->database->toArray();
    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

    return new $class($dbConfig);
});

$di->setShared('redis', function () use ($config) {
    $host = $config->redis->host;
    $port = $config->redis->port;
    $auth = $config->redis->auth;
    $redis = new Redis();
    $redis->connect($host,$port);
    $redis->auth($auth);
    return $redis;
});

$di->setShared('redis2', function () use ($config) {
    $host = $config->redis->host;
    $port = $config->redis->port;
    $auth = $config->redis->auth;
    $redis = new Redis();
    $redis->connect($host, $port,$auth);
    return $redis;

});
/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Starts the session the first time some component requests the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
});

/**
 * Set the default namespace for dispatcher
 */
$di->setShared('dispatcher', function () use($di) {
    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setDefaultNamespace('Wechat\Frontend\Controllers');
    return $dispatcher;
});
