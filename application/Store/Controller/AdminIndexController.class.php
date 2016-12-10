<?php
namespace Store\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $order_model;

    public function _initialize() {
        parent::_initialize();
        $this->order_model = D("Common/Stores");
    }


	function index(){
        $orders=$this->order_model->order(array("id"=>"ASC"))->select();
        $this->assign("orders",$orders);
        $this->display();
	}

    // 友情链接添加
    public function add(){
        $this->assign("targets",$this->targets);
        $this->display();
    }

    // 友情链接添加提交
    public function add_post(){
        if(IS_POST){
            if ($this->order_model->create()!==false) {
                if ($this->order_model->add()!==false) {
                    $this->success("添加成功！", U("link/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->order_model->getError());
            }

        }
    }




}