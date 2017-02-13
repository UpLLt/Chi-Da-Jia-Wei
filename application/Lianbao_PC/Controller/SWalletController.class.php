<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 15:39
 */
namespace Lianbao_PC\Controller;

use Think\Controller;

class SWalletController extends ServiceBaseController
{
    protected $user_card_model;
    protected $wallet_seller_model;
    protected $wallet_service_consum;
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    private function initialize()
    {
        $this->user_card_model = D('UserCard');
        $this->wallet_seller_model = D('wallet_seller');
        $this->id = session('user_id');
        $this->wallet_service_consum = D('wallet_service_consum');
    }

    public function index()
    {

        $map['user_id'] = $this->id;
        $wallet = $this->wallet_seller_model->where($map)->find();
        foreach($wallet as $k => $v ){
            if($wallet[$k] === null) $wallet[$k]= 0;
            $wallet[$k] = sprintf("%.2f", $wallet[$k]);
        }


        $now_month = now_month();
        $strat_time = strtotime($now_month['start_time']);
        $end_time = strtotime($now_month['end_time']);
        $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        $where['service_id'] = $this->id;

        $count = $this->wallet_service_consum->order('create_time desc')->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $this->wallet_service_consum->order('create_time desc')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        for($i=0; $i<count($list); $i++){
          if($list[$i]['type'] == 1 )  {
              $list[$i]['type'] = "工单收入";
              $list[$i]['service_shouru'] =  "+". $list[$i]['service_shouru'];
          }
          if($list[$i]['type'] == 2 ) {
              $list[$i]['type'] = "提现成功";
              $list[$i]['service_shouru'] =  "-". $list[$i]['service_shouru'];
          }
          if($list[$i]['type'] == 3 ) {
              $list[$i]['type'] = "提现失败";
              $list[$i]['service_shouru'] =  0;
          }
        }
        $wallet['wait_money'] = $this->wait_money();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('data', $wallet);
        $this->display('wallet');
    }

    /**
     * 提取现金
     */
    public function withdraw(){
        $map['user_id'] = $this->id;
        $result = $this->wallet_seller_model->order('create_time desc')->where($map)->field('balance')->find();
        $card = $this->user_card_model->order('create_time desc')->where($map)->field('id,card_name,card_bank,card_number')->select();
        $str  = '';
        for($i=0;$i<count($card);$i++){
            $card[$i]['card_number'] = substr($card[$i]['card_number'],-4);
            $str .= '<li><input type="radio" name="card_id" value="'.$card[$i]['id'].'" />'.$card[$i]['card_bank'].' <span>'.$card[$i]['card_name'].'</span><span>尾号：'. $card[$i]['card_number'].'</span></li>';
        }
        $this->assign('str',$str);
        $this->assign('bal',$result);
        $this->display('withdraw');
    }

