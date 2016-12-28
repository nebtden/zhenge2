<?php
namespace Member\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $model_member;

    public function _initialize() {
        parent::_initialize();
        $this->model_member = D("Common/Members");
    }


	function index(){
        $orders=$this->model_member->order(array("id"=>"ASC"))->select();
        $this->assign("orders",$orders);
        $this->display();
	}



    //
    public function add_post(){
        if(IS_POST){
            if ($this->model_member->create()!==false) {
                if ($this->model_member->add()!==false) {
                    $this->success("添加成功！", U("member/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->model_member->getError());
            }

        }
    }

    public function edit(){
        $id = intval($_GET['id']);
        $member_info = $this->model_member->where( array ('id'=>intval($id)))->find();

        $this->assign('member_info',$member_info);
        $this->display();
    }





}