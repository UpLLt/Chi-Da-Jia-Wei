<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class WxpayController extends Controller {


	/**
	 * notify_url接收页面
	 */
	public function notify_url(){

		Vendor('Weixinpay.Weixinpay');
		$wxpay = new \Weixinpay();
		$result = $wxpay->notify();
		if ($result) {
			//微信返回参数
			$order_sn    = substr($result['out_trade_no'],0,17); //订单号
			$order_price = $result['total_fee'] / 100; //微信返回的是分，换算成元
			$user_id     = substr($result['out_trade_no'],17);

			$wallet_service_consum = D('wallet_service_consum')->where(array('liushuihao'=>$order_sn))->field('id')->find();




			if( empty($wallet_service_consum['id']) ){

				$m = D('wallet_buyers');

				$wallet_buyers = $m->where(array('user_id'=>$user_id))->field('balance,recharge_money')->find();
				$save['balance']        = $wallet_buyers['balance'] + $order_price;
				$save['recharge_money'] =  $order_price + $wallet_buyers['recharge_money'];

				$m->where(array('user_id'=>$user_id))->save($save);

				$add['recharge_number'] = $order_sn;
				$add['user_id']         = $user_id;
				$add['create_time']     = time();
				$add['recharge_money']  = $order_price;
				$add['pay_money']       = $order_price;
				$add['pay_type']        = '1';
				$add['pay_time']        = time();
				$add['present_money']   = 0;
				$add['recharge_title']  = '微信充值';

				D('wallet_recharge')->add($add);


				$add_re['liushuihao']     = $order_sn;
				$add_re['order_price']    = $order_price;
				$add_re['create_time']    = time();
				$add_re['user_id']        = $user_id;
				$add_re['type']           = 5;

				D('wallet_service_consum')->add($add_re);
			}




		}
	}

}