<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ComplaintController extends AdminbaseController{

    public function index(){
        $map['type'] = 1;

        $count =  M('complaint')->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('complaint')->where($map)->order('create_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        for($i=0;$i<count($list);$i++){
            $user_id = $list[$i]['user_id'];
            $repair = D('user_shop')->where(array('user_id'=>$user_id))->field('company,user_phone')->find();

            $list[$i]['company']    =  $repair['company'];
            $list[$i]['user_phone']        =  $repair['user_phone'];
        }

        $show = $page->show('Admin');
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    public function del(){
        $map['id'] =I('id');

        $result = M('complaint')->where($map)->delete();
        if($result){
            $this->success("成功");
        }else{
            $this->error("失败");
        }

    }

    public function feedback(){
        $map['type'] = '9';
        $count =  M('complaint')->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('complaint')->where($map)->order('create_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        for($i=0;$i<count($list);$i++){
            $user_id = $list[$i]['repair_person_id'];
            $repair = D('user_repairer')->where(array('user_id'=>$user_id))->field('real_name,phone')->find();
            $list[$i]['real_name']    =  $repair['real_name'];
            $list[$i]['phone']        =  $repair['phone'];
        }

        $show = $page->show('Admin');
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display('feedback');
    }

    public function detail(){
        $list = D('complaint')->where(array('id'=>I('id')))->find();
        $this->assign('list',$list);
        $this->display('detail');
    }

}