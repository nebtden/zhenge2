<?php
namespace Order\Controller;
use Common\Controller\HomebaseController;
use Common\Model\OrdersModel;
use Think\Exception;
use Think\Log;

class IndexController extends HomebaseController{

    private  $app_id = 'wx2a94b63fba2f544e';
    private  $secret = '83e83a1a78965c8895bb4a86317e1485';
    private  $mch_id = '1424249102';


    private  $model_order;
    private  $model_member;

    function index(){

        $this->display();
    }

    //最终
    function show(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();
        $order_info['time_value'] = OrdersModel::$_time_index[$order_info['time_index']];

        $Store = M("Stores"); // 实例化User对象
        $condition['id'] = $_SESSION['store_id'];

        // 把查询条件传入查询方法
        $store_info = $Store->where($condition)->find();

        $open_id = $_SESSION['open_id'];
        $model_members = M('Members');
        $member_info = $model_members->where(array('open_id'=>$open_id))->find();


        $product_model = M('products');

        $product_info=$product_model->where( array ('id'=>$_SESSION['product_id']))->find();
        $this->assign('order_info',$order_info);
        $this->assign('member_info',$member_info);
        $this->assign('store_info',$store_info);
        $this->assign('product_info',$product_info);
        $this->assign('money',C('money'));
        $this->display(':show');
    }

