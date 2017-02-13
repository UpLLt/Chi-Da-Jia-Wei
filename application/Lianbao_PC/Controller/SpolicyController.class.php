<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 9:16
 */
namespace Lianbao_PC\Controller;

use Think\Controller;

class SpolicyController extends ServiceBaseController
{
    protected $policy_model;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    public function initialize()
    {
        $this->policy_model = D('policy');
    }

    public function policy()
    {
        $map['type'] = 1;
        $map['status'] = 2;
        $list = $this->policy_model->where($map)->order('create_time')->limit(1)->find();
        $list['content'] = html_entity_decode($list['content']);
        $this->assign('list', $list);
        $this->display('policy');
    }

    public function guide()
    {
        $map['type'] = 2;
        $map['status'] = 2;
        $list = $this->policy_model->where($map)->order('create_time')->limit(1)->find();
        $list['content'] = html_entity_decode($list['content']);
        $this->assign('list', $list);
        $this->display('guide');
    }

    public function rules()
    {
        $map['type'] = 3;
        $map['status'] = 2;
        $list = $this->policy_model->where($map)->order('create_time')->limit(1)->find();
        $list['content'] = html_entity_decode($list['content']);
        $this->assign('list', $list);
        $this->display('rules');
    }

    public function required()
    {
        $map['type'] = 4;
        $map['status'] = 2;
        $list = $this->policy_model->where($map)->order('create_time')->limit(1)->find();
        $list['content'] = html_entity_decode($list['content']);
        $this->assign('list', $list);
        $this->display('required');
    }

    public function matter()
    {
        $map['type'] = 5;
        $map['status'] = 2;
        $list = $this->policy_model->where($map)->order('create_time')->limit(1)->find();
        $list['content'] = html_entity_decode($list['content']);
        $this->assign('list', $list);
        $this->display('matter');
    }
}