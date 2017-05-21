<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 16/10/12
 * Time: 下午3:02
 */
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset;

class YbConversion extends \Phalcon\Mvc\Model
{

    public function getSource()
    {
        return 'YbConversion';
    }

    //获取状态值
    public static function statusList($field)
    {
        $list = self::find([
            "conditions" => "field = :field: AND active = 1",
            "bind" => ['field' => ucfirst($field)],
            'columns' => ['raw', 'literal'],
            "hydration" => Resultset::HYDRATE_ARRAYS
        ])->toArray();
        $statusList = array();
        if (count($list) > 0) {
            foreach($list as $k=>$v){
                $statusList[$v['raw']] = $v['literal'];
            }
            return $statusList;
        } else {
            return null;
        }
    }
}