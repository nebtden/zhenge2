<?php
namespace Order\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $order_model;

    public function _initialize() {
        parent::_initialize();
        $this->order_model = D("Common/Orders");
    }


    function index(){
        $orders=$this->order_model->order(array("id"=>"ASC"))->select();
        $this->assign("orders",$orders);
        $this->display();
    }





}