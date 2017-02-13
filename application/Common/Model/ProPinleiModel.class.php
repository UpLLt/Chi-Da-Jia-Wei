<?php
namespace Common\Model;
use Common\Model\CommonModel;
class   ProPinleiModel extends CommonModel{
     protected $tableName = 'pro_pinlei';
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('pro_pinlei', 'require', '品类名称不能为空！', 1, 'regex', 3),
        array('pinlei_picture', 'require', '图片不能为空！', 1, 'regex', 3),
    );
    protected $_auto = array (
        array('create_time','time',self::MODEL_BOTH,'function'),
    );

}