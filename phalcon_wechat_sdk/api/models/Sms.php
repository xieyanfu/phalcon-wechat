<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 17/3/5
 * Time: 下午6:01
 */

use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset;

class Sms extends \Phalcon\Mvc\Model
{
    public function getSource()
    {
        return 'Sms';
    }

    //新增验证码记录
    /**
     * @param $data
     * @return bool
     * 参数: mobile:'',verifyCode:''
     */
    public static function addVerifyCode($data)
    {
        $sms = new self();
        $sms->Mobile = $data['mobile'];
        $sms->VerifyCode = $data['verifyCode'];
        $sms->CrtDt = time();
        if ($sms->save()) {
            return true;
        } else {
            return false;
        }
    }


}