    /**
     * 充值
     */
    public function recharge(){
        $map['user_id'] = $this->id;
        $result = $this->wallet_seller_model->order('create_time desc')->where($map)->field('balance')->find();
        $this->assign('bal',$result);
        $this->display('recharge');
    }
    /**
     *银行卡
     */
    public function card()
    {
        $map['user_id'] = $this->id;
        $result = $this->user_card_model->order('create_time desc')->where($map)->select();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['card_number'] = substr($result[$i]['card_number'], -5);
        }
        $this->assign('list', $result);
        $this->display('card');
    }

    /**
     * 添加银行卡
     */
    public function add_card()
    {
        $user_id = $this->id;
        $this->assign('user_id', $user_id);
        $this->display('add_card');
    }

    /**
     * 增加银行卡
     */
    public function add_card_card()
    {
        if (I('post.card_status') == 1) {
            $map['card_status'] = 1;
            $map['user_id'] = $this->id;
            $this->user_card_model->where($map)->setField('card_status', '0');
        }
        if ($this->user_card_model->create()) {
            $result = $this->user_card_model->add();
            if ($result) {
                $this->success('新增成功',U('SWallet/card'));
            } else {
                $this->error('新增失败');
            }
        } else {
            exit($this->user_card_model->getError());
        }

    }

    /**
     * 删除银行卡
     */
    public function del_card(){
        $map['id'] = I('get.id');
        $result = $this->user_card_model->where($map)->delete();
        if($result){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

    /**
     * 流水明细
     */
    public function income()
    {
        $this->last_month();
        $this->two_month();
        $this->three_month();
        $this->four_month();
        $this->five_month();
        $this->all_month();
        $this->month();
        $this->display('income');
    }

    /**
     * 本月
     */
    private function month()
    {
        $month = now_month();
        $data = $this->bill($month);
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $data['count'] . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="1">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $data['yeshu']; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" class="2" name="' . $i . '" set="1">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($data['list']);$j++){
            if($data['list'][$j]['type'] == "工单收入"){
                $shouru['service'] += $data['list'][$j]['service_shouru'];
            }
            if($data['list'][$j]['type'] == "提现成功"){
                $shouru['tixian'] += $data['list'][$j]['service_shouru'];
            }
        }
        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];

        $this->assign('shouru',$shouru);

        $st3 = '<a href="javascript:void(0)" onclick="a(this)" class="2" name="2" set="1">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $this->assign('now_list', $data['list']);
        $this->assign('now_page', $page);
    }

    /**
     * 上一个月
     */
    private function last_month()
    {
        $month['start_time'] = date('Y-m-01', strtotime('-1 month'));
        $month['end_time'] = date('Y-m-t', strtotime('-1 month'));
        $data = $this->bill($month);
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $data['count'] . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="2">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $data['yeshu']; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" name="' . $i . '" set="2">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($data['list']);$j++){
            if($data['list'][$j]['type'] == "工单收入"){
                $shouru['service'] += $data['list'][$j]['service_shouru'];
            }
            if($data['list'][$j]['type'] == "提现成功"){
                $shouru['tixian'] += $data['list'][$j]['service_shouru'];
            }
        }
        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];

        $this->assign('last_shouru',$shouru);
        $st3 = '<a href="javascript:void(0)" onclick="a(this)" name="2" set="2">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $this->assign('last_list', $data['list']);
        $this->assign('last_page', $page);

    }

    /**
     * 上两个月
     */
    private function two_month()
    {
        $month['start_time'] = date('Y-m-01', strtotime('-2 month'));
        $month['end_time'] = date('Y-m-t', strtotime('-2 month'));
        $data = $this->bill($month);
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $data['count'] . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="3">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $data['yeshu']; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" name="' . $i . '" set="3">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($data['list']);$j++){
            if($data['list'][$j]['type'] == "工单收入"){
                $shouru['service'] += $data['list'][$j]['service_shouru'];
            }
            if($data['list'][$j]['type'] == "提现成功"){
                $shouru['tixian'] += $data['list'][$j]['service_shouru'];
            }
        }
        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];
        $this->assign('two_shouru',$shouru);
        $st3 = '<a href="javascript:void(0)" onclick="a(this)" name="2" set="3">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $this->assign('two_list', $data['list']);
        $this->assign('two_page', $page);
    }

    /**
     * 上三个月
     */
    private function three_month()
    {
        $month['start_time'] = date('Y-m-01', strtotime('-3 month'));
        $month['end_time'] = date('Y-m-t', strtotime('-3 month'));
        $data = $this->bill($month);
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $data['count'] . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="4">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $data['yeshu']; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" name="' . $i . '" set="4">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($data['list']);$j++){
            if($data['list'][$j]['type'] == "工单收入"){
                $shouru['service'] += $data['list'][$j]['service_shouru'];
            }
            if($data['list'][$j]['type'] == "提现成功"){
                $shouru['tixian'] += $data['list'][$j]['service_shouru'];
            }
        }
        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];
        $this->assign('three_shouru',$shouru);
        $st3 = '<a href="javascript:void(0)" onclick="a(this)" name="2" set="4">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $this->assign('three_list', $data['list']);
        $this->assign('three_page', $page);
    }

    /**
     * 上四个月
     */
    private function four_month()
    {
        $month['start_time'] = date('Y-m-01', strtotime('-4 month'));
        $month['end_time'] = date('Y-m-t', strtotime('-4 month'));
        $data = $this->bill($month);
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $data['count'] . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="5">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $data['yeshu']; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" name="' . $i . '" set="5">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($data['list']);$j++){
            if($data['list'][$j]['type'] == "工单收入"){
                $shouru['service'] += $data['list'][$j]['service_shouru'];
            }
            if($data['list'][$j]['type'] == "提现成功"){
                $shouru['tixian'] += $data['list'][$j]['service_shouru'];
            }
        }
        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];
        $this->assign('four_shouru',$shouru);
        $st3 = '<a href="javascript:void(0)" onclick="a(this)" name="2" set="5">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $this->assign('four_list', $data['list']);
        $this->assign('four_page', $page);
    }

    /**
     * 上五个月
     */
    private function five_month()
    {
        $month['start_time'] = date('Y-m-01', strtotime('-5 month'));
        $month['end_time'] = date('Y-m-t', strtotime('-5 month'));
        $data = $this->bill($month);
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $data['count'] . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="6">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $data['yeshu']; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" name="' . $i . '" set="6">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($data['list']);$j++){
            if($data['list'][$j]['type'] == "工单收入"){
                $shouru['service'] += $data['list'][$j]['service_shouru'];
            }
            if($data['list'][$j]['type'] == "提现成功"){
                $shouru['tixian'] += $data['list'][$j]['service_shouru'];
            }
        }
        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];
        $this->assign('five_shouru',$shouru);
        $st3 = '<a href="javascript:void(0)" onclick="a(this)" name="2" set="6">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $this->assign('five_list', $data['list']);
        $this->assign('five_page', $page);
    }

    /**
     * 全部订单
     */
    private function all_month()
    {
        $where['service_id'] = $this->id;
        $count = $this->wallet_service_consum->order('create_time desc')->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $show = $Page->show();
        $yeshu = ceil($count / 20);
        $list = $this->wallet_service_consum->order('create_time desc')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $str1 = '<div class="list_page"><a href="javascript:void(0)">' . $count . '条</a><a href="javascript:void(0)" onclick="a(this)" name="1" set="7">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $yeshu; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)" name="' . $i . '" set="7">' . $i . '</a>';
        }
        $shouru ='';
        for($j=0;$j<count($list);$j++){
            if($list[$j]['type'] == 1 )  {
                $list[$j]['type'] = "工单收入";
                $list[$j]['service_shouru'] =  "+". $list[$j]['service_shouru'];
            }
            if($list[$j]['type'] == 2 ) {
                $list[$j]['type'] = "提现成功";
                $list[$j]['service_shouru'] =  "-". $list[$j]['service_shouru'];
            }
            if($list[$j]['type'] == 3 ) {
                $list[$j]['type'] = "提现失败";
                $list[$j]['service_shouru'] =  0;
            }

            if($list[$j]['type'] == "工单收入"){
                $shouru['service'] += $list[$j]['service_shouru'];
            }
            if($list[$j]['type'] == "提现成功"){
                $shouru['tixian'] += $list[$j]['service_shouru'];
            }
        }

        $shouru['service'] = empty($shouru['service']) ? 0 : $shouru['service'];
        $shouru['tixian'] = empty($shouru['service']) ? 0 : $shouru['tixian'];
        $this->assign('all_shouru',$shouru);
        $st3 = '<a href="javascript:void(0)" onclick="a(this)" name="2" set="7">下一页</a></div>';
        $page = $str1 . $str2 . $st3;

        $this->assign('all_list', $list);
        $this->assign('all_page', $page);
    }

    public function change_page()
    {
        $page = I('post.page');
        $type = I('post.type');

        $where = $this->panduan_type($type);
        $start_tiao = ($page - 1) * 20;
        $count = $this->wallet_service_consum->where($where)->count();
        $yeshu = ceil($count /20);
        $list = $this->wallet_service_consum
            ->where($where)
            ->order('create_time desc')
            ->limit("{$start_tiao},20")
            ->select();
        if(empty($list['0']['id'] )){
            $page = 1;
            $type = I('post.type');

            $where = $this->panduan_type($type);
            $start_tiao = ($page - 1) * 20;
            $count = $this->wallet_service_consum->where($where)->count();
            $yeshu = ceil($count /20);
            $list = $this->wallet_service_consum
                ->where($where)
                ->order('create_time desc')
                ->limit("{$start_tiao},20")
                ->select();

        }

        $pro = $page - 1;
        if ($pro == 0) $pro = 1;
        $next = $page + 1;
        if ($next >= $yeshu) $next = $page;
        $str1 = '<div style="margin-top: 60px" class="list_page"><a href="javascript:void(0)">' . $count . '条</a><a href="javascript:void(0)"

         onclick="a(this)" name="' . $pro . '" set="' . $type . '">上一页</a>';
        $str2 = '';
        for ($i = 1; $i <= $yeshu; $i++) {
            $str2 .= '<a href="javascript:;" onclick="a(this)"  name="' . $i . '" set="' . $type . '">' . $i . '</a>';
        }
        $st3 = '<a href="javascript:void(0)" onclick="a(this)"   name="' . $next . '" set="' . $type . '">下一页</a></div>';
        $page = $str1 . $str2 . $st3;
        $bianli2 = '';
        $shouru  = '';
        for ($i = 0; $i < count($list); $i++) {

            if($list[$i]['type'] == 1 )  {
                $list[$i]['type'] = "工单收入";
                $list[$i]['service_shouru'] =  "+". $list[$i]['service_shouru'];
            }
            if($list[$i]['type'] == 2 ) {
                $list[$i]['type'] = "提现成功";
                $list[$i]['service_shouru'] =  "-". $list[$i]['service_shouru'];
            }
            if($list[$i]['type'] == 3 ) {
                $list[$i]['type'] = "提现失败";
                $list[$i]['service_shouru'] =  0;
            }


                $bianli2 .= "<li><i>" . date('Y-m-d', $list[$i]['create_time']) . "</i><b>".$list[$i]['type']."</b><b>" . $list[$i]['service_shouru'] . "元</b><strong>" . $list[$i]['liushuihao'] . "</strong><span>".$list[$i]['type'].":" . $list[$i]['service_shouru']  . "元</span></li>";
            $shouru  += $list[$i]['service_shouru'];
        }

        $bianli =$bianli2 . $page;
        $this->ajaxReturn($bianli);

    }

    private function panduan_type($type)
    {
        if ($type == 1) {
            $month = now_month();
            $where['service_id'] = $this->id;
            $strat_time = strtotime($month['start_time']);
            $end_time = strtotime($month['end_time']);
            $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        }
        if ($type == 2) {
            $month['start_time'] = date('Y-m-01', strtotime('-1 month'));
            $month['end_time'] = date('Y-m-t', strtotime('-1 month'));
            $where['service_id'] = $this->id;
            $strat_time = strtotime($month['start_time']);
            $end_time = strtotime($month['end_time']);
            $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        }
        if ($type == 3) {
            $month['start_time'] = date('Y-m-01', strtotime('-2 month'));
            $month['end_time'] = date('Y-m-t', strtotime('-2 month'));
            $where['service_id'] = $this->id;
            $strat_time = strtotime($month['start_time']);
            $end_time = strtotime($month['end_time']);
            $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        }
        if ($type == 4) {
            $month['start_time'] = date('Y-m-01', strtotime('-3 month'));
            $month['end_time'] = date('Y-m-t', strtotime('-3 month'));
            $where['service_id'] = $this->id;
            $strat_time = strtotime($month['start_time']);
            $end_time = strtotime($month['end_time']);
            $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        }
        if ($type == 5) {
            $month['start_time'] = date('Y-m-01', strtotime('-4 month'));
            $month['end_time'] = date('Y-m-t', strtotime('-4 month'));
            $where['service_id'] = $this->id;
            $strat_time = strtotime($month['start_time']);
            $end_time = strtotime($month['end_time']);
            $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        }
        if ($type == 6) {
            $month['start_time'] = date('Y-m-01', strtotime('-5 month'));
            $month['end_time'] = date('Y-m-t', strtotime('-5 month'));
            $where['service_id'] = $this->id;
            $strat_time = strtotime($month['start_time']);
            $end_time = strtotime($month['end_time']);
            $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        }
        if ($type == 7) {
            $where['service_id'] = $this->id;
        }
        return $where;

    }

    private function bill($month)
    {
        $strat_time = strtotime($month['start_time']);
        $end_time = strtotime($month['end_time']);
        $where['create_time'] = array('between', "{$strat_time},{$end_time}");
        $where['service_id'] = $this->id;
        $count = $this->wallet_service_consum->where($where)->count();
        $yeshu = ceil($count / 20);
        $list = $this->wallet_service_consum->where($where)->order('create_time desc')->limit(20)->select();
        for($i=0; $i<count($list); $i++){
            if($list[$i]['type'] == 1 )  {
                $list[$i]['type'] = "工单收入";
                $list[$i]['service_shouru'] =  "+". $list[$i]['service_shouru'];
            }
            if($list[$i]['type'] == 2 ) {
                $list[$i]['type'] = "提现成功";
                $list[$i]['service_shouru'] =  "-". $list[$i]['service_shouru'];
            }
            if($list[$i]['type'] == 3 ) {
                $list[$i]['type'] = "提现失败";
                $list[$i]['service_shouru'] =  0;
            }
        }
        $data['yeshu'] = $yeshu;
        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }

    /**
     * 提现
     */
    public function tixian(){
        $user_id  = session('user_id');
        $money    = I('tixian_money');
        $card_id  = I('card_id');
        $taking_password = I('taking_password');
        if(empty($card_id)){
            $this->error('请添加银行卡');
        }
		
		
		
        $pass = D('user')->where(array('id'=>$user_id))->field('taking_password')->find();
        if(empty($pass['taking_password'])){
            $this->error('失败，提现密码未设置');
        }

        if(md5($taking_password) != $pass['taking_password']){
            $this->error('失败，提现密码不正确');
        }

        $tixian = $this->wallet_seller_model->where(array('user_id'=>$user_id))->field('balance,tixian_jinxinshi,all_tixian,tixian_card_id')->find();


        $tixian_ing = $tixian['tixian_jinxinshi'] + $money;
        if($tixian['balance'] < 0 || $tixian['balance']< $money ){
            $this->error('失败，余额不足');
        }
		
		if( !empty($tixian['tixian_jinxinshi']) && $tixian['tixian_card_id'] != $card_id ){
			
			$this->error('再次提现失败，请勿使用与正在提现中的不同银行卡');
		
		}

        $save['balance']         =  $tixian['balance'] - $money ;
        $save['tixian_status']   = 1;
        $save['tixian_jinxinshi']= $tixian_ing;
        $save['tixian_card_id']  = $card_id;
        $result = $this->wallet_seller_model->where(array('user_id'=>$user_id))->save($save);

        if($result){
            $this->success('申请成功,正在提现中');
        }else{
            $this->error('提现失败');
        }

    }

    public function wait_money(){
        $service_id = session('user_id');
        $map['repair_service_id'] = $service_id;
        $map['status'] = 13;
        $id = D('order')->where($map)->field('repair_person_id,service_price,parts_price,far_price,repair_price,logistics_price')->select();
        $proportion = D('wallet_admin')->field('proportion,no_ser_proportion')->find();

        //获取平台提成比例
        for($i=0;$i<count($id);$i++){

            $map_repairer['user_id'] = $id[$i]['repair_person_id'];
            $map_service['user_id'] = $id[$i]['repair_service_id'];
            $repairer = D('user_repairer')->where($map_repairer)->field('parent_id,type,proportion')->find();
            if (!empty($repairer['proportion'])) {

                $service_all = $id[$i]['service_price'] * (1 - $proportion['proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'];

                $service_ticheng = $service_all * $repairer['proportion'];
                $service_ticheng  = sprintf("%.2f", $service_ticheng);

            }
            if($repairer['type'] == 1){
                $service_ticheng = $id[$i]['service_price'] * (1 - $proportion['proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'];
                $service_ticheng  = sprintf("%.2f", $service_ticheng);
            }
            if ($repairer['type'] == 3 || $repairer['type'] == 4 || empty($repairer['parent_id'])) {

                $service_ticheng = 0;
            }


            $money  +=  $service_ticheng;
        }

            if(empty($money))  $money = 0;
            return $money;


    }

}