<?php
namespace Order\Controller;
use Common\Controller\HomebaseController;

class IndexController extends HomebaseController{
	
	function index(){
        $this->display();
	}

	function show(){
        $id = $_GET['id'];

        $this->display(':show');

    }
}