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
        if(IS_POST){
            $data = array();
            $data['store_name'] = $_POST['post']['store_name'];
            $data['address'] = $_POST['post']['address'];
            $data['phone'] = $_POST['post']['phone'];

            if ($this->stores_model->create($data)!==false) {
                if ($this->stores_model->add()!==false) {
                    $this->success("添加成功！", U("store/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->stores_model->getError());
            }

        }else{
            $this->display();
        }

    }



    // 友情链接添加提交
    public function add_post(){

    }

    public function edit(){
        if(IS_POST){

            $data=I("post.post");
//            $data['detail']=htmlspecialchars_decode($_POST['post']['detail']);
//            $data['price']=$_POST['post']['price'];

            $result=$this->stores_model->save($data);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
        $id = intval($_GET['id']);

        $store_info = $this->stores_model->where( array ('id'=>intval($id)))->find();

        $this->assign('store_info',$store_info);
        $this->display();
    }




}