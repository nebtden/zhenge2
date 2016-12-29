<?php
namespace Voucher\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $model_vouchers;

    public function _initialize() {
        parent::_initialize();
        $this->model_vouchers = D("Common/Vouchers");
    }


	function index(){
        $count=$this->model_vouchers->count();
        $page = $this->page($count, 10);

        $vouchers_list = $this->model_vouchers
            ->limit($page->firstRow , $page->listRows)
            ->order('id desc')
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("vouchers_list",$vouchers_list);
        $this->display();
	}

    //
    public function add(){
        if(IS_POST){
            if( $_POST['post']['num']>1000){
                $this->error("优惠券数目生成过多，一次最多1000！");
            }
            $price = $_POST['post']['price'];
            $num = $_POST['post']['num'];

            $voucher_init = time();
            for ($i=0;$i<$num;$i++ ){
                $data=array();
                $data['code'] = $voucher_init+$i.$this->getrandom();
                $data['price'] =$price;
                $this->model_vouchers->add($data);
            }

            $this->success("保存成功！");

        }else{
            $this->display();
        }


    }

    public function voucherlist_create(){

    }

    private function getrandom(){
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';

        $key ='';
        for($i=0;$i<6;$i++)
        {
            $key .= $pattern{mt_rand(0,35)};    //生成php随机数
        }
        return $key;
    }






}