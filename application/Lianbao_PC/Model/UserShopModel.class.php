<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3
 * Time: 18:49
 */
namespace Lianbao_PC\Model;
use Think\Model;
class UserShopModel extends Model{
    protected $tableName = 'user_shop';
    protected $_validate = array(
        array('name','require','姓名不能为空。'),
        array('company','require','公司不能为空'),
        array('user_phone','require','电话不能为空。'),
        array('skill_phone','require','技术电话不能为空'),
        array('service_phone','require','客服电话不能为空。'),
        array('detail_address','require','公司地址不能为空'),
        array('qq','require','QQ不能为空。'),
        array('business_license','require','营业执照不能为空'),
    );


}