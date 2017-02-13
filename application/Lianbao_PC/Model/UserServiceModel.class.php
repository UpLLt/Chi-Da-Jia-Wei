<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 14:11
 */
namespace Lianbao_PC\Model;
use Think\Model;
class UserServiceModel extends Model{
    protected $tableName = "user_service";
    protected $_validate = array(
        array('phone','^1[3|4|5|8][0-9]\d{4,8}$','手机号码错误！','0','regex',1),
    );
}