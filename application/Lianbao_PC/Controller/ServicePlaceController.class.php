<?php
namespace Lianbao_PC\Controller;

use Think\Controller;

class ServicePlaceController extends IndexBaseController
{
    public function index()
    {
        // $this->serviceplace();
        $map['status']  = 1;
        $map['term_id'] = 15;
        $list = M('posts')->where($map)->order('post_date DESC')->limit(1)->select();
       
        $this->assign('list',$list);
        $this->display();
    }


    /*
     * 服务网点   
     */
    public function serviceplace()
    {
        $User = M('user_service');
        $shop_location = I('city');

        if (!empty($shop_location))
            $where['shop_location'] = $shop_location;
            $_GET['city']  = $shop_location;
        $count = $User
            ->where($where)
            ->count();
        $Page = new \Think\Page($count, 10);

        $show = $Page->show();
        $list = $User->order('create_time')
            ->where($where)
            ->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);

    }

    public function serviceplace_detail()
    {
        $id = I('get.id');
        $User = M('user_service');
        $list = $User->where("id='{$id}'")->find();
        $this->assign('list', $list);
        $this->display('serviceplace_detail');
    }

    public function search()
    {
        $shop_location = I('city');
        $where['shop_location'] = array('like',array('%'.$shop_location.'%'));
        $list = M('user_service')->where($where)->select();
        $this->assign('list', $list);
        $this->display('index');
    }


}