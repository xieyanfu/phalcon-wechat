<?php

use Redis;
class Jssdk
{
    private $appId;
    private $appSecret;
    private $protocol;
    private $rcache;

    public function __construct($appId, $appSecret, $protocol)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->protocol = $protocol;
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->auth('your auth');
        $this->rcache = $redis;
    }
    public function test(){
          $this->rcache->set('name','peterpang',7200);
          echo json_encode($this->rcache->get('name'));
    }
    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        /*
        $httpsflag=$_SERVER['HTTPS'];
        if ($httpsflag){
            $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }else{
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        */
        $url = $this->protocol."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data =$this->rcache->get('jsapi_ticket');
        if (!$data) {
            $data['expire_time'] = 0;
            $data['jsapi_ticket'] = "0";
            $this->rcache->set('jsapi_ticket', $data, 7200);
        }

        if ($data['expire_time'] < time()) {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data['expire_time'] = time() + 7000;
                $data['jsapi_ticket'] = $ticket;
                $this->rcache->set('jsapi_ticket', $data, 7200);
            }
        } else {
            $ticket = $data['jsapi_ticket'];
        }

        return $ticket;
    }

    private function getAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        //$data = json_decode(file_get_contents("access_token.json"));
        $data = $this->rcache->get('access_token');
        if (!$data) {
            $data['expire_time'] = 0;
            $data['access_token'] = "0";
            $this->rcache->set('access_token', $data, 7200);
        }

        if ($data['expire_time'] < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data['expire_time'] = time() + 7000;
                $data['access_token'] = $access_token;
                $this->rcache->set('access_token', $data, 7200);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    private function httpGet($url)
    {
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
}

?>
