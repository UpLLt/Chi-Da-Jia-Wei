<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;
use Repair\Controller\BaseController;

class OrderController extends AdminbaseController
{
    public function index()
    {
        $table = 'order_user';
        $start_time = I('post.start_time');
        $end_time = I('post.end_time');
        $key = I('post.term');
        $key_cond = "status";
        $order_number = I('order_number');
        $status = '';
        $_GET['order_number']   = $order_number;
        $_GET['start_time'] = $start_time;
        $_GET['end_time']   = $end_time;
        $_GET['status']     = $key;

        if(!empty($order_number)){
           $list = $this->select_a($table, $start_time, $end_time, $key, $key_cond,$order_number, $status);
        }else{
           $list = $this->select($table, $start_time, $end_time, $key, $key_cond , $status);
        }

        $key == 1 ? $a = " selected='selected' " : $a = " ";
        $key == 2 ? $b = " selected='selected' " : $b = " ";
        $key == 7 ? $c = " selected='selected' " : $c = " ";
        $key == 8 ? $d = " selected='selected' " : $d = " ";
        $key == 9 ? $e = " selected='selected' " : $e = " ";
        $key == 10 ? $f = " selected='selected' " : $f = " ";
        $key == 11 ? $g = " selected='selected' " : $g = " ";
        $key == 12 ? $h = " selected='selected' " : $h = " ";
        $key == 13 ? $i = " selected='selected' " : $i = " ";
        $key == 16 ? $j = " selected='selected' " : $j = " ";
        $key == 20 ? $s = " selected='selected' " : $s = " ";
        $str =  "<option" . $a . " value='1'>待接单</option>" .
            "<option" . $b . " value='2'>已取消</option>" .
            "<option" . $c . " value='7'>已完结 </option>" .
            "<option" . $d . " value='8'>待服务</option>" .
            "<option" . $e . " value='9'>待预约</option>" .
            "<option" . $f . " value='10'>待指派</option>".
            "<option" . $g . " value='11'>待收件</option>" .
            "<option" . $h . " value='12'>待寄件</option>" .
            "<option" . $i . " value='13'>待收款</option>" .
            "<option" . $i . " value='20'>返件单</option>" .
            "<option" . $j . " value='16'>正在服务中</option>" ;



        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['status'] == 1) $list[$i]['status'] = "待接单";
            if ($list[$i]['status'] == 2) $list[$i]['status'] = "已取消";
            if ($list[$i]['status'] == 7) $list[$i]['status'] = "已完结";
            if ($list[$i]['status'] == 8) $list[$i]['status'] = "待服务";
            if ($list[$i]['status'] == 9) $list[$i]['status'] = "待预约";
            if ($list[$i]['status'] == 10) $list[$i]['status'] = "待指派";
            if ($list[$i]['status'] == 11) $list[$i]['status'] = "待收件";
            if ($list[$i]['status'] == 12) $list[$i]['status'] = "待寄件";
            if ($list[$i]['status'] == 13) $list[$i]['status'] = "待收款";
            if ($list[$i]['status'] == 16) $list[$i]['status'] = "正在服务中";
            if ($list[$i]['status'] == 20) $list[$i]['status'] = "返件单";
            $repair_price =  D('order')->where(array('order_number'=>$list[$i]['order_number']))->field('repair_price')->find();
            $list[$i]['repair_price'] = $repair_price['repair_price'];
        }
        $this->assign('str', $str);

        $this->assign('list', $list);
        $this->display();
    }

    public function detail()
    {
        $model_order = M('order');
        $model_user = M('order_user');
        $model_pro = M('order_pro');
        $id = intval(I('id'));
        $order_number = $model_user->where("id='{$id}'")->field('order_number')->find();
        $map['order_number'] = $order_number['order_number'];
        $order = $model_order->where($map)->find();
        $user = $model_user->where($map)->find();
        $pro = $model_pro->where($map)->find();
        $username = M('user')->where("id='{$order['user_id']}'")->field('username')->find();
        $repair_person = M('user')->where("id='{$order['repair_person_id']}'")->field('username')->find();
        $repair_service = M('user')->where("id='{$order['repair_service_id']}'")->field('username')->find();
        $order['user_id'] = $username['username'];
        $order['repair_person_id'] = $repair_person['username'];
        $order['repair_service_id'] = $repair_service['username'];
        if ($order['status'] == 1) $order['status'] = "待接单";
        if ($order['status'] == 2) $order['status'] = "已取消";
        if ($order['status'] == 7) $order['status'] = "已完结";
        if ($order['status'] == 8) $order['status'] = "待服务";
        if ($order['status'] == 9) $order['status'] = "待预约";
        if ($order['status'] == 10) $order['status'] = "待指派";
        if ($order['status'] == 11) $order['status'] = "待收件";
        if ($order['status'] == 12) $order['status'] = "待寄件";
        if ($order['status'] == 13) $order['status'] = "待收款";
        if ($order['status'] == 16) $order['status'] = "正在服务中";
        if ($order['status'] == 20) $order['status'] = "返件单";
        if ($order['order_type'] == 1) $order['order_type'] = "安装单";
        if ($order['order_type'] == 2) $order['order_type'] = "维修单";
        if ($order['order_type'] == 3) $order['order_type'] = "送修单";
        if ($pro['baoxiu_type']  == 1) $pro['baoxiu_type'] = "保修内";
        if ($pro['baoxiu_type']  == 2) $pro['baoxiu_type'] = "保修外";
        $parts = D('pro_parts')->where($map)->select();
        for($i=0;$i<count($parts);$i++){
            if( $parts[$i]['type'] == 1 && $pro['baoxiu_type'] == 1 ) $parts[$i]['parts_price'] = 0 ;
        }
        $repair = M('order_repair')->where($map)->find();
        if($pro['order_type'] == 1 ){
            $add_service = D('order_add_service')->where($map)->field('add_service')->limit(1)->select();
        }else{
            $add_service = D('order_add_service')->where($map)->field('add_service')->select();
        }
           $service = '';
           for($i=0;$i<count($add_service);$i++){
               $service .= $add_service[$i]['add_service'].',';
           }
        $service = rtrim($service, ',');

        $order_logistics = D('order_logistics')->where($map)->find();
        $this->assign('order_logistics',$order_logistics);
        $this->assign('service',$service);
        $this->assign('parts',$parts);
        $this->assign('repair', $repair);
        $this->assign('order', $order);
        $this->assign('user', $user);
        $this->assign('pro', $pro);
        $this->display('detail');

    }

    public function select($table, $start_time = "", $end_time = "", $key = "", $key_cond = "",$username="", $status = "")
    {


        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        if (empty($start_time) || empty($end_time)) {
            if (empty($key)) {
                if (empty($status)) {
                    $count = M($table)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            } else {
                $keywords = $key;
                $keys[$key_cond] = array('like', $keywords);
                if (empty($status)) {
                    $count = M($table)->where($keys)->order('id desc')->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($keys)->limit($page->firstRow . ',' . $page->listRows)->select();

                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->where($keys)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->where($keys)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            }
        } else {
            $time = "create_time>='{$start_time}' and create_time<='{$end_time}'";
            if (empty($key)) {
                if (empty($status)) {
                    $count = M($table)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($time)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();


                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->order('id desc')->where($time)->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            } else {
                $keywords = $key;
                $keys[$key_cond] = array('like', $keywords);
                if (empty($status)) {
                    $count = M($table)->where($keys)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($keys)->where($time)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();


                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->where($keys)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->where($keys)->where($time)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            }
        }

        if (empty($start_time) || empty($end_time)) {
            $tim['start_time'] = '1430452800';
            $tim['end_time'] = time();
        } else {
            $tim['start_time'] = $start_time;
            $tim['end_time'] = $end_time;
        }
        $show = $page->show('Admin');


        $this->assign('time', $tim);
        $this->assign('list', $list);
        $this->assign('page', $show);
        return $list;
    }

    public function examine()
    {
        $map['status'] = 13;
        $count = M('order_user')->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('order_user')->where($map)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $show = $page->show('Admin');
        for ($i = 0; $i < count($list); $i++) {
            $repair_price = D('order')->where(array('order_number'=>$list[$i]['order_number']))->field('repair_price')->find();
            $list[$i]['status'] = '待付款';
            $list[$i]['pro_price'] = $repair_price['repair_price'];
        }
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('examine');

    }


    /**
     * 确认支付
     */
    public function examine_price()
    {
        $model = D('order');
        $model->startTrans();
        $roll_va = true;
        $is_status = D('order')->where(array('order_number'=>I('order_number')))->field('status')->find();

        if($is_status['status']!= 13){
            $this->error('工单状态不正确，不能支付');
        }

        $map['order_number'] = I('order_number');
        $id = D('order')->where($map)->field('user_id,repair_person_id,repair_service_id,service_price,parts_price,far_price,repair_price,logistics_price')->find();
        $order_pro = D('order_pro')->where($map)->field('baoxiu_type,order_type')->find();
        $wallet_buyers = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->find();
        $save_buyers['balance']   = $wallet_buyers['balance']   - $id['repair_price'];
        $save_buyers['use_money'] = $wallet_buyers['use_money'] + $id['repair_price'];
        $save_buyers['frozen_balance']   = $wallet_buyers['frozen_balance'] - $id['repair_price'];
        $save_change_wallet = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->save($save_buyers);

        if(empty($save_change_wallet)){
            $roll_va = false;
        }

        //获取平台提成比例
        $proportion = D('wallet_admin')->field('proportion,no_ser_proportion')->find();
        $map_repairer['user_id'] = $id['repair_person_id'];
        $map_service['user_id'] = $id['repair_service_id'];
        $repairer = D('user_repairer')->where($map_repairer)->field('parent_id,type,proportion')->find();


        if (!empty($repairer['proportion'])) {
            $pintai      = $id['service_price'] * $proportion['proportion'];
            $pintai      = sprintf("%.2f",$pintai);

            $service_all = $id['service_price'] * (1 - $proportion['proportion']);
            $service_all      = sprintf("%.2f",$service_all);

            $repair_ticheng = $service_all  * (1 - $repairer['proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'];
            $repair_ticheng      = sprintf("%.2f",$repair_ticheng);

            $service_ticheng = $service_all * $repairer['proportion'];
            $service_ticheng      = sprintf("%.2f",$service_ticheng);

            //添加至服务中心钱包
            $seller = D('wallet_seller')->where($map_service)->field('balance,all_money')->find();
            $save_ser['balance'] = $seller['balance'] + $service_ticheng;
            $save_ser['all_money'] = $seller['all_money'] + $service_ticheng;
            $se = D('wallet_seller')->where($map_service)->save($save_ser);

            if(empty($se)){
                $roll_va = false;
            }

            //添加至师傅钱包
            $repair = D('wallet_repairer')->where($map_repairer)->field('balance,all_money,taking_money,service_coucheng')->find();
            $save_repair['balance'] = $repair['balance'] + $repair_ticheng;
            $save_repair['all_money'] = $repair['all_money'] + $repair_ticheng;
            $save_repair['taking_money'] = $repair['taking_money'] + $repair_ticheng;
            $save_repair['service_coucheng'] = $repair['service_coucheng'] + $service_ticheng;
            $result = D('wallet_repairer')->where($map_repairer)->save($save_repair);

            if(empty($result)){
                $roll_va = false;
            }

            //增加至income表
            $add_income['order_number'] = I('order_number');
            $add_income['create_time']  = time();
            $add_income['pintai']       = $pintai;
            $add_income['service']      = $service_ticheng;
            $add_income['repair']       = $repair_ticheng;
            $add_income['far_price']    = $id['far_price'];
            $income = D('income')->add($add_income);
            if(empty($income)){
                $roll_va = false;
            }

            //添加到wallet_service_consumm表

            $add_service['create_time']    = time();
            $add_service['order_number']   = I('order_number');
            $add_service['order_price']    = $id['repair_price'];
            $add_service['service_shouru'] = $service_ticheng;
            $add_service['pintai_shouru']  = $pintai;
            $add_service['repairer_price'] = $repair_ticheng;
            $add_service['type']           = "1";
            $add_service['user_id']        = $id['user_id'];
            $add_service['repairer_id']    = $id['repair_person_id'];
            $add_service['service_id']     = $id['repair_service_id'];
            $add_service['buyer_id']       = $id['user_id'];
            $add_service['liushuihao']     = I('order_number');

            $wallet_service_consum = D('wallet_service_consum')->add($add_service);


            if(empty($wallet_service_consum)){
                $roll_va = false;
            }


        }
        if ($repairer['type'] == 1) {
            $pintai      = $id['service_price'] * $proportion['proportion'];
            $service_all = $id['service_price'] * (1 - $proportion['proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'] ;
            $seller = D('wallet_seller')->where($map_service)->field('balance,all_money')->find();
            $save_ser['balance'] = $seller['balance'] + $service_all;
            $save_ser['all_money'] = $seller['all_money'] + $service_all;
            $se = D('wallet_seller')->where($map_service)->save($save_ser);
            if(empty($se)){
                $roll_va = false;
            }
            $add_income['order_number'] = I('order_number');
            $add_income['create_time']  = time();
            $add_income['pintai']       = $pintai;
            $add_income['service']      = $service_all;
            $add_income['repair']       = 0;
            $add_income['far_price']    = $id['far_price'];
            $income = D('income')->add($add_income);

            if(empty($income)){
                $roll_va = false;
            }

            //添加到wallet_service_consumm表

            $add_service['create_time']    = time();
            $add_service['order_number']   = I('order_number');
            $add_service['order_price']    = $id['repair_price'];
            $add_service['service_shouru'] = $service_all;
            $add_service['pintai_shouru']  = $pintai;
            $add_service['repairer_price'] = 0;
            $add_service['type']           = "1";
            $add_service['user_id']        = $id['user_id'];
            $add_service['repairer_id']    = $id['repair_person_id'];
            $add_service['service_id']     = $id['repair_service_id'];
            $add_service['buyer_id']       = $id['user_id'];
            $add_service['liushuihao']     = I('order_number');

            $wallet_service_consum = D('wallet_service_consum')->add($add_service);

            if(empty($wallet_service_consum)){
                $roll_va = false;
            }

        }

        if ($repairer['type'] == 3 || $repairer['type'] == 4 || empty($repairer['parent_id'])) {
            $pintai      = $id['service_price'] * $proportion['no_ser_proportion'];
            $pintai      = sprintf("%.2f",$pintai);

            $repair_all  = $id['service_price'] * (1 - $proportion['no_ser_proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'] ;

            $repair_all      = sprintf("%.2f",$repair_all);

            $repair = D('wallet_repairer')->where($map_repairer)->field('balance,all_money,taking_money')->find();
            $save_repair['balance'] = $repair['balance'] + $repair_all;
            $save_repair['all_money'] = $repair['all_money'] + $repair_all;
            $save_repair['taking_money'] = $repair['taking_money'] + $repair_all;
            $re = D('wallet_repairer')->where($map_repairer)->save($save_repair);

            if(empty($re)){
                $roll_va = false;
            }




            //添加到wallet_service_consumm表

            $add_service['create_time']    = time();
            $add_service['order_number']   = I('order_number');
            $add_service['order_price']    = $id['repair_price'];
            $add_service['service_shouru'] = 0;
            $add_service['pintai_shouru']  = $pintai;
            $add_service['repairer_price'] = $repair_all;
            $add_service['type']           = "1";
            $add_service['user_id']        = $id['user_id'];
            $add_service['repairer_id']    = $id['repair_person_id'];
            $add_service['service_id']     = $id['repair_service_id'];
            $add_service['buyer_id']       = $id['user_id'];
            $add_service['liushuihao']     = I('order_number');

            $wallet_service_consum = D('wallet_service_consum')->add($add_service);

            if(empty($wallet_service_consum)){
                $roll_va = false;
            }

        }

        $status['status'] = '7';
        $order = D('order')->where($map)->save($status);

        $order_user = D('order_user')->where($map)->save($status);
        $end_time['end_time'] = time();
        $end_time['pay_time'] = time();
        $time = D('order_repair')->where($map)->save($end_time);

        if(empty($time) ||  empty($order) || empty($order_user)){
            $roll_va = false;
        }


        if($order_pro['baoxiu_type']==2){

            if($repairer['type'] == 1){
                $save_se_balance =  D('wallet_seller')->where(array('user_id'=>$id['repair_service_id']))->field('balance')->find();
                $save['balance'] = $save_se_balance['balance']-$id['repair_price'];
                $walle_seller = D('wallet_seller')->where(array('user_id'=>$id['repair_service_id']))->save($save);
                if(empty($walle_seller)){
                    $roll_va = false;
                }
            }else{

                $save_se_balance =  D('wallet_repairer')->where(array('user_id'=>$id['repair_person_id']))->field('balance')->find();
                $save['balance'] = $save_se_balance['balance']-$id['repair_price'];
                $redaa           =  D('wallet_repairer')->where(array('user_id'=>$id['repair_person_id']))->save($save);
                if(empty($redaa)){
                    $roll_va = false;
                }
            }
        }

            $data['content'] = "平台强制付款";
            $data['create_time']    =time();
            $data['person']  ="驰达家维客服";
            $data['order_number'] = I('order_number');
            $result = D('order_track')->add($data);
            if(empty($result)){
                $roll_va = false;
            }

        if ($roll_va == true) {
            $model->commit();
            $this->success('付款成功');
        } else {
            $model->rollback();
            $this->error('付款失败');
        }
    }


    /**
     * 提成比例
     */
    public function proportion()
    {
        $proportion = D('wallet_admin')->find();
        $this->assign('proportion', $proportion);
        $this->display('proportion');
    }

    public function proportion_edit()
    {
        $data   = I('post.');
        $data['create_time'] = time();
        $pro = D('WalletAdmin');
        $id = $pro->field('id')->find();
        if ($pro->create($data)) {
            $where['id'] = $id['id'];
            $result = $pro->where($id)->save($data);
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        } else {
            exit($pro->getError());
        }

    }

    public function far_const_order_list()
    {
        $count = D('order_far_order')->count();
        $page = $this->page($count, 20);
        $list = D('order_far_order')->order('create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        for($i=0;$i<count($list);$i++){
            if($list[$i]['status'] == 1 ) $list[$i]['status'] = '待确认';
            if($list[$i]['status'] == 2 ) $list[$i]['status'] = '已同意';
            if($list[$i]['status'] == 3 ) $list[$i]['status'] = '已拒绝';
        }
        $show = $page->show('Admin');
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('far_const_list');
    }
    public function search_far_order(){
        $map['order_number'] = I('order_number');
        $list = D('order_far_order')->where($map)->select();
        for($i=0;$i<count($list);$i++){
            if($list[$i]['status'] == 1 ) $list[$i]['status'] = '待确认';
            if($list[$i]['status'] == 2 ) $list[$i]['status'] = '已同意';
            if($list[$i]['status'] == 3 ) $list[$i]['status'] = '已拒绝';
        }
        $this->assign('list', $list);
        $this->display('far_const_list');
    }
    public function search_order()
    {
        $map['order_number'] = I('order_number');
        $list = D('order_far_order')->where($map)->order('create_time desc')->select();
        for($i=0;$i<count($list);$i++){
            if($list[$i]['status'] == 1 ) $list[$i]['status'] = '待确认';
            if($list[$i]['status'] == 2 ) $list[$i]['status'] = '已同意';
            if($list[$i]['status'] == 3 ) $list[$i]['status'] = '已拒绝';
        }
        $this->assign('list', $list);
        $this->display("far_const_list");
    }

    public function add_far_const_list()
    {
        $this->display('add_far_const_list');
    }

    public function add_far_const()
    {
        $User = D('FarOrder');
        $picture = I('post.photos_url');
        $picture = implode(',', $picture);
        $data = I('post.');
        $data['create_time'] = time();
        $data['picture'] = $picture;
        $price = D('order')->where(array('order_number'=>$data['order_number']))->field('user_id,repair_price,far_price')->find();
        $data['fadan_id'] = $price['user_id'];
        $data['status']   = 1;
        $user_map['username'] = I('repair_name');
        $user_map['user_type']     = 4;

        if ($User->create($data)) {
            $is_re = $User->where(array('order_number'=>$data['order_number']))->find();
            if($is_re['id']){
                $this->error('添加失败,该订单已添加为远程订单');
            }
            if(!$price['user_id'])  $this->error('添加失败,该订单不存在');

            $track['order_number'] = $data['order_number'];
            $track['create_time']  = time();
            $track['content']      = "联保添加远程费用为".$data['far_price'];
            $track['person']       = "联保客服";
            $tra = D('order_track')->add($track);

            $user_shop = D('user_shop')->where(array('user_id'=>$price['user_id']))->field('user_phone')->find();
            $this->send_info($mobile = $user_shop['user_phone'] , $price = $data['far_price'] );

            $result = $User->add($data);
            if ($result  && $tra) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        } else {
            exit($User->getError());
        }
    }

    public function del_far_order(){
        $map['id'] = I('id');
        $result = D('FarOrder')->where($map)->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    public function edit_far_order_list(){
        $map['id'] = I('id');
        $result = D('FarOrder')->where($map)->find();
        $this->assign('list',$result);
        $this->display('edit_far_const_list');
    }


    public function edit_far_const()
    {
        $User = D('FarOrder');
        $picture = I('post.photos_url');
        $map['id'] = I('id');
        $picture = implode(',', $picture);
        $data = I('post.');
        $data['create_time'] = time();
        $data['picture'] = $picture;
        if ($User->create($data)) {
            $result = $User->where($map)->save($data);
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        } else {
            exit($User->getError());
        }
    }

    /**
     * 远程单详情页列表
     */
    public function detail_far_list(){
        $map['id'] = I('id');
        $str = '';
        $result = D('FarOrder')->where($map)->find();
        $picture = explode(',',$result['picture']);
        for($i=0;$i<count($picture);$i++){
            $str .= "<img style='width:413px' src='".$picture[$i]."'> ";
        }
        $this->assign('picture',$str);
        $this->assign('list',$result);
        $this->display('detail_far_const_list');
    }


    /**
     * 师傅列表
     */
    public function repair_list(){
        $count = D('user_repairer')->count();
        $page = $this->page($count, 20);
        $list = D('user_repairer')->field('id,user_id,real_name,qq,email,address,city,phone')->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        $show = $page->show('Admin');
        for($i=0;$i<count($list);$i++){
            $map['id'] = $list[$i]['user_id'];
            $username = D('user')->where($map)->field('username')->find();
            $list[$i]['username'] = $username['username'];
            $list[$i]['phone']    = empty( $list[$i]['phone']) ? $username['username'] : $list[$i]['phone'];
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display('repair_list');
    }


    public function repair_detail(){

        $user_id = I('id');

        $user_type = D('user')->where(array('id'=>$user_id))->field('username,user_type')->find();
        $data['username'] = $user_type['username'];

        if($user_type['user_type'] == 2){
            $data = D('user_shop')->where(array('user_id'=>$user_id))->find();
            $data['username'] = $user_type['username'];
            $this->assign('list',$data);
            $this->display('shop_detail');

        }else if($user_type['user_type'] == 3){
            $data = D('user_service')->where(array('user_id'=>$user_id))->find();
            $data['username'] = $user_type['username'];
            $this->assign('list',$data);
            $this->display('service_detail');

        }else if($user_type['user_type'] == 4){
            $data = D('user_repairer')->where(array('user_id'=>$user_id))->find();
            $data['username'] = $user_type['username'];
            $user_service = D('user_service')->where(array('user_id'=>$data['parent_id']))->field('company')->find();
            $data['parent_com'] = $user_service['company'];
            if( $data['type'] ==1 )  $data['type'] = "直属成员";
            if( $data['type'] ==2 )  $data['type'] = "附属成员";
            if( $data['type'] ==3 )  $data['type'] = "申请成员中";
            if( $data['type'] ==4 )  $data['type'] = "申请成员中";
            $this->assign('list',$data);
            $this->display('repairer_detail');
        }

    }


    /**
     * 搜索师傅
     */
    public function search_repair()
    {
        $id = D('user')->where(array('username'=>I('username')))->field('id')->find();
        $_GET['username'] = I('username');
        $where['user_id'] = $id['id'];
        $where['city']    = I('username');
        $where['_logic'] = 'OR';

        $count = D('user_repairer')->where($where)->count();
        $page = $this->page($count, 20);
        $list = D('user_repairer')->where($where)->field('user_id,real_name,qq,email,address,city,phone')->limit($page->firstRow . ',' . $page->listRows)->order('id desc')->select();
        $show = $page->show('Admin');
        for($i=0;$i<count($list);$i++){
            $map['id'] = $list[$i]['user_id'];

            $username = D('user')->where($map)->field('username')->find();
            $list[$i]['username'] = $username['username'];
        }


        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display('repair_list');
    }
    /**
     * 处罚师傅页
     */

    public function punish_repairer_list(){
        $data =  D('user_repairer')->where(array('user_id'=>I('user_id')))->field('real_name,phone')->find();
        $user =  D('user')->where(array('id'=>I('user_id')))->field('username')->find();
        $data['repair_id'] = I('user_id');
        $data['username'] = $user['username'];
        $this->assign('list',$data);
        $this->display('punish_repairer');
    }

    /**
     * 处罚师傅
     */
    public function punish_repairer(){

        $order = D('order')->where(array('order_number'=>I('post.order_number')))->field('repair_person_id,id')->find();


       if(empty($order['id'])){
               $this->error('订单不存在');
       }else{
           if($order['repair_person_id'] !== I('post.repair_id')){
               $this->error('该订单不属于该师傅');
           }else{
               if(D('PunlishRepair')->create()){
                   $balance = D('wallet_repairer')->where(array('user_id'=>I('post.repair_id')))->field('balance')->find();
                   $save_balance =  $balance['balance'] - I('post.money');
                   $res = D('wallet_repairer')->where(array('user_id'=> I('post.repair_id')))->setField('balance',$save_balance);
                   $result  = D('PunlishRepair')->add();
                   if($result && $res){

                       $track['order_number'] = I('order_number');
                       $track['create_time']  = time();
                       $track['content']      = "驰达家维处罚师傅：处罚原因是".I('content')."；处罚金额为".I('money');
                       $track['person']       = '驰达家维客服';

                       $tra = D('order_track')->add($track);

                       $this->success('处罚成功');
                   }else{
                       $this->success('处罚失败');
                   }
               }else{
                   exit(D('punlish_repair')->getError());
               }
           }

       }

    }


    /**
     * 图片详情
     */
    public function detail_picture(){
        $id = I('id');
        $order_number = D('order_user')->where(array('id'=>$id))->field('order_number')->find();
        $result = D('order')->where(array('order_number'=>$order_number['order_number']))->field('id,order_number,end_picture_1,end_picture_2,wanjiema,buying_picture')->find();
        if( $result['end_picture_1'] || $result['end_picture_2'] || $result['wanjiema'] || $result['buying_picture']){
            $this->assign('list',$result);
            $this->display('detail_picture');
        }else{
            $this->error('没有图片存在');
        }

    }

    /**
     * 订单价格修改
     */
    public function edit_price_list(){
        $id = I('id');
        $order_number = D('order_user')->where(array('id'=>$id))->field('order_number')->find();
        $result = D('order')->where(array('order_number'=>$order_number['order_number']))->field('repair_price,order_number,service_price,far_price,parts_price,logistics_price')->find();
        $result['repair_price'] = empty($result['repair_price']) ? '0' : $result['repair_price'];
        $result['service_price'] = empty($result['service_price']) ? '0' : $result['service_price'];
        $result['far_price']     = empty($result['far_price'])     ? '0' : $result['far_price'];
        $result['parts_price']   = empty($result['parts_price'])   ? '0' : $result['parts_price'];
        $result['logistics_price'] = empty($result['logistics_price']) ? '0' : $result['logistics_price'];
        $this->assign('list',$result);
        $this->display('edit_price');
    }

    /**
     * 修改价格
     */
    public function edit_price(){
        $data = I('post.');
        $data['repair_price'] = $data['service_price']  +   $data['parts_price']  +  $data['far_price']  +   $data['logistics_price'];

        $is_exist = D('order')->where(array('order_number'=>$data['order_number']))->field('status')->find();

        if($is_exist['status'] == 7 ){
            $this->error('修改失败，目前订单已处于不可修改费用状态');
        }


        if($data['logistics_price'] != 0 ){
            $track['order_number'] = $data['order_number'];
            $track['create_time']  = time();
            $track['content']      = "联保客服添加物流费用为".$data['logistics_price'];
            $track['person']       = "联保客服";
            $tra = D('order_track')->add($track);
        }

        $price  =  D('order')->where(array('order_number'=>I('post.order_number')))->field('order_number,service_price,far_price,parts_price,logistics_price')->find();

        $price['new_repair_price']      =  $data['repair_price'];
        $price['new_service_price']     =  $data['service_price'];
        $price['new_far_price']         =  $data['far_price'];
        $price['new_parts_price']       =  $data['parts_price'];
        $price['new_logistics_price']   =  $data['logistics_price'];
        $price['create_time']           =  time();
        $res = D('judge_price')->add($price);
        $result = D('order')->where(array('order_number'=>I('post.order_number')))->save($data);
        if($result && $res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

    /**
     * 待接单
     */

    public function not_access_order(){
        $map['status'] = 1;
        $count = M('order_user')->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('order_user')->where($map)->order('id asc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $show = $page->show('Admin');
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['status'] = '待接单';
            $repair_price = D('order')->where(array('order_number'=>$list[$i]['order_number']))->field('repair_price')->find();
            $list[$i]['pro_price'] = $repair_price['repair_price'];
        }
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('not_access_order');
    }

    /**
     * 指派接单页
     */
    public function app_orer_list(){
        $id = I('order_number');
        $list = D('order_user')->where(array('order_number'=>$id))->field('user_city,order_number,user_address')->find();
        $this->assign('list',$list);
        $this->display('appoint_repairer');
    }
    /**
     * 指派师傅
     */
    public function appoint_repairer(){
        $order_number = I('order_number');

        $repair_id =  D('user')->where(array('username'=>I('repair_name'),'user_type'=>4))->field('id')->find();

        if($repair_id['id']) {
            $service_id =  D('user_repairer')->where(array('user_id'=>$repair_id['id']))->field('real_name,parent_id')->find();


            $save['repair_person_id']   =  $repair_id['id'];
            $save['repair_service_id'] =  empty($service_id['parent_id']) ? '' : $service_id['parent_id'] ;
            $save['status']          = 9;

            $res_a =  D('order')->where(array('order_number'=>$order_number))->save($save);

            $res_b =  D('order_user')->where(array('order_number'=>$order_number))->setField('status',9);

            $add_repair['order_number'] =  $order_number;
            $add_repair['taking_time']  =  time();
            $add_repair['assign_time']  =  time();
            $add_repair['assign_end_time'] = strtotime("+1 day");
            D('order_repair')->add($add_repair);

            $add['order_number'] = $order_number;
            $add['taking_time']  = time();
            $add['assign_time']  = time();


            $track['order_number'] = $order_number;
            $track['create_time']  = time();
            $track['content']      = "派单给".$service_id['real_name'];
            $track['person']       = '驰达家维客服';
            $tra = D('order_track')->add($track);
            if($res_a && $res_b  && $tra){
             
                $content = "【驰达家维】：您有新订单，请登录师傅端查看";
                $this->send_info_fiel(I('repair_name'),$content);
                $this->success('指派成功',U('Order/not_access_order'));
           
            }else{
                $this->error('指派失败');
            }
        }else{
            $this->error('指派失败，该师傅不存在');
        }
    }


    /**
     * 发送短信
     * @param  [type] $mobile [description]
     * @param  [type] $cont   [description]
     * @return [type]         [description]
     */
    private function send_info_fiel($mobile,$content){
        vendor ("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms  = new \Cxsms($options);
        $result = $Cxsms->send($mobile,$content);
        if($result && $result['returnsms']['returnstatus']=='Success'){

        }else{
            $this->error('发送短信失败');
        }
    }


    /**
     * 关闭订单页面
     */
    public function close_order_list(){
        $this->assign('order_number',I('order_number'));
        $this->display('close_order');
    }


    /**
     * 关闭工单
     */
    public function close_order(){
        $status = D('order')->where(array('order_number'=>I('order_number')))->field('status')->find();
        if( $status['status'] == 7 ){
            $this->error('该订单已完结，不允许强行关闭工单');
        }
        if( $status['status'] == 2 ){
            $this->error('该订单已关闭，不允许强行关闭工单');
        }
        $repair = D('order_repair')->where(array('order_number'=>I('order_number')))->field('id')->find();
        if(empty($repair['id'])){
          $repair['order_number']  =  I('order_number');
          D('order_repair')->add($repair);
        }
        $data['cancel_order_reason']  = I('reason');
        $data['cancel_order_time']    = time();
        $a = D('order_repair')->where(array('order_number'=>I('order_number')))->save($data);
        $b = D('order')->where(array('order_number'=>I('order_number')))->setField('status',2);
        $c = D('order_user')->where(array('order_number'=>I('order_number')))->setField('status',2);


        if($a && $b && $c){
            $track['order_number'] = I('order_number');
            $track['create_time']  = time();
            $track['content']      = "驰达家维客服取消订单";
            $track['person']       = '驰达家维客服';
            $tra = D('order_track')->add($track);

            $this->success('关闭订单成功',U('Order/index'));
        }else{
            $this->error('关闭订单失败');
        }

    }


    public function select_a($table, $start_time = "", $end_time = "", $key = "", $key_cond = "",$order_number="", $status = "")
    {


        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        if (empty($start_time) || empty($end_time)) {
            if (empty($key)) {
                if (empty($status)) {
                    $count = M($table)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where(array('order_number'=>$order_number))->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
                } else {
                    $map['status'] = $status;
                    $map['order_number']= $order_number;
                    $count = M($table)->where($map)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            } else {
                $keywords = $key;
                $keys[$key_cond] = array('like', $keywords);
                $keys['order_number'] = $order_number;
                if (empty($status)) {
                    $count = M($table)->where($keys)->order('id desc')->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($keys)->limit($page->firstRow . ',' . $page->listRows)->select();

                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->where($keys)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->where($keys)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            }
        } else {
            $time = "create_time>='{$start_time}' and create_time<='{$end_time}' and order_number='{$order_number}'";
            if (empty($key)) {
                if (empty($status)) {
                    $count = M($table)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($time)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();


                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->order('id desc')->where($time)->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            } else {
                $keywords = $key;
                $keys[$key_cond] = array('like', $keywords);
                if (empty($status)) {
                    $count = M($table)->where($keys)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($keys)->where($time)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                } else {
                    $map['status'] = $status;
                    $count = M($table)->where($map)->where($keys)->where($time)->count();
                    $page = $this->page($count, 20);
                    $list = M($table)->where($map)->where($keys)->where($time)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();

                }
            }
        }

        if (empty($start_time) || empty($end_time)) {
            $tim['start_time'] = '1430452800';
            $tim['end_time'] = time();
        } else {
            $tim['start_time'] = $start_time;
            $tim['end_time'] = $end_time;
        }
        $show = $page->show('Admin');


        $this->assign('time', $tim);
        $this->assign('list', $list);
        $this->assign('page', $show);
        return $list;
    }

    /**
     * 维修记录
     */
    public function repair_index(){
        $user = D('user')->where(array('id'=>I('user_id')))->field('user_type')->find();
        if($user['user_type'] == 3){
            $map['repair_service_id'] = I('user_id');
        }
        if($user['user_type'] == 4){
            $map['repair_person_id']  = I('user_id');
        }
        if($user['user_type'] == 2){
            $map['user_id'] = I('user_id');
        }

        $count = D('order')->where($map)->count();

        $page = $this->page($count, 20);
        $list = D('order')->where($map)->field('order_number,status,repair_price')->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        for($i=0;$i<count($list);$i++){
            if ($list[$i]['status'] == 1) $list[$i]['status'] = "待接单";
            if ($list[$i]['status'] == 2) $list[$i]['status'] = "已取消";
            if ($list[$i]['status'] == 7) $list[$i]['status'] = "已完结";
            if ($list[$i]['status'] == 8) $list[$i]['status'] = "待服务";
            if ($list[$i]['status'] == 9) $list[$i]['status'] = "待预约";
            if ($list[$i]['status'] == 10) $list[$i]['status'] = "待指派";
            if ($list[$i]['status'] == 11) $list[$i]['status'] = "待收件";
            if ($list[$i]['status'] == 12) $list[$i]['status'] = "待寄件";
            if ($list[$i]['status'] == 13) $list[$i]['status'] = "待收款";
            if ($list[$i]['status'] == 16) $list[$i]['status'] = "正在服务中";
            $order_user = D('order_user')->where(array('order_number'=>$list[$i]['order_number']))->field('user_name,user_address')->find();
            $list[$i]['user_name']    = $order_user['user_name'];
            $list[$i]['user_address'] = $order_user['user_address'];
        }
        $show = $page->show('Admin');
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('index');
    }

    public function send_info($mobile,$price){

        $content = "您好：因为距离过远，维修师傅申请了远程费用，费用为".$price."元，请您到订单页面查看详情，如非本人操作，请无需理会,【驰达家维】";
        vendor ("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms  = new \Cxsms($options);
        $result = $Cxsms->send($mobile,$content);
        if($result && $result['returnsms']['returnstatus']=='Success'){

        }else{
            $this->error('发送短信失败');
        }
    }


}