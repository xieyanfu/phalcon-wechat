<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 16/9/29
 * Time: 上午10:46
 */

namespace Wechat\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Http\Request;

class IndexController extends ControllerBase
{
    protected $logger;
    public function initialize(){
        $this->logger = new FileAdapter(__DIR__ . '/../../logs/test.log');
        $this->logger->setLogLevel(
            Logger::CRITICAL
        );
    }
    public function indexAction()
    {
        $request = new Request();
        if($request->isPost()){
            $word = trim($request->getPost('word'));
            echo $this->switch->TransformUcwords($word);
        }
        die;
        echo '汉子转拼音首字母缩写<br/>';
        echo '好好学习,天天向上!<br/>';
        $word = '好好学习,天天向上';
        $result = $this->switch->TransformUcwords($word);
        var_dump($result);
    }

    public function testAction()
    {
        $this->view->setParamToView('name', 'peterpang');
        $this->assets->addJs('public/js/jquery.min.js');
        // These are the different log levels available:

        $this->logger->critical(
            "woca,MDZZ"
        );

    }

    public function timeAction(){
        echo 'time page...';die;
        $locale = Locale::acceptFromHttp($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        echo $locale;

    }
    public function err404Action()
    {
        var_dump('Not Found 404!');
        sleep(2);
        var_dump('Please go other page!');

    }
}