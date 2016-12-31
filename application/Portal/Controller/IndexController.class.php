<?php
/*
 *      _______ _     _       _     _____ __  __ ______
 *     |__   __| |   (_)     | |   / ____|  \/  |  ____|
 *        | |  | |__  _ _ __ | | _| |    | \  / | |__
 *        | |  | '_ \| | '_ \| |/ / |    | |\/| |  __|
 *        | |  | | | | | | | |   <| |____| |  | | |
 *        |_|  |_| |_|_|_| |_|_|\_\\_____|_|  |_|_|
 */
/*
 *     _________  ___  ___  ___  ________   ___  __    ________  _____ ______   ________
 *    |\___   ___\\  \|\  \|\  \|\   ___  \|\  \|\  \ |\   ____\|\   _ \  _   \|\  _____\
 *    \|___ \  \_\ \  \\\  \ \  \ \  \\ \  \ \  \/  /|\ \  \___|\ \  \\\__\ \  \ \  \__/
 *         \ \  \ \ \   __  \ \  \ \  \\ \  \ \   ___  \ \  \    \ \  \\|__| \  \ \   __\
 *          \ \  \ \ \  \ \  \ \  \ \  \\ \  \ \  \\ \  \ \  \____\ \  \    \ \  \ \  \_|
 *           \ \__\ \ \__\ \__\ \__\ \__\\ \__\ \__\\ \__\ \_______\ \__\    \ \__\ \__\
 *            \|__|  \|__|\|__|\|__|\|__| \|__|\|__| \|__|\|_______|\|__|     \|__|\|__|
 */
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController;
use Common\Model\SettingModel;
use Common\Model\StoreModel;
use Think\Log;

/**
 * 首页
 */
require_once SITE_PATH."lib/WxPay.Notify.php";
class PayNotifyCallBack extends \WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        \Log::DEBUG("query:" . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        \Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }

        //处理订单逻辑
        $order_sn = $data['out_trade_no'];

        $f = fopen('log.txt','a');
        fwrite($f,'$order_sn='.$order_sn."\r\n");
        fclose($f);

        $model_order = M('orders');
        $order_info = $model_order->where( array ('order_sn'=>intval($order_sn)))->find();
        if($order_info){
            $data=array();
            $data['order_state']  =2;
            $res = $model_order->where( array ('order_sn'=>intval($order_sn)))->save($data);
        }else{
            return false;
        }


        return true;
    }
}



class IndexController extends HomebaseController {


    private  $app_id = 'wx2a94b63fba2f544e';
    private  $secret = '83e83a1a78965c8895bb4a86317e1485';
    //首页
    public function index() {


        $f = fopen('log.txt','a');
        fwrite($f,date("Y-m-d H:i:s")."post\r\n/r/n".json_encode($_POST));
        fwrite($f,date("Y-m-d H:i:s")."input\r\n/r/n".file_get_contents("php://input"));
        fclose($f);

        //$_POST 回调通知,写入到这个借口
        if(IS_POST){
            $f = fopen('log.txt','a');
            fwrite($f,date("Y-m-d H:i:s")."bbb\r\n/r/n".json_encode($GLOBALS['HTTP_RAW_POST_DATA'].""));
            fclose($f);
//            require_once SITE_PATH."example/WxPay.JsApiPay.php";
            $notify = new PayNotifyCallBack();
            $notify->Handle(false);
            return false;

        }else{
            require_once SITE_PATH."lib/WxPay.Api.php";
            require_once SITE_PATH."example/WxPay.JsApiPay.php";

            $tools = new \JsApiPay();
            $open_id = $tools->GetOpenid();
            if(!$open_id){
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->app_id.'&redirect_uri=http%3A%2F%2F'.$redirect_url.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
                header("Location: $url");
            }else{
                $_SESSION['open_id'] = $open_id;
                Log::record($open_id);
            }

            $open_id = $_SESSION['open_id'];
            $model_members = M('Members');
            $menber_info = $model_members->where(array('open_id'=>$open_id))->find();
            if(!$menber_info){
                //创建用户
                $data['open_id'] = $open_id;
                $model_members->create($data);
                $model_members->add();
            }

            $this->display(":index");
        }

    }
    public function test(){
        $data = array();
        $data['email'] = '1111';
        $open_id= 'o0qLLwNFiueQN-UfYWL0Y7H2xIT8';
        $model_members = M('Members');
        $model_members->where(array('open_id'=>$open_id))->save($data);
    }

    public function step2(){
        //得到店铺列表

        $model_store = M('Stores');
        $storelist = $model_store->select();

        if($_GET['id']){

            $storeinfo = $model_store->where(array('id'=>intval($_GET['id'])))->find();
        }else{
            $store_list_values = array_values($storelist);
            $storeinfo = $store_list_values[0];

        }
        $_SESSION['store_id'] =$storeinfo['id'];
        $_SESSION['store_name'] =$storeinfo['store_name'];

        $this->assign('storelist',$storelist);
        $this->assign('storeinfo',$storeinfo);
        $this->display(":step2");
    }

    public function get_access_token(){

        //考虑是否会过期
        $setting = M('setting');
        $access_token_list = $setting->where(array('name'=>'access_token'))->find();
        if($access_token_list && time()<$access_token_list['created_at']+$access_token_list['expires_in']){
            return $access_token_list['value'];
        }else{
            $curl = new \Curl();

            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.
                $this->app_id.
                '&secret='.
                $this->secret;
            $result = $curl->get($url);
            $result_array = json_decode($result,true);
            if($access_token_list){
                $data = array();
                $data['value'] = $result_array['access_token'];
                $data['expires_in'] = $result_array['expires_in'];
                $data['created_at'] = time();

                $setting->where('name = "access_token"')->save($data);
            }else{
                $access_token_list->name = 'access_token';
                $access_token_list->value = $result_array['access_token'];
                $access_token_list->expires_in = $result_array['expires_in'];
                $access_token_list->created_at = time();
                $access_token_list->create();
            }
            return $result_array['access_token'];
        }

    }

}




