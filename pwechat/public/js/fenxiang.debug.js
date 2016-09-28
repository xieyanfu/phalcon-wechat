/*document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
 WeixinJSBridge.call('hideToolbar');
 });*/

//非电脑打开，跳转
//var is_mobi = navigator.userAgent.toLowerCase().match(/(ipod|iphone|android|coolpad|mmp|smartphone|midp|wap|xoom|symbian|j2me|blackberry|win ce)/i) != null;
//if(!is_mobi){
//window.location.href="http://www.4dfang.com/";
//}

//alert('{$signPackage.signature}');


/*
 * 注意：
 * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
 * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
 * 3. 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
 *
 * 如有问题请通过以下渠道反馈：
 * 邮箱地址：weixin-open@qq.com
 * 邮件主题：【微信JS-SDK反馈】具体问题
 * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
 */

//下面的内容可以动态数据库中获取


var _title = wxTitle;
var _desc = wxDesc;
var _link = wxURL;
var _imgUrl = wxIcon;

var jsApiList=[
    // 所有要调用的 API 都要加到这个列表中
    'checkJsApi',//判断当前客户端是否支持指定JS接口
    'onMenuShareTimeline',//获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
    'onMenuShareAppMessage',//获取“分享给朋友”按钮点击状态及自定义分享内容接口
    'onMenuShareQQ',//获取“分享到QQ”按钮点击状态及自定义分享内容接口
    'onMenuShareWeibo',//获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
    'hideMenuItems',//批量隐藏功能按钮接口
    'showMenuItems',//批量显示功能按钮接口
    'hideAllNonBaseMenuItem',//隐藏所有非基础按钮接口
    'showAllNonBaseMenuItem',//显示所有功能按钮接口
    'translateVoice',//识别音频并返回识别结果接口
    'startRecord',//开始录音接口
    'stopRecord',//停止录音接口
    'playVoice',//播放语音接口
    'pauseVoice',//暂停播放接口
    'stopVoice',//停止播放接口
    'uploadVoice',//上传语音接口
    'downloadVoice',//下载语音接口
    'chooseImage',//拍照或从手机相册中选图接口
    'previewImage',//预览图片接口
    'uploadImage',//上传图片接口
    'downloadImage',//下载图片接口
    'getNetworkType',//获取网络状态接口
    'openLocation',//使用微信内置地图查看位置接口
    'getLocation',//获取地理位置接口
    'hideOptionMenu',//隐藏右上角菜单接口
    'showOptionMenu',//显示右上角菜单接口
    'closeWindow',//关闭当前网页窗口接口
    'scanQRCode',//调起微信扫一扫接口
    'chooseWXPay',//发起一个微信支付请求
    'openProductSpecificView',//跳转微信商品页接口
    'addCard',//批量添加卡券接口
    'chooseCard',//调起适用于门店的卡券列表并获取用户选择列表
    'openCard'//查看微信卡包中的卡券接口
];

function wxready() {

    // 在这里调用 API
    // 2. 分享接口
    // 2.1 监听“分享给朋友”，按钮点击、自定义分享内容及分享结果接口
    /*
     wx.getLocation({
     success: function (res) {
     var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
     var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
     var speed = res.speed; // 速度，以米/每秒计
     var accuracy = res.accuracy; // 位置精度
     }
     });

     */
    wx.onMenuShareAppMessage({
        title: _title,
        desc: _desc,
        link: _link,
        imgUrl: _imgUrl
        /* trigger: function (res) {
         alert('用户点击发送给朋友');
         },
         success: function (res) {
         alert('已分享');
         },
         cancel: function (res) {
         alert('已取消');
         },
         fail: function (res) {
         alert(JSON.stringify(res));
         } */
    });

    // 2.2 监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareTimeline({
        title: _title,
        link: _link,
        imgUrl: _imgUrl
        /* trigger: function (res) {
         alert('用户点击分享到朋友圈');
         },
         success: function (res) {
         alert('已分享');
         },
         cancel: function (res) {
         alert('已取消');
         },
         fail: function (res) {
         alert(JSON.stringify(res));
         } */
    });

    // 2.3 监听“分享到QQ”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareQQ({
        title: _title,
        desc: _desc,
        link: _link,
        imgUrl: _imgUrl
        /* trigger: function (res) {
         alert('用户点击分享到QQ');
         },
         complete: function (res) {
         alert(JSON.stringify(res));
         },
         success: function (res) {
         alert('已分享');
         },
         cancel: function (res) {
         alert('已取消');
         },
         fail: function (res) {
         alert(JSON.stringify(res));
         } */
    });

    // 2.4 监听“分享到微博”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareWeibo({
        title: _title,
        desc: _desc,
        link: _link,
        imgUrl: _imgUrl
        /* trigger: function (res) {
         alert('用户点击分享到微博');
         },
         complete: function (res) {
         alert(JSON.stringify(res));
         },
         success: function (res) {
         alert('已分享');
         },
         cancel: function (res) {
         alert('已取消');
         },
         fail: function (res) {
         alert(JSON.stringify(res));
         } */
    });
}

$.ajax({
    url: 'http://www.iihwx.com/iih_wechatbs/home/index/getSignPackage_url',
    type: 'POST',
    dataType: 'json',
    data: {url:window.location.href},
    success:function  (re) {
        //var rjss = eval("("+re.info+")");
        var rjss = re;
        console.log("remote jss is:");
        console.log(rjss);
        alert("get sign package "+rjss.url);

        wx.config({
            debug: true,
            appId: rjss.appId,
            timestamp: rjss.timestamp,
            nonceStr: rjss.nonceStr,
            signature: rjss.signature,
            jsApiList: jsApiList
        });

        wx.ready(wxready);
    },
    error:function  (xhr,textStatus,errorThrown) {
        console.log("error sign");
        console.log(xhr.response);
        console.log(textStatus);
        console.log(errorThrown);
        return false;
    }
});