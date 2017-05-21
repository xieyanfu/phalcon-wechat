<?php

/**
 * Created by PhpStorm.
 * User: adonishong
 * Date: 16/9/11
 * Time: 上午2:02
 */

use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

class ReqAndResponse
{

    static $_instance;

    static $responseInfo = array(
        200 => "请求成功",
        201 => "资源创建成功",
        400 => "请求无效, 请检查参数",
        401 => "未授权访问或者授权未通过",
        403 => "资源禁止访问",
        404 => "请求的资源不存在",
        405 => "请求的方法禁止访问",
        500 => "内部服务器错误",
        501 => "请求的方法未实施",
        503 => "服务不可用"
    );

    static $accessDataMethod = array(
        'GET' => 'has',
        'POST' => 'hasPost',
        'PUT' => 'hasPut'
    );

    private function __construct()
    {

    }

    //发送响应数据包
    //参数: 响应码, 信息, 数据
    //过程: 把响应码, 信息, 数据组装好
    public function sendResponsePacket($code, $data = NULL, $info = '')
    {
//        Di::getDefault()->getFjLog()->debug(__FUNCTION__.' send response '.json_encode($data).' '.json_encode($info).'\n');
        $response = array(
            'code' => $code,
            'info' => $info ? $info : self::$responseInfo[$code],
            'data' => $data
        );

        echo json_encode($response);
    }



    //验证companyToken的合法性
    //如果companyToken的合法性, 返回对应的companyId, 否则返回false
    public function verifyCompanyToken($token)
    {
        //1. 验证companyToken是否存在
        $cache = Di::getDefault()->getServiceCache();
        $companyId = $cache->get($token);
        if ($companyId) {
            return $companyId;
        } else {
            return false;
        }
    }

    //根据username, password, departmentToken验证合法性
    //如果合法, 生成token, 存储, 并且返回token, 否则返回false
    /**
     * @param $model
     * @param $mobile
     * @param $password
     * @return mixed
     *
     *  mobile   联系方式
     *  uId      当前用户Id
     *  name     用户名
     *  statusList   订单状态列表
     *  roleList     角色列表
     *
     *
     */
    public function generateUserToken($model, $mobile, $password)
    {
        if ($model == "Operator") {
            $columnList = ["Id", "Mobile", "Name"];
        } else {
            $columnList = ["Id", "Mobile", "Name", "RoleId"];
        }
        //2. 验证$username , $password是否存在
        $validUser = $model::findFirst([
            "conditions" => "Mobile = :mobile: AND Password = :pwd: AND DelFlg = :delFlg:",
            "bind" => [
                "mobile" => trim($mobile),
                "pwd" => sha1(trim($password)),
                'delFlg' => 0
            ],
            "columns" => $columnList
        ]);
        if (!$validUser->Id) {
            Di::getDefault()->get('logger')->debug(__FUNCTION__ . ' no emp issue\n' . $mobile . ' ' . $password);
            return false;
        }

        //4. 如果用户存在, 并且companyId和用户的dept的companyId匹配, 那就sha1($username.$validUser->DeptId.$password.uniqueId)生成一个token, 并且记录在redis中
        $userToken['userToken'] = sha1($model . $mobile . $validUser->Id . uniqid());
        if ($model != 'Operator') {
            $userToken['roleId'] = $validUser->RoleId;
            $statusGroup = Role::findFirstById($validUser->RoleId);
            if ($statusGroup) {
                $statusGroup = explode(',', $statusGroup->StatusGroups);
            } else {
                $statusGroup = null;
            }
            $statusList = StatusGroup::find([
                "conditions" => "Id in ({myarr:array})",
                "bind" => ['myarr' => $statusGroup],
                'columns' => ['Status'],
                "hydration" => Resultset::HYDRATE_ARRAYS
            ])->toArray();
            $operabilityStatus = array();
            foreach ($statusList as $k => $v) {
                array_push($operabilityStatus, explode(',', $v['Status']));
            }
        }

        $roleList = array();
        $roles = Role::find([
            "conditions" => "Id < :roleId:",
            "bind" => ['roleId' => 100],
            'columns' => ['Id as RoleId', 'Title as RoleNm'],
            "hydration" => Resultset::HYDRATE_ARRAYS
        ])->toArray();
        foreach ($roles as $k => $v) {
            $roleList[$v['RoleId']] = $v['RoleNm'];
        }
        $statusGroupList = StatusGroup::find([
            "conditions" => "Id < :id:",
            "bind" => ['id' => 8],
            'columns' => ['Id', 'Status','Title'],
            "hydration" => Resultset::HYDRATE_ARRAYS
        ])->toArray();
        foreach($statusGroupList as $key=>&$val){
            $status = explode(',',$val['Status']);
            $val['statusList'] = OrderStatus::find([
                "conditions" => "Id in ({ids:array})",
                "bind" => ['ids' => $status],
                'columns' => ['Id', 'Status','Title'],
                "hydration" => Resultset::HYDRATE_ARRAYS
            ])->toArray();
            unset($val['Status']);
        }
        $userToken['mobile'] = $validUser->Mobile;
        $userToken['uId'] = $validUser->Id;
        $userToken['name'] = $validUser->Name;
        $userToken['statusList'] = $statusGroupList;
        $userToken['roleList'] = $roleList;
        if ($model != 'Operator') {
            $userToken['operabilityStatus'] = Role::getKey_arr($operabilityStatus);
        }
        $cache = Di::getDefault()->getServiceCache();
        if ($model != 'Operator') {
            $cache->save($userToken['userToken'], array(
                'name' => $validUser->Name,
                'mobile' => $mobile,
                'uId' => $validUser->Id,
                'roleId' => $validUser->RoleId,
            ));
        } else {
            $cache->save($userToken['userToken'], array(
                'name' => $validUser->Name,
                'mobile' => $mobile,
                'uId' => $validUser->Id,
                //'projectInfo' => $projectList
            ));
        }

        return $userToken;

    }


    //验证userToken的合法性
    //如果userToken的合法性, 返回对应的username, 否则返回false
    public function verifyUserToken($token)
    {
        //1. 验证userToken是否存在
        $cache = Di::getDefault()->getServiceCache();
        $userInfo = $cache->get($token);
        if ($userInfo) {
            return $userInfo;
        } else {
            return false;
        }
    }


    //验证请求的合法性
    //$type, 要验证的数据包类型, 值为大写, POST, GET, PUT, PATCH, DELETE, HEAD应该都可以验证
    //$requiredField, 必须存在的字段, 和type类型对应
    public function verifyTypeAndField($type, $requiredField = NULL)
    {

        $request = Di::getDefault()->getRequest();

        $accessDataMethod = self::$accessDataMethod[$type] ? self::$accessDataMethod[$type] : 'has';

        //如果不是指定类型的请求, 返回false
        if (!$request->isMethod($type)) {
            return false;
        }

        //如果没有必须要有的字段, 返回false
        if (count($requiredField)>0) {
            foreach ($requiredField as $k => $v) {
                if (!$request->$accessDataMethod($v)) {
                    return false;
                }
            }
        }

        return true;
    }

    //随机生成字符串
    public function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000 * (double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }


    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


}