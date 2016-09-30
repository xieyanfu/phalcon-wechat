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

class TestController extends ControllerBase
{
    protected $logger;
    public function initialize(){
        $this->logger = new FileAdapter(__DIR__ . '/../../logs/test.log');
        $this->logger->setLogLevel(
            Logger::CRITICAL
        );
    }
   public function testAction(){
       echo 'test Action';
   }
   public function saveAction(){
       echo 'save action page...';
   }
}