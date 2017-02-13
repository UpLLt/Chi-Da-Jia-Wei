<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class UserChargeController extends AdminbaseController
{
    public function index(){
        $count = D('wallet_recharge')->count();
        $page = $this->page($count, 20);
        $list = M('wallet_recharge')->order('create_time DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        $show = $page->show('Admin');

        for($i=0;$i<count($list);$i++){
            if($list[$i]['pay_type'] == 1)  $list[$i]['pay_type'] = "微信支付";
            if($list[$i]['pay_type'] == 2)  $list[$i]['pay_type'] = "后台添加";
            if($list[$i]['pay_type'] == 3)  $list[$i]['pay_type'] = "支付宝支付";
            $user_shop = D('user')->where(array('id'=>$list[$i]['user_id']))->field('username')->find();
            $list[$i]['user_id'] = $user_shop['username'];
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display('index');
    }

    /**
     * 查询充值记录
     */

    public function serach(){
        $username = I('username');
        $id = D('user')->where(array('username'=>$username))->field('id')->find();
        $count = D('wallet_recharge')->where(array('user_id'=>$id['id']))->count();
        $page = $this->page($count, 20);
        $list = M('wallet_recharge')->where(array('user_id'=>$id['id']))->order('create_time DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        $show = $page->show('Admin');

        for($i=0;$i<count($list);$i++){
            if($list[$i]['pay_type'] == 1)  $list[$i]['pay_type'] = "微信支付";
            if($list[$i]['pay_type'] == 2)  $list[$i]['pay_type'] = "后台添加";
            if($list[$i]['pay_type'] == 3)  $list[$i]['pay_type'] = "支付宝支付";
            $user_shop = D('user_shop')->where(array('user_id'=>$list[$i]['user_id']))->field('name')->find();
            $list[$i]['user_id'] = $user_shop['name'];
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display('index');
    }

    /**
     * 充值列表
     */
    public function add_list(){
        $this->display('add');
    }

    /**
     * 添加充值
     */
    public function add(){
        $username = I('post.username');
        $money    = I('post.money');
        $username = D('user')->where(array('username'=>$username,'user_type'=>2))->field('id')->find();
        if($username['id']){
            $m = D('wallet_buyers');

            $m->startTrans();
            $wallet_buyers = $m->where(array('user_id'=>$username['id']))->field('balance,recharge_money')->find();
            $save['balance']        = $wallet_buyers['balance'] + $money;
            $save['recharge_money'] =  $money + $wallet_buyers['recharge_money'];

            $res = $m->where(array('user_id'=>$username['id']))->save($save);

            $add['recharge_number'] = liushuihao();
            $add['user_id']         = $username['id'];
            $add['create_time']     = time();
            $add['recharge_money']  = $money;
            $add['pay_money']       = $money;
            $add['pay_type']        = '2';
            $add['pay_time']        = time();
            $add['present_money']   = 0;
            $add['recharge_title']  = '后台充值';

            $result = D('wallet_recharge')->add($add);


            $add_re['liushuihao']     = $add['recharge_number'];
            $add_re['order_price']    = $money;
            $add_re['create_time']    = time();
            $add_re['user_id']        = $username['id'];
            $add_re['type']           = 5;

            D('wallet_service_consum')->add($add_re);

            if( $result && $res){
                $m->commit();
                $this->success('充值成功',U('UserCharge/index'));
            }else{
                $m->rollback();
                $this->error('充值失败',U('UserCharge/index'));
            }
        }else{
            $this->error('添加失败，该商家账号不存在');
        }
    }

}