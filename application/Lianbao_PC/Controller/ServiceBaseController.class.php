<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/17
 * Time: 10:24
 */
namespace Lianbao_PC\Controller;

use Think\Controller;

class ServiceBaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_login();
        $this->own_mess();
    }

    public function is_login()
    {
        $user_id = session('user_id');
        $map['id'] = $user_id;
        $map['user_type'] = 3;
        $map['status'] = 1;
        $result = D('user')->where($map)->field('username')->find();
        if (!$result) {
            $this->redirect('Login/index', '', 3, '未登陆，请您登陆');
        }
    }

    public function login_out()
    {
        $user = session('user_id', null);
        if (empty($user)) $this->redirect('Login/index', '', 0);

    }

    private function own_mess()
    {
        $map['user_id'] = session('user_id');
        $map_user['id'] = session('user_id');
        $wallet = D('wallet_seller')->where($map)->field('all_money')->find();
        $wallet['all_money']  = sprintf("%.2f", $wallet['all_money']);
        $wallet['all_money'] = empty($wallet['all_money']) ? 0 : $wallet['all_money'];

        $count  = D('order')->where(array('repair_service_id'=>session('user_id'),'status'=>7))->count();
        $own_mess = D('user_service')->where(array('user_id'=>session('user_id')))->field('business_license,company')->find();
        $wallet['order_count'] = $count;
        $username = D('user')->where($map_user)->field('username')->find();
        $this->assign('wallet', $wallet);
        $this->assign('username', $username);
        $this->assign('own_mess',$own_mess);
    }

    public function loginout()
    {
        $user = session('user_id', null);
        if (empty($user))
            $this->redirect('Login/index', '', 0);
    }
}