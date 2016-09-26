<?php
namespace Wechat\Frontend\Controllers;

use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Metadata;
use Phalcon\Mvc\Dispatcher;


class WechatController extends ControllerBase{

    protected $wechatinfo;
    protected $config;
    protected $weixin;
    protected $timestamp;
    protected $signPackage;


    public function initialize(){
        $this->config = array(
            'BaseAddress'  => 'http://www.unclepang.com:2333/pwechat/',
            'appId'        => 'wx038a0dc5a52a97df',
            'appSecret'    => 'cf8a6fc628c00ecedb657b5b97bc4362',
        );

      /*  $this->weixin = $this->jssdk->getSignPackage();
        $this->view->setParamToView('signPackage', $this->weixin);*/

    }

    public function testAction(){
        $this->weixin = $this->jssdk->getAccessToken();
        echo json_encode($this->weixin);
        exit;
    }
    public function indexAction(){
          $this->wechatinfo = $this->session->get('wechat');
          if(empty($this->wechatinfo)){
          if(!isset($_GET['code'])){
              $this->getcode('wechat/index');
          }else{
              $this->wechatinfo = $this->getAdvWechatInfo($_GET['code']);
              $this->session->set('wechat',$this->wechatinfo);
          }
          }
        $this->view->setParamToView('info',$this->wechatinfo);
        $this->assets->addJs('public/js/fenxiang.selfopenid.js');
        $this->assets->addJs('public/js/jquery.min.js');
        $this->assets->addJs('public/js/jquery.flipcountdown.js');
        $this->assets->addCss('public/css/jquery.flipcountdown.css');

      }

    public function getcode($entrace) {
        $targeturl =$this->config['BaseAddress'].$entrace;
        $this->response->redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->config['appId'].'&redirect_uri=' . urlencode($targeturl) . '&response_type=code&scope=snsapi_base&state=1#wechat_redirect');
    }

    public function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    public function getAccessToken() {
       /* $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('pang123');*/

        $data = $this->redis->get('pa_access_token');
        if (!$data) {
            $data['expire_time'] = 0;
            $data['access_token'] = "0";
            $this->redis->set('pa_access_token', $data, 7200);
        }
        if ($data['expire_time'] < time()) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->config['appId'].'&secret='.$this->config['appSecret'];
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data['expire_time'] = time() + 7000;
                $data['access_token'] = $access_token;
            }
        }
        //debug_info("getaccesstoken,b4 storage:".json_encode($data));
        $this->redis->set('pa_access_token', $data, 7200);
        return $data;
    }

    public function getwechatinfo($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->config['appId'].'&secret='.$this->config['appSecret'].'&code=' . $code . '&grant_type=authorization_code';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $output = curl_exec($ch);
        $info = json_decode($output, TRUE);
        $infoToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $infoToken['access_token'] . '&openid=' . $info['openid'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $output = curl_exec($ch);
        $infoAdv = json_decode($output, TRUE);
        $info['subscribe'] = $infoAdv['subscribe'];
        return $info;
    }

    public function getAdvWechatInfo($code) {
        //1. 获取accesstoken
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->config['appId'].'&secret='.$this->config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $ch=curl_init($url) ;
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        $output=curl_exec($ch);
        $info=json_decode($output,TRUE);
        //2.获取高级信息
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$info['access_token'].'&openid='.$info['openid'].'&lang=zh_CN';
        $ch=curl_init($url) ;
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        $output=curl_exec($ch);
        $infoAdv=json_decode($output,TRUE);

        return $infoAdv;
    }

    public function getAdvWechatInfoCus($code, $appid, $appsecret) {
        //1. 获取accesstoken
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        $ch=curl_init($url) ;
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        $output=curl_exec($ch);
        $info=json_decode($output,TRUE);
        //2.获取高级信息
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$info['access_token'].'&openid='.$info['openid'].'&lang=zh_CN';
        $ch=curl_init($url) ;
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        $output=curl_exec($ch);
        $infoAdv=json_decode($output,TRUE);

        return $infoAdv;
    }



}

