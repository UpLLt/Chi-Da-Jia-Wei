<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/23
 * Time: 11:44
 */
namespace AdOwn\Model;
use Think\Model;
class LinksModel extends Model{
    protected $_validate = array(
        array('link_name','require','名称不能为空。'),
        array('link_description','require','内容不能为空。'),
        array('link_name','require','图片不能为空。'),

    );

    protected $_auto = array (
//        array('create_time','time',self::MODEL_BOTH,'function'),
    );

}