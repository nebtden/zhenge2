<?php
if(!defined('SITE_PATH')){
    define('SITE_PATH', dirname(dirname(__FILE__))."/");
}
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once SITE_PATH."lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志


//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
if(!$_SESSION['open_id']){
    $tools = new JsApiPay();
    $openId = $tools->GetOpenid();
}else{
    $openId = $_SESSION['open_id'];
}



//②、统一下单
$order_info = $_SESSION['order_info'];
var_dump($order_info);



$input = new WxPayUnifiedOrder();
$input->SetBody("甄阁押金预定");
$input->SetAttach($order_info['store_name']);
$input->SetOut_trade_no($order_info['order_sn']);
$input->SetTotal_fee($order_info['paid_money']*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://zhenimage.com/wx/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);

$jsApiParameters = $tools->GetJsApiParameters($order);


//获取共享收货地址js函数参数
//$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信支付</title>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
    <meta charset="utf-8">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,minimal-ui">
    <meta name="renderer" content="webkit">

    <link rel="stylesheet" href="/themes/simplebootx/Public/style/css/base.css">
    <link rel="stylesheet" href="/themes/simplebootx/Public/style/css/tlp.css">


    <script type="text/javascript" src="/themes/simplebootx/Public/style/lib/jquery.min.js"></script>
    <script type="text/javascript" src="/themes/simplebootx/Public/style/js/base.js"></script>
    <script type="text/javascript" src="/themes/simplebootx/Public/style/js/jquery.datePicker-min.js"></script>
    <link type="text/css" href="/themes/simplebootx/Public/style/css/rili.css" rel="stylesheet" />

</head>
<body>
<header class="demo-header widget-hd amcontent">
    <div class="head">
        <!--<span class="bj-icon ic-head-back" onclick="history.back()"> 上一页</span>-->
        <h2 class="h2_logo"></h2>
        <span class="bj-icon ic_head_menu" id="s_menu"></span>
    </div>
</header>
<div class="wrap amcontent">

    <dl class="dl_otips">

        <dt class="color1">支付提示<br>
            档期将为您保留20分钟，请尽快支付。</dt>

        <dd class="color3">支付剩余时间 </dd>
        <dd class="dd_time">17:54</dd>

        <dd class="color3">请您在剩余时间内完成支付，否则订单将被取消
            您也可以在“我的订单”中查看或取消该订单</dd>

        <dd><span class="ic_gz">查看退款规则</span></dd>

        <dd>
            <button class="btn_wx" type="button" onclick="callpay()"  class="btn2">微信支付</button>
        </dd>
        <dd class="dd_line"></dd>

        <dd class="dd_btn">
            <a href="/index.php?g=order&m=index&a=my"  class="btn2">我的订单</a>
        </dd>

    </dl>
</div>
<nav id="menu">
    <ul class="menu_ku">
        <li class="" data-href="/index.php"><span class="menu_yy">开始预约</span></li>
        <li class="" data-href="/index.php?g=order&m=index&a=my"><span class="menu_myorder">我的订单</span></li>
        <li class="" data-href="/index.php?g=member&m=index&a=index""><span class="menu_ziliao">个人资料</span></li>
        <li class="" data-href="/index.php?g=&m=article&a=index&id=1&cid=1"><span class="menu_mend">门店地址</span></li>
        <li class="" data-href="/index.php?g=&m=article&a=index&id=3&cid=1"><span class="menu_xuz">拍摄须知</span></li>
    </ul>
</nav>
<div class="bMask"></div>
<script>
    $(function () {
        $('#menu li').click(function () {
            window.location = $(this).data('href');
        })
    });

</script>
</body>
</html>