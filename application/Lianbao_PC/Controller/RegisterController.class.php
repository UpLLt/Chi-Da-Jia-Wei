<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class RegisterController extends IndexBaseController{
	public function index(){

		$this->display('register');

	}
	public function agreement(){

		$this->display('agreement');
	}


	/**
	 * 注册账号
	 */
	public function register(){


		$username     = I('mobile');
		$password	  = md5(I('password'));
		$pay_password = md5(I('pay_password'));
		$vcode        = I('vcode');
		$vode = check_verify($vcode);
		if(!$vode){
			$this->ajaxReturn(2);
		}
		$map['username'] = $username;
		$end_time = D('shot_message')->where($map)->field('end_time,code')->find();
		$shot_message = I('shot_message');
		if($shot_message != $end_time['code']){
			$this->ajaxReturn(3);
		}
		if(time()>$end_time['end_time']){
			$this->ajaxReturn(4);
		}
		$data['username'] = $username;
		$data['password'] = $password;
		$data['pay_password'] = $pay_password;
		$data['create_time'] = time();
		$data['examine_status'] = 1;
		$data['user_type']      = 2;
		$data['status']         = 1;
		$data['examine_status'] = 1;
		D('user')->create($data);
		$result = D('user')->add($data);

		if($result){

			$add['user_id'] = $result;
			$add['create_time'] = time();
			$add['balance'] = 0;
			$add['frozen_balance'] = 0;
			$add['bond'] = 0;
			$add['present_money'] = 0;
			$add['recharge_money'] = 0;
			$add['frozen_money'] = 0;
			$add['use_money'] = 0;
			M('wallet_buyers')->add($add);


			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(5);
		}
	}

	public function verfiy_username(){
		$username['username'] = I('mobile');
		$id = D('user')->where($username)->field('id')->find();
		if($id['id']){
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(2);
		}
	}
	/**
	 * 发送短信
	 */
	public function send_info(){
		$mobile      = I('post.mobile');

		$rand_number = rand_six();
		if(!preg_match('/^1[34578]\d{9}$/',$mobile)){
			exit($this->ajaxReturn(2));
			//手机号码错误
		}
		$map['username'] = $mobile;
		$username = D('user')->where($map)->field('username')->find();
		if($username['username']){
			exit($this->ajaxReturn(3));
			//账户已存在
		}

		$next = D('shot_message')->where($map)->field('next_send,create_time')->find();
		if($next['create_time']<time() && time()<$next['next_send']){
			exit($this->ajaxReturn(4));
			//短时间重复发送
		}
		$content = "您好，您的注册验证码是".$rand_number.".如非本人操作，请无需理会【驰达家维】";
		vendor ("Cxsms.Cxsms");
		$options = array(
			'userid'  =>'1167',
			'account' =>'18781176753',
			'password'=>'5280201',
		);
		$Cxsms  = new \Cxsms($options);
		$result = $Cxsms->send($mobile,$content);
		if($result && $result['returnsms']['returnstatus']=='Success'){
			$map_te['username'] = $mobile;
			$shot_message = D('shot_message')->where($map)->field('username')->find();

			$save_shot['username']        = $mobile;
			$save_shot['create_time']     =  time();
			$save_shot['code']            =  $rand_number;
			$save_shot['end_time']        =  strtotime("+15 minute");
			$save_shot['next_send']        =  strtotime("+1 minute");
			if($shot_message['username']){
				$result = D('shot_message')->where($map_te)->save($save_shot);
			}else{
				$result = D('shot_message')->add($save_shot);
			}
			if($result){
				exit($this->ajaxReturn(1));
			}else{
				exit($this->ajaxReturn(6));
			}
		}else{
			exit($this->ajaxReturn(5));
		}
	}



	

}