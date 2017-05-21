<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Uniqueness;

class AdminValidation extends Validation
{
    static $_instance;

    public function initialize()
    {
       // $this->testValidator('buildingNo', '楼栋数');
       // $this->testValidator('households', '总户数');
    }

    public function testValidator($key, $message)
    {
        $this->add($key, new Numericality(["message" => $message . '应为数字']));
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