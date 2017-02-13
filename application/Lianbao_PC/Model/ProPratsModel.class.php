<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/23
 * Time: 11:44
 */
namespace Lianbao_PC\Model;
use Think\Model;
class ProPratsModel extends Model{
    protected $tableName = 'pro_parts';
    protected $_validate = array(
        array('parts_picture','require','图片必须是两张图片。'),
        array('all_picture','require','图片必须是两张图片。'),
        array('parts_count','number','请输入正确的数字！'),
        array('parts_price','number','请输入正确的价格！'),
    );
    protected $_auto = array (
        array('create_time','time',self::MODEL_BOTH,'function'),
    );
}