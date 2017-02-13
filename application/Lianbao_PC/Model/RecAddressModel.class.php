<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 17:34
 */
namespace Lianbao_PC\Model;
use Think\Model;
class RecAddressModel extends Model{
    protected $tableName = 'user_rec_address';
    protected $_validate = array(
        array('rec_address','require','收货地址不能为空。'),
        array('rec_name','require','收货人不能为空。'),
        array('rec_phone','/^1[34578]\d{9}$/','手机号码错误！','0','regex',self::MODEL_BOTH),
    );

}