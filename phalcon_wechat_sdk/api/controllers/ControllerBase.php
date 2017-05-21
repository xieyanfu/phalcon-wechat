<?php

use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset;
use \Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    public function initialize()
    {
        $now = gmdate("D, d M Y H:i:s") . " GMT";
        header("Date: $now");
        header("Expires: $now");
        header("Last-Modified: $now");
        header("Pragma: no-cache");
        header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
        $content_type = 'application/json';
        if (Di::getDefault()->getRequest()->isMethod("OPTIONS")) {
            $this->response->resetHeaders();
            $this->response->setHeader('Access-Control-Allow-Origin', '*');
            $this->response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
            $this->response->setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE,OPTIONS');
            $this->response->setHeader('Content-type', $content_type);
            $this->response->sendHeaders();

            die;
        }

        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $this->response->setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE,OPTIONS');
        $this->response->setHeader('Content-type', $content_type);

    }



    // 以确认是对象的方式访问某个POST
    protected function getObjFromArray ($field, $data) {
        if (array_key_exists($field, $data)){
            $target = $data[$field];
            if (is_array($target) || is_object($target)){
                return $target;
            } else {
                return json_decode($target, true);
            }
        } else {
            return false;
        }
    }

}
