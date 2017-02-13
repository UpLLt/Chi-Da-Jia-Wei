<?php
namespace Common\Model;
use Common\Model\CommonModel;
class WalletAdminModel extends CommonModel
{
	protected $tableName = 'wallet_admin';
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('proportion', 'require', '名称不能为空！', 1, 'regex', 3),
			array('no_ser_proportion', 'require', '名称不能为空！', 1, 'regex', 3),
			array('install', 'require', '安装单量不能为空！', 1, 'regex', 3),
			array('repair', 'require', '维修单量不能为空！', 1, 'regex', 3),
			array('send', 'require', '送修单量不能为空！', 1, 'regex', 3),
	);

}




