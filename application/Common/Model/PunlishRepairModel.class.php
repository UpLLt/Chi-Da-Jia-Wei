<?php
namespace Common\Model;
use Common\Model\CommonModel;
class   PunlishRepairModel extends CommonModel{
     protected $tableName = 'punlish_repair';
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('content', 'require', '处罚内容不能为空！', 1, 'regex', 3),
        array('money','/^[0-9]*$/','金钱只能为数字！','0','regex',self::MODEL_BOTH),
    );
    protected $_auto = array (
        array('create_time','time',self::MODEL_BOTH,'function'),
    );

}