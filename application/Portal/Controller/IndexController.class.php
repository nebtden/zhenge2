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
class IndexController extends HomebaseController {


    private  $app_id = 'wx2a94b63fba2f544e';
    private  $secret = '83e83a1a78965c8895bb4a86317e1485';
    //首页
    public function index() {
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



        //$_POST 通知
        if($_POST){


            //初始化日志
            $logHandler= new \CLogFileHandler(SITE_PATH."example/logs/".date('Y-m-d').'.log');
            $log =\Log::Init($logHandler, 15);


            \Log::DEBUG("begin notify");
            $notify = new \WxPayNotify();
            $notify->Handle(false);


        }




        if(!$_SESSION['open_id']){
            $_SESSION['open_id'] = 1;
        }
        $this->display(":index");
    }

    public function step2(){
        //得到店铺列表

        $model_store = M('Stores');
        $storelist = $model_store->select();

        if($_GET['id']){
            $_SESSION['store_id'] = intval($_GET['id']);
            $storeinfo = $model_store->where(array('id'=>intval($_GET['id'])))->find();
        }else{
            $store_list_values = array_values($storelist);
            $storeinfo = $store_list_values[0];
            $_SESSION['store_id'] =$storeinfo['id'];
        }

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


