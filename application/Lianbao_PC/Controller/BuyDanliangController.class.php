<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class BuyDanliangController extends BaseController {
	public function index(){
		$this->mydanliang();
		$this->display('index');		
	}
	//单量的购买
	public function add(){
		$user_id = session('user_id'); 
		$num  = I('post.num');
		$type = I('post.type');
		$price= I('post.price');

		if($user_id){
			$result = M('wallet_buyers')->where("user_id='{$user_id}'")->find();
		if(empty($result)){
			$info['user_id'] = $user_id;
			M('wallet_buyers')->add($info);		
			$result = M('wallet_buyers')->where("user_id='{$user_id}'")->find();
		}
			if( $result['balance']>=$price){
				$data['balance']   = $result['balance']-$price;
				$data['use_money'] = $result['use_money']+$price;
				
				$wallet   = M('wallet_buyers')->where("user_id='{$user_id}'")->save($data);
				$rand_num = rand_num(8);
				$danliang['user_id']       = $user_id;
				$danliang['buy_number']    = $rand_num;
				$danliang['buy_count']     = $num;
				$danliang['create_time']   = time();
				$danliang['buy_price']     = $price;
				if($type =="安装单量"){
					$danliang['install']  = $num;
					$danliang['buy_type'] = 1;
				}
				if($type =='维修单量'){
					$danliang['repair']   = $num;
					$danliang['buy_type'] = 2;
				}
				if($type =='送修单量'){
					$danliang['send']     = $num;
					$danliang['buy_type'] = 3;
				}
				$danliang_sel = M('wallet_buydanliang')->where("user_id='{$user_id}'")->add($danliang);
				$this->total_danliang($type,$user_id,$num);
				if($danliang_sel && $wallet){


					$add_re['liushuihao']     = liushuihao();
					$add_re['order_price']    = $price;
					$add_re['create_time']    = time();
					$add_re['user_id']        = $user_id;
					$add_re['type']           = 4;
					D('wallet_service_consum')->add($add_re);



					$this->ajaxReturn(1);
				}else{
					$this->ajaxReturn(2);
				}
				
			}else{
				$this->ajaxReturn(0);
			}
		}else{			
			$this->redirect('Login/index','',1, '未登陆，请您登陆');	
		}
	}
		//总数的添加
		public function total_danliang($type,$user_id,$num){
			$dan = M('wallet_danliang')->where("user_id='{$user_id}'")->find();
			if(empty($dan)){
			$in['user_id'] = $user_id;
			M('wallet_danliang')->add($in);		
			$dan = M('wallet_danliang')->where("user_id='{$user_id}'")->find();	
				
			}
			//dump($dan);exit;
			if("维修单量"==$type){
				$daliang_total['repair']  = $dan['repair'] + $num; 	
			}
			if("安装单量"==$type){
				$daliang_total['install'] = $dan['install'] + $num; 
			}
			if("送修单量"==$type){
				$daliang_total['send']    = $dan['send'] + $num; 
			}
				$daliang_total['user_id'] = $user_id;			
				//dump($daliang_total);exit;
				$re = M('wallet_danliang')->where("user_id='{$user_id}'")->save($daliang_total);
								
		}
	private function mydanliang(){
		$map['user_id'] = session('user_id');
		$list = D('wallet_danliang')->where($map)->find();
		$this->assign('mydanliang',$list);


	}
}