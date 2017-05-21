<?php

use Phalcon\Di;

use Phalcon\Validation;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class TestOnlyValidation extends Validation
{
    static $_instance;

    public function initialize()
    {
        $this->newPresenceValidator('required', '必选项没有填写');
        $this->newNumericalityValidator('optional', '请正确填写数字');
    }

    private function newNumericalityValidator($key, $message)
    {
        $this->add($key, new Numericality(["message" => $message . '应为数字', 'allowEmpty' => true]));
    }

    private function newPresenceValidator($key, $message)
    {
        $this->add($key, new PresenceOf(["message" => $message]));
    }

    private function newStringLengthValidator($key, $minLength, $message)
    {
        $this->add($key, new StringLength(['min' => $minLength, "messageMinimum" => $message]));
    }

    private function newStringLengthWithMaxValidator($key, $minLength, $maxLength, $message)
    {
        $this->add($key, new StringLength(['min' => $minLength, 'max' => $maxLength, "messageMinimum" => $message]));
    }

    private function newMobileValidator($key)
    {
        $this->add($key, new RegexValidator([
            'pattern' => '/^1[34578]\d{9}$/',
            'message' => '请输入有效的手机号码'
        ]));
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}