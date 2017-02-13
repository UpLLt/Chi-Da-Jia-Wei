<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 18:04
 */
namespace Lianbao_PC\Model;
use Think\Model;
class UserCardModel extends Model{
    protected $tableName = 'user_card';


    protected $_validate = array(
        array('card_name','require','持卡人姓名不能为空。'),
        array('card_number','require','银行卡不能为空。'),
        array('phone','/^1[34578]\d{9}$/','手机号码错误！','0','regex',self::MODEL_BOTH),
    );

    protected $_auto = array (
        array('create_time','time',self::MODEL_BOTH,'function'),
    );

}