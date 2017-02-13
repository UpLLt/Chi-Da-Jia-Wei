<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 10:50
 */
namespace Repair\Model;

use Think\Model;

class UserModel extends Model
{
    protected $tableName = 'user';
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_BOTH, 'function'),
    );
}