<?php
namespace Store\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $stores_model;

    public function _initialize() {
        parent::_initialize();
        $this->stores_model = D("Common/Stores");
    }


	function index(){
        $stores_list=$this->stores_model->order(array("id"=>"ASC"))->select();
        $this->assign("stores_list",$stores_list);
        $this->display();
	}


    //添加页面
    public function add(){
        $this->display();
    }



    // 友情链接添加提交
    public function add_post(){
        if(IS_POST){
            if ($this->stores_model->create()!==false) {
                if ($this->stores_model->add()!==false) {
                    $this->success("添加成功！", U("store/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->stores_model->getError());
            }

        }
    }




}