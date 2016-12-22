<?php
namespace Store\Controller;
use Common\Controller\HomebaseController;

class IndexController extends HomebaseController{
	
	function index(){
	    echo 1;
        die();
        $this->display();
	}
}