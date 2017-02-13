<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/6
 * Time: 20:38
 */
namespace Repair\Model;

use Think\Model;

class UserCardModel extends Model{
    protected $tableName = 'user_card';
    protected $_validate = array(
        array('phone','/^1[34578]\d{9}$/','手机号码错误！','0','regex',self::MODEL_BOTH),
        array('card_number','/^[0-9]*$/','请输入正确的银行卡号！','0','regex',self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_BOTH, 'function'),
        array('card_status', '0'),
    );
}