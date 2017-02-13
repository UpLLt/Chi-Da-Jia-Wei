<?php
namespace Common\Model;
use Common\Model\CommonModel;
class FarOrderModel extends CommonModel
{
	protected $tableName = 'order_far_order';
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('picture', 'require', '图片不能为空！', 1, 'regex', 3),
		array('order_number', 'require', '订单编号不能为空！', 1, 'regex', 3),
		array('far_price', 'require', '价格不能为空！', 1, 'regex', 3),
		array('address', 'require', '地址不能为空！', 1, 'regex', 3),
		array('repair_name', 'require', '订单编号不能为空！', 1, 'regex', 3),
	);

	protected $_auto = array (
		array('create_time','time',self::MODEL_BOTH,'function'),
	);

	
}




