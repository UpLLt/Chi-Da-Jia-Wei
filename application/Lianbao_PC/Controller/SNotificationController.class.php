<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 17:22
 */
namespace Lianbao_PC\Controller;

use Think\Controller;

class SNotificationController extends ServiceBaseController
{
    protected $inform_user_model;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    private function initialize()
    {
        $this->inform_user_model = D('inform_user');

    }

    public function index()
    {
        $this->inform();
        $this->display('index');
    }

    public function inform()
    {
        $User = $this->inform_user_model;
        $map['type'] = '2' ;
        $count = $User->where($map)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $User->where($map)->order('create_time')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
    }
    public function news_show()
    {
        $map['id'] = I('get.id');
        $result =  $this->inform_user_model->where($map)->find();
        $result['content']  = html_entity_decode($result['content']);
        $this->assign('list',$result);
        $this->display('news_show');
    }

}