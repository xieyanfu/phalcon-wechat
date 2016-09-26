<?php
namespace Wechat\Frontend\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public  function IndexAction() {
       echo 'index action';
    }

}