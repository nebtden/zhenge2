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
        }
        $this->display(':show');
    }

    function date(){
        if(!$_SESSION['product_id']){
            throw  new  Exception('请选择产品！');
        }
        $this->display(':date');
    }

    function time(){

//        $_SESSION['member_id'] =1;
//        $_SESSION['member_id'] =1;

        if(!$_GET['date']){
            throw  new  Exception('请选择日期！');
        }else{
            $_SESSION['date'] = $_GET['date'];
        }
        $Store = M("Stores"); // 实例化User对象
        $condition['id'] = $_SESSION['id'];

        // 把查询条件传入查询方法
        $store_info = $Store->where($condition)->select();
        $address = $store_info[0]['address'];

        $time_index = OrdersModel::$_time_index;
        $this->assign('list',$time_index);
        $this->assign('address',$address);
        $this->display(':time');
    }

    function createOrder(){
        if(!IS_POST){
            throw new Exception('system error!');
        }
        $Order = M('Orders');
        $Order->create();
        $Order->order_sn = '2222222'.rand(456,789); // 设置默认的用户状态
        $Order->order_amount = '2222222'.rand(0); // 设置默认的用户状态

        $result = $Order->add();
        if($result){
            redirect(U('order/index/show',['id'=>$result]));
        }else{
            $this->error($Order->getError());
        }

        //$this->success('新增成功', 'Order/index/');
//        redirect('/Order/category/cate_id/2');
//        $this->success('新增成功', 'User/list');

    }
}