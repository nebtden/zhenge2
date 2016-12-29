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
        if($_SESSION['open_id']){
            $open_id = $_SESSION['open_id'];
        }else{
            require_once SITE_PATH."lib/WxPay.Api.php";
            require_once SITE_PATH."example/WxPay.JsApiPay.php";

            $tools = new \JsApiPay();
            $open_id = $tools->GetOpenid();
        }

        $where = array();
        $where['open_id'] = $open_id;
        $member_info  = $this->model_member->where($where)->find();
        $this->assign('member_info',$member_info);
        $this->display(':edit');
    }

    function test(){
        $this->display(':edit');
    }

    function post(){
        if(IS_POST){
            $where = array();
            $where['open_id']    =    $_SESSION['open_id'];

            $data= array();
            $data['telephone'] = $_POST['telephone'];
            $data['email'] = $_POST['email'];
            $data['address'] = $_POST['address'];
            $data['name']  = $_POST['name'];
            $result  = $this->model_member->where($where)->data($data)->save();
            if ($result!==false) {
                //redirect(U('index/index'));
                $this->success("数据更新成功！",U('index/index'));
            } else {
                $this->error("添加失败！");
            }

        }

    }
}