<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class InformController extends AdminbaseController
{
    public function index()
    {

        $this->display();
    }

    public function edit()
    {
        $id = I('id');
        $map['id'] = $id;
        $inform_user = M('inform_user')->where($map)->order('create_time desc')->find();
        $inform_user['type'] == 1 ? $a = " selected='selected' " : ' ';
        $inform_user['type'] == 2 ? $b = " selected='selected' " : ' ';
        $inform_user['type'] == 3 ? $c = " selected='selected' " : ' ';
        $inform_user['type'] == 4 ? $f = " selected='selected' " : ' ';
        $inform_user['type'] == 5 ? $g = " selected='selected' " : ' ';
        $inform_user['type'] == 5 ? $h = " selected='selected' " : ' ';
        $type = "<option " . $a . " value='1'>发给买家</option>" .
                "<option " . $b . " value='2'>发给卖家</option>" ;


        $inform_user['status'] == 1 ? $d = " selected='selected' " : ' ';
        $inform_user['status'] == 2 ? $e = " selected='selected' " : ' ';
        $status = "<option " . $d . "  value='1'>待发布</option>" .
            "<option " . $e . "  value='2'>发布</option>";
        $this->assign('inform_user', $inform_user);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->display();

    }

    public function add_inform()
    {

        $add = I('post.');
        $id = I('post.inform_id');
        if (empty($id)) {
            $add['create_time'] = time();
            $add['content'] = htmlspecialchars_decode( $add['content']);
            $result = M('inform_user')->add($add);
            if ($result) {
                $this->success("添加成功");
            } else {
                $this->error("添加失败");
            }
        }else{
            $map['id']=$id;
            $result = M('inform_user')->where($map)->save($add);
            if ($result) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        }
    }
    public function detail(){
        $id = I('get.id');
        $map['id'] = $id;
        $inform = M('inform_user')->where($map)->find();
        if($inform['type']==1)$inform['type']='发给买家';
        if($inform['type']==2)$inform['type']='发给卖家';
        if($inform['status']==1)$inform['status']='待发表';
        if($inform['status']==2)$inform['status']='发表';

        $this->assign('inform',$inform);
        $this->display();

    }

    public function info_list()
    {
        $map['type'] = array(array('eq',1),array('eq',2),'or');
        $count = M('inform_user')->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('inform_user')->where($map)->order('create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $page = $page->show('Admin');

        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['type'] == 1) {
                $list[$i]['type'] = '发给买家';
            };
            if ($list[$i]['type'] == 2) {
                $list[$i]['type'] = '发给卖家';
            };
            if ($list[$i]['status'] == 1) {
                $list[$i]['status'] = '待发表';
            };
            if ($list[$i]['status'] == 2) {
                $list[$i]['status'] = '发表';
            };
        }
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display('list');

    }

    public function search()
    {


        $map['type'] = array(array('eq',1),array('eq',2),'or');

        if(empty( I('post.keyword') )){
            $list = M('inform_user')->order('create_time desc')->where($map)->select();
        }else{

            $map['title'] = I('post.keyword');
            $list = M('inform_user')->order('create_time desc')->where($map)->select();
        }


        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['type'] == 1) {
                $list[$i]['type'] = '发给买家';
            };
            if ($list[$i]['type'] == 2) {
                $list[$i]['type'] = '发给卖家';
            };
            if ($list[$i]['status'] == 1) {
                $list[$i]['status'] = '待发表';
            };
            if ($list[$i]['status'] == 2) {
                $list[$i]['status'] = '发表';
            };
        }

        $this->assign('list', $list);
        $this->display('list');
    }

    /**
     * PC端搜索
     */
    public function pc_search()
    {


        $map['type'] = array(array('eq',1),array('eq',2),'or');

        if(empty( I('post.keyword') )){
            $list = M('inform_user')->order('create_time desc')->where($map)->select();
        }else{

            $map['title'] = I('post.keyword');
            $list = M('inform_user')->order('create_time desc')->where($map)->select();
        }


        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['type'] == 1) {
                $list[$i]['type'] = '发给买家';
            };
            if ($list[$i]['type'] == 2) {
                $list[$i]['type'] = '发给卖家';
            };
            if ($list[$i]['status'] == 1) {
                $list[$i]['status'] = '待发表';
            };
            if ($list[$i]['status'] == 2) {
                $list[$i]['status'] = '发表';
            };
        }

        $this->assign('list', $list);
        $this->display('list');
    }

    public function del()
    {
        $map['id'] = I('id');
        $result = M('inform_user')->where($map)->delete();
        if ($result) {
            $this->success("添加成功");
        } else {
            $this->error("添加失败");
        }
    }


}