    function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }

    public function wxpay2(){
        include SITE_PATH.'example/jsapi.php';
    }

    public  function  wxpay(){


        include SITE_PATH.'example/jsapi.php';

        require_once SITE_PATH."lib/WxPay.Api.php";
        require_once SITE_PATH."example/WxPay.JsApiPay.php";
        require_once SITE_PATH.'example/log.php';

        $logHandler= new \CLogFileHandler(SITE_PATH."example/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

        //①、获取用户openid
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        \Log::DEBUG("1:" . $openId);

        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url($_SERVER['SERVER_NAME']."/index.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        \Log::DEBUG("1:" . $input);
        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';

        $this->printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php

//         * 注意：
//         * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
//         * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
//         * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）

        $this->printf_info($jsApiParameters);
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('editAddress',$editAddress);
        Log::record($jsApiParameters);
        $this->display(':create');


    }
    function create1(){

        $Order = M('Orders');
        $Order->create();
        $product_model = M('Products');
        $id = $_SESSION['product_id'];

        $product_info=$product_model->where( array ('id'=>$id))->find();
        if(!$product_info){
            $this->error('系统错误！');
        }


        $Order->order_sn = time().rand(100,999); // 设置默认的用户状态
        $Order->order_amount = $product_info['price']; // 设置默认的用户状态

        $Order->paid_amount = number_format(C('money'),2);
        $Order->created_at =date('Y:m:d H:i:s',time());

        $open_id = $_SESSION['open_id'];
        $model_members = M('Members');
        $member_info = $model_members->where(array('open_id'=>$open_id))->find();

        $Order->member_id = $member_info['id'];
        $Order->state = 0;

        $result = $Order->add();
        if($result){
            redirect(U('order/index/show',array('id'=>$result)));
        }else{
            $this->error($Order->getError());

        }
    }

    function create2(){
        $id = intval($_POST['id']);
//        $id= 4;
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();
        $_SESSION['order_info'] = $order_info;

        if(!$order_info){
            $this->error('订单没找到');
        }
        $open_id = $_SESSION['open_id'];
        if(!$open_id){
            $this->error('路径错误！',$_SERVER['SERVER_NAME']);
        }

        //处理优惠券的事情
        if($_POST['voucher']){
            $model_voucher = M('Vouchers');
            $where = array();
            $where['code'] = $_POST['voucher'];
            $where['is_used'] = 0;
            $voucher_info = $model_voucher->where($where)->find();
            if(!$voucher_info){
                $this->error('优惠券不存在或者优惠券已经被使用！');
            }
        }




        $model_members = M('Members');
        $menber_info = $model_members->where(array('open_id'=>$open_id))->find();
        $data=array();

        $data['name']  =$_POST['name'];
        $data['voucher']  =$_POST['voucher'];
        $data['address']  =$_POST['address'];
        $data['sex']  =$_POST['sex'];
        $data['email']  =$_POST['email'];
        $data['telephone']  =$_POST['telephone'];
        $res = $model_order->where( array ('id'=>intval($id)))->save($data);
        if(!$menber_info){
            //创建用户
            $data['open_id'] = $open_id;
            $model_members->create($data);
            $res =  $model_members->add();

        }else{
            //更新用户
            $res =   $model_members->where(array('open_id'=>$open_id))->save($data);
        }


        $_SESSION['order_info']['store_name'] = $_SESSION['store_name'];
        $url = $_SERVER['SERVER_NAME'].'/example/jsapi.php';
        header("Location: http://$url");

    }
    function test(){
//        $model_order = M('orders');
//        $order_info = $model_order->where( array ('id'=>2))->find();
//        var_dump($order_info);
//        $_SESSION['order_info'] = $order_info;
        var_dump( $_SESSION['order_info']);
    }


    function prePay(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();
        $xmlinfo = $this->xml($order_info);

        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,'https://api.mch.weixin.qq.com/pay/unifiedorder');//抓取指定网页
      //  curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlinfo);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        print_r($data);//输出结果
    }

    function detail(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();

        $model_product = M('Products');
        $model_store = M('Stores');

        $product_info = $model_product->where( array ('id'=>$order_info['product_id']))->field('id,title')->find();
        $store_info = $model_store->where( array ('id'=>$order_info['store_id']))->find();

        $order_info['product_title'] = $product_info['title'];
        $order_info['time'] = OrdersModel::$_time_index[$order_info['time_index']];
        $order_info['store_name'] = $store_info['store_name'];


        $this->assign('order_info',$order_info);
        $this->display(':detail');
    }

    function my(){
        $this->model_member = M('Members');
        $this->model_order = M('orders');
        require_once SITE_PATH."lib/WxPay.Api.php";
        require_once SITE_PATH."example/WxPay.JsApiPay.php";

        $tools = new \JsApiPay();
        $open_id = $tools->GetOpenid();
        $member_info = $this->model_member->where(
            array ('open_id'=>$open_id)
        )->find();

        $count=$this->model_order->where( array ('member_id'=>$member_info['id']))->count();
        $page = $this->page($count, 10);

        $order_list = $this->model_order
            ->where( array ('member_id'=>$member_info['id']))
            ->limit($page->firstRow , $page->listRows)
            ->order('id desc')
            ->select();


        $model_product = M('Products');

        foreach ($order_list as &$val){
            $product_info = $model_product->where( array ('id'=>$val['product_id']))->field('id,title')->find();

            $val['product_title'] = $product_info['title'];

            $val['time'] = OrdersModel::$_time_index[$val['time_index']];
        }

        $this->assign('order_list',$order_list);
        $this->display(':my');
    }

    function downimage(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();

        $model_product = M('Products');
        $model_store   = M('Stores');

        $product_info = $model_product->where( array ('id'=>$order_info['product_id']))->field('id,title')->find();
        $store_info = $model_store->where( array ('id'=>$order_info['store_id']))->find();

        $order_info['product_title'] = $product_info['title'];
        $order_info['time'] = OrdersModel::$_time_index[$order_info['time_index']];
        $order_info['store_name'] = $store_info['store_name'];

        $this->assign('order_info',$order_info);
        $this->display(':down');
    }

    public function xml($order_info = array()){
        $order_info['order_sn'] = '9090';
        $data = array();
        $data['appid'] = $this->app_id;
        $data['body'] = '产品定金';
        $data['mch_id'] = $this->mch_id;
        $data['nonce_str'] = rand(1000000000000000,9999999999999999);
        $data['notify_url'] =  $_SERVER['SERVER_NAME'].'/index.php';
        $data['out_trade_no'] = $order_info['order_sn'];
        $data['spbill_create_ip'] =  $_SERVER['REMOTE_ADDR'];
        $data['total_fee'] =  C('money');
        $data['trade_type'] =  'JSAPI';
        $code = 'appid='.$data['appid'].
            '&body='.$data['body'].
            '&mch_id='.$data['mch_id'].
            '&nonce_str'.$data['nonce_str'].
            '&notify_url'.$data['notify_url'].
            '&out_trade_no'.$data['out_trade_no'].
            '&spbill_create_ip'.$data['spbill_create_ip'].
            '&total_fee'.$data['total_fee'].
            '&trade_type'.$data['trade_type'];

        $data['sign'] =md5($code);
        $xmlinfo = xml_encode($data,'xml');
        return $xmlinfo;
    }
}