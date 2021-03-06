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
        $count=$this->model_member->count();
        $page = $this->page($count, 10);

        $orders = $this->model_member
            ->limit($page->firstRow , $page->listRows)
            ->order('id desc')
            ->select();

        $this->assign("page", $page->show('Admin'));
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