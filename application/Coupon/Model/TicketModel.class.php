<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/23
 * Time: 11:44
 */
namespace Coupon\Model;
use Think\Model;
class TicketModel extends Model{
    protected $_validate = array(
        array('price','require','价格不能为空。'),
        array('describe','require','内容不能为空。'),
        array('vaildity','require','过期时间不能为空。'),

    );

    protected $_auto = array (
//        array('create_time','time',self::MODEL_BOTH,'function'),
    );

}