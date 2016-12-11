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
	        $_SESSION['id'] = $id;
        }
        $this->display(':show');
    }

    function date(){
        if(!$_SESSION['id']){
            throw  new  Exception('请选择产品！');
        }
        $this->display(':date');
    }

    function time(){
        if(!$_GET['date']){
            throw  new  Exception('请选择日期！');
        }
        $time_index = OrdersModel::$_time_index;
        $this->assign('list',$time_index);
        $this->display(':time');
    }
}