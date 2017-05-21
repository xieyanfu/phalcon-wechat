<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 17/5/16
 * Time: 下午5:22
 */

use Phalcon\Http\Request;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Query;
use Phalcon\Tag;
use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class UserController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }

    //微信平台对接接口
    public function indexAction()
    {
        $options = array(
            'token' => $this->config->wechat->Token,
            'encodingaeskey' => $this->config->wechat->EncodingAESKey,
            'appid' => $this->config->wechat->Appid,
            'appsecret' => $this->config->wechat->AppSecret
            );
        $weixin = new WeiXin($options);
        $method = $_SERVER['REQUEST_METHOD'];
        $weixin->valid(false, $this->request->get('signature'), $this->request->get('timestamp'), $this->request->get('nonce'), $this->request->get('echostr'),$method);

        $type = $weixin->getRev()->getRevType();
        $this->wlogger->debug(__FUNCTION__ . " getRev type : " . json_encode($type));

        switch ($type) {
            case WeiXin::MSGTYPE_TEXT:
            $this->text($weixin);
            break;
            case WeiXin::MSGTYPE_EVENT:
            $revEvent = array();
            $revEvent = $weixin->getRevEvent();
            switch ($revEvent['event']) {
                case "subscribe":
                $this->subscribeAction($weixin);
                break;
                case "unsubscribe":
                $this->unsubscribeAction($weixin);
                break;
                case "CLICK":
                $this->clickAction($weixin);
                break;
            }
            break;
            case WeiXin::MSGTYPE_IMAGE:
            $weixin->text("亲，你发了一张皂片！")->reply();
            break;
            default:
            $weixin->text("hello world")->reply();
        }
       //创建自定义菜单
        $this->meunAction($weixin);

    }

    public function subscribeAction(WeiXin $weObj)
    {
        $msg = "欢迎关注我的服务号！";
        $weObj->text($msg)->reply();
        // $newsData = array(
        //     0=>array(
        //         'Title'=>'我的测试服务号',
        //         'Description'=>"测试服务号,感谢您的关注~",
        //         'PicUrl'=>'http://www.baidu.com/pxb/66.jpg',
        //         'Url'=>'http://www.baidu.com'
        //         ),
        //     );
        // $weObj->news($newsData)->reply();
    }
    
    public function unsubscribeAction(WeiXin $weObj)
    {
        // TODO: 做一些删除用户记录之类的事情
        $UserInfo = $this->userinfoAction($weObj);
        $this->wlogger->debug(__FUNCTION__." unsubscribe : ".json_encode($UserInfo));
    }

    public function textAction(WeiXin $weObj)
    {
        // TODO 文本消息的回复，做yes|y上传，no|n取消的判断
        $weObj->text(WeiXin::TEXT)->reply();
    }

    public function clickAction(WeiXin $weObj)
    {
        // TODO 点击菜单事件消息处理
        $revEvent = $weObj->getRevEvent();
        $this->wlogger->debug(__FUNCTION__." clickAction : ".json_encode($revEvent));
        switch ($revEvent['key']) {
            case "changjianwenti": 
            $weObj->text("该功能正在开发中，敬请期待～")->reply();
            break;
            case "rexiandianhua":
             $weObj->text("该功能正在开发中，敬请期待～")->reply();
            break;
            case "woyaozixun":
             $weObj->text("该功能正在开发中，敬请期待～")->reply();
            break;
            case "zhengcejiedu":
             $weObj->text("该功能正在开发中，敬请期待～")->reply();
            break;
            default:
            $this->sendTemplateMessageAction($weObj);
            break;
        }
    }

   //发送模版消息
    /*
        ｛
    "touser":"OPENID",
    "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
    "url":"http://weixin.qq.com/download",
    "topcolor":"#FF0000",
    "data":{
    "参数名1": {
    "value":"参数",
    "color":"#173177"    //参数颜色
    },
    "Date":{
    "value":"06月07日 19时24分",
    "color":"#173177"
    },
    "CardNumber":{
    "value":"0426",
    "color":"#173177"
    }
    "Type":{
    "value":"消费",
    "color":"#173177"
    }
    }
    }
    */
    private function sendTemplateMessageAction(Weixin $weObj){
        $openid = $weObj->getRevFrom();
        $this->logger->debug(__FUNCTION__." openid: ".$openid);
        $userInfo = Members::fetchUserOrderByOpenid($openid);
        switch($userInfo){
            case 400:  //未绑定微信
            $weObj->text('您还未绑定手机号,请绑定后再进行查询。')->reply();
            break;
            case 404:  //没有订单
            $weObj->text('没有查询到您的订单信息。')->reply();
            break;
            default: //返回订单
            $data = array(
               'first' =>array('value'=>"客户姓名：".$userInfo['name'],'color'=>"#000000"),
               'Good' =>array('value'=>"订单进度查询",'color'=>'#743A3A'),
               'contentType' =>array('value'=>$userInfo['orderStatus'],'color'=>'#346AG7'),
               'remark' =>array('value'=>'您当前有'.$userInfo['orderCount'].'条订单。','color'=>'#000000'),
               );
            $template = array(
                'touser' => $openid,
                'template_id' => $this->config->wechat->template_id,
                'url' => "http://www.baidu.com",
                'topcolor' => "#FF0000",
                'data' => $data
                );
            $weObj->sendTemplateMessage($template);
            break;
        }
    }

    public function userinfoAction(WeiXin $weObj)
    {
        $openid = $weObj->getRevFrom();
        $UserInfo = $weObj->getUserInfo($openid);
        return $UserInfo;
    }

    //生成自定义菜单：
    //todo:此处可优化。
    public function meunAction(WeiXin $weObj)
    {
        $weObj->getMenu();
       // $newmenu =  array ();
        $newmenu =  array (
            'button'    =>array(
                0 => array(
                    'name' => '易保服务',
                    'sub_button'   =>array(
                        0 => array(
                            'type'  => 'view',
                            'name'  =>'购房流程',
                            'url'   =>'http://www.baidu.com',
                            ),
                        1 => array(
                            'type' => 'click',
                            'name' => '常见问题',
                            'key' => 'changjianwenti',
                            ),
                        2 => array(
                            'type' => 'click',
                            'name' => '热线电话',
                            'key' => 'rexiandianhua',
                            )
                        )
                    ),
                1 => array(
                    'name'  => '政策解读',
                    'type'    => 'click',
                    'key'     =>'zhengcejiedu'
                    ),
                2 => array(
                    'name' => '我的进度',
                    'sub_button'   =>array(
                        0 => array(
                            'type'  => 'view',
                            'name'  =>'绑定解绑',
                            'url'   =>'http://www.baidu.com',
                            ),
                        1 => array(
                            'type' => 'click',
                            'name' => '我的进度',
                            'key' => 'wodejindu',
                            ),
                        2 => array(
                            'type' => 'click',
                            'name' => '我要咨询',
                            'key' => 'woyaozixun',
                            )
                        )
                    )
                )
            );

        $weObj->createMenu($newmenu);
    }





















}