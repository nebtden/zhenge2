<?php
namespace Order\Controller;
use Common\Controller\HomebaseController;
use Common\Model\OrdersModel;

class IndexController extends HomebaseController{
	
	function index(){
        $this->display();
	}

	//最终
	function show(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where(['id'=>intval($id)])->find();
        $order_info['time_value'] = OrdersModel::$_time_index[$order_info['time_index']];

        $Store = M("Stores"); // 实例化User对象
        $condition['id'] = $_SESSION['store_id'];

        // 把查询条件传入查询方法
        $store_info = $Store->where($condition)->find();


        $product_model = M('products');

        $product_info=$product_model->where(['id'=>$_SESSION['product_id']])->find();
        $this->assign('order_info',$order_info);
        $this->assign('store_info',$store_info);
        $this->assign('product_info',$product_info);
        $this->assign('money',C('money'));
        $this->display(':show');
    }

    function create(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where(['id'=>intval($id)])->find();


        $this->display(':create');
    }

    function detail(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where(['id'=>intval($id)])->find();

        $this->assign('order_info',$order_info);
        $this->display(':detail');
    }

    function my(){
        //需要连表查询  @todo
        $model_order = M('orders');
        $order_list = $model_order->where(['member_id'=>$_SESSION['member_id']])->select();
        $this->assign('order_list',$order_list);
        $this->display(':my');
    }

    function downImage(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where(['id'=>intval($id)])->find();

        $this->assign('order_info',$order_info);
        $this->display(':down');
    }
}