<?php
namespace Common\Model;


class StoreOrdersModel extends CommonModel{
	
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('store_name', 'require', '店铺名不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('address', 'require', '地址不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);
	

	
}




