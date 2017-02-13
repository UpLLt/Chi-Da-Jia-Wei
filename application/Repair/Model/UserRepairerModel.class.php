<?php

namespace Repair\Model;

use Think\Model;

class UserRepairerModel extends Model
{
    protected $tableName = 'user_repairer';
    protected $_auto = array(
        array('phone','/^1[34578]\d{9}$/','手机号码错误！','0','regex',self::MODEL_BOTH),
    );
}