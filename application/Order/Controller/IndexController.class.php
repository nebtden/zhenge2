<?php
namespace Order\Controller;
use Common\Controller\HomebaseController;
use Common\Model\OrdersModel;

class IndexController extends HomebaseController{

    private  $app_id = 'wx2a94b63fba2f544e';
    private  $secret = '83e83a1a78965c8895bb4a86317e1485';
    private  $mch_id = '1424249102';


    private  $model_order;
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

    function create(){
        $id = intval($_POST['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();

        $open_id = $_SESSION['open_id'];
        $model_members = M('Members');
        $menber_info = $model_members->where(array('open_id'=>$open_id))->find();
        $data=array();

        $data['name']  =$_POST['name'];
        $data['address']  =$_POST['address'];
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
        $xmlinfo = $this->xml($order_info);

        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,'https://api.mch.weixin.qq.com/pay/unifiedorder');//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlinfo);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        print_r($data);//输出结果




//        $pay_info= array();
//        $pay_info['appId'] =$this->app_id;
//        $pay_info['timeStamp'] =time();
//        $pay_info['nonceStr'] =rand(1000000000000000,9999999999999999);
//        $pay_info['package'] ='prepay_id=123456789';


//        $this->assign('pay_info',$pay_info);
        $this->assign('order_info',$order_info);
        $this->display(':create');
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
        //需要连表查询  @todo
        $this->model_order = M('orders');

        $count=$this->model_order->where( array ('member_id'=>$_SESSION['member_id']))->count();
        $page = $this->page($count, 10);

        $order_list = $this->model_order
            ->where( array ('member_id'=>$_SESSION['member_id']))
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