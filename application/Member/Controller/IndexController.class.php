<?php
namespace Member\Controller;
use Common\Controller\HomebaseController;
use Common\Model\MembersModel;

class IndexController extends HomebaseController{

    protected $model_member;

    public function _initialize() {
        parent::_initialize();
        $this->model_member = D("Members");
    }
    function index(){
        $this->display(':edit');
    }

    function post(){
        if(IS_POST){

            $where = [];
            $where['open_id']    =    $_SESSION['open_id'];


            $data= [];
            $data['telephone'] = $_POST['telephone'];
            $data['email'] = $_POST['email'];
            $data['address'] = $_POST['address'];
            $data['name']  = $_POST['name'];
            $result  = $this->model_member->where($where)->data($data)->save();
            if ($result!==false) {
                $this->success("数据更新成功！");
            } else {
                $this->error("添加失败！");
            }

        }

    }
}