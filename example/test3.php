<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 20:18
 */

session_start();
$order_info =array();
$order_info['store_name'] = 'testtt';
$order_info['order_sn'] = 555555+time();
$order_info['paid_money'] = 0.01;
$_SESSION['order_infos'] =$order_info;
var_dump($_SESSION['order_infos']);
