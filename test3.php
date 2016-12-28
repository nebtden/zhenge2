<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 20:18
 */

session_start();
$order_info =array();
$order_info['store_name'] = 'aaaaaa';
$order_info['order_sn'] = 555555+time();
$order_info['paid_amount'] = 0.01;
$_SESSION['order_info'] =$order_info;
$_SESSION['aaa'] =$order_info;
var_dump($_SESSION['order_info']);
var_dump($_SESSION['aaa']);
