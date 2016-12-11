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
use Common\Model\StoreModel;
/**
 * 首页
 */
class IndexController extends HomebaseController {
	
    //首页 小夏是老猫除外最帅的男人了
	public function index() {
    	$this->display(":index");
    }

    public function step2(){
        //得到店铺列表

        $model_store = M('Stores');
        $storelist = $model_store->select();

        if($_GET['store_id']){
            $_SESSION['store_id'] = intval($_GET['store_id']);
            $storeinfo = $model_store->where(['id'=>intval($_GET['store_id'])])->find();
        }else{
            $storeinfo = array_values($storelist)[0];
            $_SESSION['store_id'] =$storeinfo['id'];
        }

        $this->assign('storelist',$storelist);
        $this->assign('storeinfo',$storeinfo);
        $this->display(":step2");
    }

    public function step3(){
        $this->display(":step3");
    }

    public function step4(){
        $this->display(":step4");
    }

    public function step5(){
        $this->display(":step5");
    }

    public function step6(){
        $this->display(":step6");
    }

    public function step7(){
        $this->display(":step7");
    }

}


