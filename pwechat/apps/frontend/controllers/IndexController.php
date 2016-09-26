<?php
namespace Wechat\Frontend\Controllers;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
      $test = new Test();
        $test->index();
    }

    public function testAction()
    {
        echo 'test action';
        $this->view->disable();
    }
    public function err404Action()
    {
        var_dump('Not Found 404!');
        sleep(1);
        echo "<br/>";

    }
}

