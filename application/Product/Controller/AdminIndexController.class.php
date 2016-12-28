<?php
namespace Product\Controller;
use Common\Controller\AdminbaseController;

class AdminIndexController extends AdminbaseController{

    protected $product_model;

    public function _initialize() {
        parent::_initialize();
        $this->product_model = D("Common/Products");
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

    public function edit(){
        if(IS_POST){

            $data=I("post.post");
            $data['detail']=htmlspecialchars_decode($_POST['post']['detail']);
            $data['price']=$_POST['post']['price'];

            $result=$this->product_model->save($data);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
        $id = intval($_GET['id']);
        $model_products = M('products');
        $product_info = $model_products->where( array ('id'=>intval($id)))->find();

        $this->assign('product_info',$product_info);
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