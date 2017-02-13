<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/6
 * Time: 22:19
 */
namespace Repair\Model;

use Think\Model;
class ComplaintModel extends Model{
    protected $_validate = array(
      array('content','require','内容不能为空'),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_BOTH, 'function'),
        array('complaint_type', '2'),
    );
}