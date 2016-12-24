<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 20:18
 */


$_SESSION['order_info'] = $order_info;
$url = $_SERVER['SERVER_NAME'].'/index.php';
header("Location: http://$url");