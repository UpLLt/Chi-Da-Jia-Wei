<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/6
 * Time: 20:38
 */
namespace Repair\Model;

use Think\Model;

class ApplyServiceModel extends Model{
    protected $tableName = 'apply_service';
    protected $_validate = array(
        array('business_license','require','营业执照不能为空。'),
        array('tax_regis','require','税务登记证不能为空。'),
        array('title','require','特殊资质证不能为空。'),
        array('faren_name','require','法人姓名不能为空。'),
        array('repair_id','require','用户id姓名不能为空。'),
        array('company','require','公司名不能为空。'),
        array('email','email','邮箱格式不符合要求。'),
        array('phone','/^1[34578]\d{9}$/','手机号码错误！','0','regex',self::MODEL_BOTH),
        array('card_number','/^[0-9]*$/','请输入正确的银行卡号！','0','regex',self::MODEL_BOTH),

    );

}