<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3
 * Time: 18:49
 */
namespace Lianbao_PC\Model;
use Think\Model;
class OrderLogisticsModel extends Model{
    protected $tableName = 'order_logistics';
    protected $_validate = array(
        array('logistics_com','require','请选择物流公司。'),
        array('logistics_danhao','number','请输入正确的物流单号。'),
        array('logistics_money','number','请输入正确的物流费用。'),
        array('logistics_money','require','请输入支付方式。'),
    );
    protected $_auto = array (
        array('create_time','time',self::MODEL_BOTH,'function'),
    );

}