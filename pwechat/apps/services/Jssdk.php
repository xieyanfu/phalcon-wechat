<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 16/9/9
 * Time: 下午3:07
 */

class Jssdk{

    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;

    }

    public function test(){
       echo 'aaaaaaa jssdk';
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        $httpsflag=$_SERVER['HTTPS'];
        if ($httpsflag){
            $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }else{
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
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

    public function getSignPackage_url($url)
    {
        $jsapiTicket = $this->getJsApiTicket();
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
        /*$redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('pang123');*/
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = $this->redis->get('jsapi_ticket');
        if (!$data) {
            $data['expire_time'] = 0;
            $data['jsapi_ticket'] = "0";
            $this->redis->set('jsapi_ticket', $data, 7200);
        }
        if ($data['expire_time'] < time()) {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data['expire_time'] = time() + 7000;
                $data['jsapi_ticket'] = $ticket;
                $this->redis->set('jsapi_ticket', $data, 7200);
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
        $data = $this->redis->get('access_token');
        if (!$data) {
            $data['expire_time'] = 0;
            $data['access_token'] = "0";
            $this->redis->set('access_token', $data, 7200);
        }
        if ($data['expire_time'] < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data['expire_time'] = time() + 7000;
                $data['access_token'] = $access_token;
                $this->redis->set('access_token', $data, 7200);
                debug_info("getaccesstoken,get from remote:".json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        //debug_info("getaccesstoken,b4 storage:".json_encode($data));

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