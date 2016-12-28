<?php
namespace Product\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $product_model;

    public function _initialize() {
        parent::_initialize();
        $this->product_model = D("Common/Product");
    }


	function index(){
        $product_list=$this->product_model->order(array("id"=>"ASC"))->select();
        $this->assign("product_list",$product_list);
        $this->display();
	}

    //
    public function add(){

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