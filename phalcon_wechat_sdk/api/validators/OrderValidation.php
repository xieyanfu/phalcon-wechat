<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Uniqueness;

class OrderValidation extends Validation
{
    static $_instance;

    public function initialize()
    {
        $this->payTypeValidator('payType', '付款方式');
        $this->customerNmValidator('customerNm','购房者姓名');
        $this->mobileValidator('mobile','联系方式1');
        $this->roomIdValidator('roomId','房号');
    }
    public function customerNmValidator($key, $message)
    {
        $this->add($key, new PresenceOf(["message" => $message . '为必填项']));
    }
    public function payTypeValidator($key, $message)
    {
        $this->add($key, new PresenceOf(["message" => $message . '为必填项']));
    }
    public function mobileValidator($key, $message)
    {
        $this->add($key, new PresenceOf(["message" => $message . '为必填项']));
    }
    public function roomIdValidator($key, $message)
    {
        $this->add($key, new PresenceOf(["message" => $message . '为必填项']));
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


}


?>