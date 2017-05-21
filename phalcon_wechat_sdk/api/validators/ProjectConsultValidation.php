<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Uniqueness;

class ProjectConsultValidation extends Validation
{
    static $_instance;

    public function initialize()
    {
       /* $this->numValidator('presellReferTime','预售签订参考时间');
        $this->numValidator('limitedReferTime','限购查询参考时间');
        $this->numValidator('loanReferTime','贷款审批参考时间');
        $this->numValidator('prevueReferTime','预告抵押参考时间');
        $this->numValidator('accountReferTime','放款参考时间');
        $this->numValidator('finishReferTime','结案参考时间');*/
    }

    public function numValidator($key, $message)
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