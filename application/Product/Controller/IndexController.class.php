<?php
namespace Product\Controller;
use Common\Controller\HomebaseController;
use Think\Exception;
use Common\Model\OrdersModel;




class IndexController extends HomebaseController{

	function index(){
        $this->display();
	}

	function show(){
        $id = intval($_GET['id']);
	    if($id){
	        $_SESSION['product_id'] = $id;
        }else{
            throw  new Exception('系统错误！');
        }

        $product_model = M('products');
        $model_store = M('stores');
        $product_info=$product_model->where( array ('id'=>$id))->find();


        $storeinfo = $model_store->where( array ('id'=>$_SESSION['store_id'] ))->find();
        $this->assign('storeinfo',$storeinfo);
        $this->assign('product_info',$product_info);
        $this->display(':show');
    }

    function date(){
        $model_store = M('stores');
        $store_info = $model_store->where( array ('id'=>$_SESSION['store_id'] ))->find();
        if(!$_SESSION['product_id']){
            throw  new  Exception('请选择产品！');
        }
        $this->assign('store_info',$store_info);
        $this->display(':date');
    }

    function getDateJson(){
        $model_order = M('Orders');

        $conditon = array();
        $conditon['store_id'] = $_SESSION['store_id'];
        $conditon['date']     =array('egt', date('Y-m-d'));
        $order_list=$model_order->field('count(*) as count,date')->where($conditon)->group('date')->select();
        $length = count(OrdersModel::$_time_index);

        $return_list = array();
        foreach ($order_list as &$val){
            if($val['count']==$length){
                $date_array = explode('-',$val['data']);
                $date = $date_array[0].'-'.intval($date_array[1]).'-'.intval($date_array[2]);
                $return_list[] =array('d'=>$date) ;
            }
        }
        echo json_encode($return_list);
        exit();

    }



    function time(){

        if(!$_GET['date']){
            throw  new  Exception('请选择日期！');
        }else{
            $_SESSION['date'] = $_GET['date'];
        }
        $Store = M("Stores"); // 实例化User对象
        $model_order = M('Orders');
        $condition['id'] = $_SESSION['store_id'];
        $store_info = $Store->where($condition)->find();

        $conditon = array();
        $conditon['store_id'] = $_SESSION['store_id'];
        $conditon['date']     =   htmlspecialchars($_GET['date']);
        $order_list=$model_order->field('id,time_index')->where($conditon)->select();
        $used_list = array();

        //过期日期不再点击
        $date_array = explode('-',$_GET['date']);
        $date = $date_array[0].'-'.intval($date_array[1]).'-'.intval($date_array[2]);

        if($_GET['date']== date('Y-m-d'))
        foreach ($order_list as $val){
            $used_list[] = $val['time_index'];
        }

        $time_index = OrdersModel::$_time_index;

        $this->assign('list',$time_index);
        $this->assign('used_list',$used_list);
        $this->assign('store_info',$store_info);
        $this->display(':time');
    }




    function createOrder(){
        if(!IS_POST){
            throw new Exception('system error!');
        }
        $Order = M('Orders');
        $Order->create();
        $product_model = M('Products');
        $id = $_SESSION['product_id'];
        $product_info=$product_model->where( array ('id'=>$id))->find();


        $Order->order_sn = time().rand(100,999); // 设置默认的用户状态
        $Order->order_amount = $product_info['price']; // 设置默认的用户状态

        $Order->paid_amount = C('money');
        $Order->state = 0;

        $result = $Order->add();
        if($result){
            redirect(U('order/index/show',array('id'=>$result)));
        }else{
            $this->error($Order->getError());
        }

        //$this->success('新增成功', 'Order/index/');
//        redirect('/Order/category/cate_id/2');
//        $this->success('新增成功', 'User/list');

    }
}