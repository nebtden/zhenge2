<?php
namespace Order\Controller;
use Common\Controller\AdminbaseController;
use Think\Exception;

class AdminIndexController extends AdminbaseController{

    protected $order_model;
    protected $model_voucher;

    private static $order_state = array(
        0=>'订单取消',
        1=>'未付款',
        2=>'已付款',
    );

    public function _initialize() {
        parent::_initialize();
        $this->order_model = D("Common/Orders");
        $this->model_voucher = M("Vouchers");
    }


    function index(){
        $count=$this->order_model->count();
        $page = $this->page($count, 10);

        $orders = $this->order_model
            ->limit($page->firstRow , $page->listRows)
            ->order('id desc')
            ->select();
        foreach ($orders as &$val){
            $val['order_state_name'] =self::$order_state[$val['order_state']];
            if($val['voucher']){

                $voucher_info = $this->model_voucher->where(array('code'=>$val['voucher']))->find();
                $val['voucher_amount'] = $voucher_info['price'];
            }else{
                $val['voucher_amount']= '';
            }

        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("orders",$orders);
        $this->display();
    }

    function edit(){
        if($_FILES){
            $order_sn = htmlspecialchars($_POST['order_sn']);
            for($i=0; $i<count($_FILES['file']['name']); $i++) {
                //Get the temp file path
                $tmpFilePath = $_FILES['file']['tmp_name'][$i];
//                $basename = basename( $_FILES['file']['name'][$i]);

                //Make sure we have a filepath
                if ($tmpFilePath != ""){
                    $path = SITE_PATH."public/uploadFiles/" .$order_sn;
                    if(!file_exists($path)){
                        mkdir($path, 0777);
                    }

                    //Setup our new file path
                    $newFilePath = SITE_PATH."public/uploadFiles/" .$order_sn.'/'.$_FILES['file']['name'][$i];

                    //Upload the file into the temp dir
                    if(move_uploaded_file($tmpFilePath, $newFilePath)) {

                        //Handle other code here

                    }else{
                        throw  new  Exception('error');
                    }
                }
            }
            $this->success("图片上传成功！", U("adminindex/index"));
        }
        $id = intval($_GET['id']);
        $model_order = M('orders');
        $order_info = $model_order->where( array ('id'=>intval($id)))->find();
        $order_sn = $order_info['order_sn'];
        //获取图片列表
         $path = SITE_PATH."public/uploadFiles/" .$order_sn;
        $imagelist= $this->getDir($path,$order_sn);

        $this->assign('order_info',$order_info);
        $this->assign('imagelist',$imagelist);
        $this->display();
    }



    public function getDir($dir,$order_sn){
        $fileArray=array();
        if (false != ($handle = opendir ( $dir ))) {
            $i=0;
            while ( false !== ($file = readdir ( $handle )) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".." && strpos($file,".")) {
                    $fileArray[$i]="/public/uploadFiles/".$order_sn.'/'.$file;
                    if($i==100){
                        break;
                    }
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        return $fileArray;
    }





}