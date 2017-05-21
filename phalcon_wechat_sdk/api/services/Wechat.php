<?php
/**
 * Created by PhpStorm.
 * User: pangxb
 * Date: 17/5/16
 * Time: 上午9:42
 */
use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Query;
use Phalcon\Http\Request;

class Wechat
{

    public function getwechatinfo($code)
    {
        $config = Di::getDefault()->getConfig();
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $config->wechat->Appid . '&secret=' . $config->wechat->AppSecret . '&code=' . $code . '&grant_type=authorization_code';
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

    private function getAccessToken()
    {
       $config = Di::getDefault()->getConfig();
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        //$data = json_decode(file_get_contents("access_token.json"));
       $data = Di::getDefault()->getServiceCache()->get('pa_access_token');
       if (!$data) {
        $data['expire_time'] = 0;
        $data['access_token'] = "0";
        Di::getDefault()->getServiceCache()->save('pa_access_token', $data);
    }
    if ($data['expire_time'] < time()) {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $config->wechat->Appid . '&secret=' . $config->wechat->AppSecret;
        $res = json_decode($this->httpGet($url));
        $access_token = $res->access_token;
        if ($access_token) {
            $data['expire_time'] = time() + 7000;
            $data['access_token'] = $access_token;
        }
    }
        //debug_info("getaccesstoken,b4 storage:".json_encode($data));
    Di::getDefault()->getServiceCache()->save('pa_access_token', $data);
    return $data;
}

public function httpGet($url)
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