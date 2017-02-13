<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class RechargMoneyController extends Controller {
	public function index(){
		$user_id = session('user_id');
		$out_trade_no = liushuihao().$user_id;

		$this->assign('out_trade_no',$out_trade_no);
		$this->display();
	}
	public function __construct()
	{
		parent::__construct();
	}


	public function pay(){

		$user_id  = session('user_id');
		$money    = I('money');
		$out_trade_no = I('out_trade_no');
		$username = D('user')->where(array('id'=>$user_id,'user_type'=>2))->field('id')->find();

			$money    = $money * 100;
			$order=array(
				'body'=>'驰达家维商家微信充值',
				'total_fee'=>$money,
				'out_trade_no'=>$out_trade_no,
				'product_id'=>$user_id,
			);


		weixinpay($order);


	}

	public function madify_pay(){
		$order_sn = I('post.id');

		$order_sn = substr($order_sn,0,17);
		$wallet_service_consum = D('wallet_service_consum')->where(array('liushuihao'=>$order_sn))->field('id')->find();

		if($wallet_service_consum){
			$this->ajaxReturn(1);
		}

	}


}