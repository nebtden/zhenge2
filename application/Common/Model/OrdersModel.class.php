<?php
namespace Common\Model;



class OrdersModel extends CommonModel{

    public static $_state = array(
        -1=>'订单取消',
        0=>'订单生成',
        1=>'支付完成',
        2=>'订单完成',



    );
    public static $_time_index=array(
        21=>'10:30',
        22=>'11:00',
        23=>'11:30',
        24=>'12:00',
        25=>'12:30',
        26=>'13:00',
        27=>'13:30',
        28=>'14:00',
        29=>'14:30',
        30=>'15:00',
        31=>'15:30',
        32=>'16:00',
        33=>'16:30',
        34=>'17:00',
        35=>'17:30',
        36=>'18:00',
        37=>'18:30',
        38=>'19:00',
        39=>'19:30',
    );

	
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('order_sn', 'require', '订单号不能为空！'),
		array('member_id', 'require', '用户不能为空！'),
	);
	
	protected function _before_write(&$data) {
		parent::_before_write($data);

	}

	public function getOrderinfo($id){
	    $order_info = $this->where( array ('id'=>$id))->find();
        return $order_info;
    }
	
}




