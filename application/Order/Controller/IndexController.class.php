<?php
namespace Order\Controller;
use Common\Controller\HomebaseController;
use Common\Model\OrdersModel;

class IndexController extends HomebaseController{

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


        $product_model = M('products');

        $product_info=$product_model->where( array ('id'=>$_SESSION['product_id']))->find();
        $this->assign('order_info',$order_info);
        $this->assign('store_info',$store_info);
        $this->assign('product_info',$product_info);
        $this->assign('money',C('money'));
        $this->display(':show');
    }

    function create(){
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();


        $this->display(':create');
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
}