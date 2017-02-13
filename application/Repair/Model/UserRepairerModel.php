<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 14:43
 */
namespace Repair\Controller;
use Think\Controller;
class UserRepairer extends Controller{
    protected $tableName = 'user_repairer';
    protected $_validate = array(
        array('parts_picture','require','图片必须是两张图片。'),
        array('all_picture','require','图片必须是两张图片。'),
        array('parts_count','number','请输入正确的数字！'),

    );
}