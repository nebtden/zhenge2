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

/**
 * 首页
 */
class IndexController extends HomebaseController {


    private  $app_id = 'wx2a94b63fba2f544e';
    private  $secret = '83e83a1a78965c8895bb4a86317e1485';
    //首页
	public function index() {
        $open_id = false;
        $redirect_url = urlencode($_SERVER['SERVER_NAME'].'/');
        if(!$open_id){
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->app_id.'&redirect_uri='.$redirect_url.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
            header("Location: $url");
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
            $storeinfo = $model_store->where(['id'=>intval($_GET['id'])])->find();
        }else{
            $storeinfo = array_values($storelist)[0];
            $_SESSION['store_id'] =$storeinfo['id'];
        }

        $this->assign('storelist',$storelist);
        $this->assign('storeinfo',$storeinfo);
        $this->display(":step2");
    }

    public function get_access_token(){

        //考虑是否会过期
        $setting = M('setting');
        $access_token_list = $setting->where(['name'=>'access_token'])->find();
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
                $data = [];
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


