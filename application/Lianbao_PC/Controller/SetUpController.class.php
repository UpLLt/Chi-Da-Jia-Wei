<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class SetUpController extends BaseController {
	public function index(){
			$user_id = session('user_id');
			$result = M('user_shop')->where("user_id='{$user_id}'")->find();
			$this->assign('data',$result);
			$this->display('index');

	}
	//修改设置
	public function setup(){
		$user_id         = session('user_id');
				$data['name']     		 = I('post.name');
				$data['user_phone'] 	 = I('post.user_mobile');
				$data['company']  		 = I('post.user_company');
				$data['com_address']  	 = I('post.com_address');
				$data['detail_address']  = I('post.user_delaise_addr');
				$data['qq'] 			 = I('post.user_qq');
				$data['finance_phone']   = I('finance_phone');
				$data['service_phone']   = I('service_phone');
				$data['skill_phone']     = I('skill_phone');
			$select = M('user_shop')->where("user_id='{$user_id}'")->find();
			if($select){
				$result =  M('user_shop')->where("user_id='{$user_id}'")->save($data);
			}else{
				$data['user_id'] 	= $user_id;
				$result =  M('user_shop')->add($data);
			}
			if($result){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(2);
			}
	}

	public function upload_picture(){
		$user_id         = session('user_id');
		$upload = new \Think\Upload();
		$upload->maxSize = 3145728;
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		$upload->rootPath = 'data/upload/';
		$upload->savePath = '';
		$info   =   $upload->upload();

		if(I('type')==1){
			if($info['business_license']['savepath']){
				$data['business_license'] = '/data/upload/'.$info['business_license']['savepath'].$info['business_license']['savename'];
				$data['business_license'] = geturl($data['business_license']);
			}
		}
		$result =  M('user_shop')->where("user_id='{$user_id}'")->save($data);
		if($result){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}
	}

	public function id_card(){
		$user_card = D('user_card')->where(array('user_id'=>session('user_id')))->select();
		for($i=0;$i<count($user_card);$i++){
			$user_card[$i]['card_number'] = 	substr($user_card[$i]['card_number'],-4);
		}
		$this->assign('user_card',$user_card);
		$this->display('add_id_card');
	}

	public function del_id_card(){
		$id = I('id');
		$result = D('user_card')->where(array('id'=>$id))->delete();
		if($result){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

	public function add_id_card(){
		$user_id = session('user_id');
		$card['card_bank']        = I('post.bank');
		$card['card_name']        = I('post.bankcardname');
		$card['card_number']      = I('post.bankcardnum');
		$card['create_time']      = time();
		$card['user_id']          = $user_id;
		$card['card_status']      = 1;
		$result = M('user_card')->where("user_id='{$user_id}'")->add($card);
		if($result){
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
	}
	public function address(){
		$user_rec_address = D('user_rec_address')->where(array('user_id'=>session('user_id')))->select();
		$this->assign('list',$user_rec_address);
		$this->display('add_address');
	}

	public function del_rec_address(){
		$id = I('id');
		$result = D('user_rec_address')->where(array('id'=>$id))->delete();
		if($result){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}

	}

	public function add_address(){
		$user_id = session('user_id');

		$address['rec_city']    = I('post.city');
		$address['rec_address'] = I('post.addre');
		$address['rec_name']    = I('post.people');
		$address['rec_phone']   = I('post.phone');
		$address['rec_postal']  = I('post.poxcode');
		$address['create_time'] = time();
		$address['user_id']     = $user_id;
		$address['status']      = 1;

		$result = M('user_rec_address')->where("user_id='{$user_id}'")->add($address);
		if($result){
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(2);
		}

	}
	public function set_password(){
		$username = D('user')->where(array('id'=>session('user_id')))->field('username')->find();
		$this->assign('username',$username);
		$this->display('set_password');
	}
	public function xg_password(){
		$password     = I('post.password');
		$new_password = I('post.new_password');
		$user_id      = session('user_id');
		if($user_id){
			$username = M('user')->where("id='{$user_id}'")->find();
			//dump($password);exit;
			$map['username']  = $username['username'];
			$map['password']  = md5($password);
			$use = M('user')->where($map)->find();
			if($use){
				$add['password'] = md5($new_password);
				$result = M('user')->where("id='{$user_id}'")->save($add);
				if($result){
					$this->ajaxReturn(0);
				}else{
					$this->ajaxReturn(2);
				}
			}else{
				$this->ajaxReturn(1);
			}
		}else{
			$this->redirect('Login/index','',1,'未登录，请您登陆');
		}
	}
	
	
	public function xg_pay_pass(){
		$password     = I('post.password');
		$new_password = I('post.new_password');
		$user_id     = session('user_id');
		if($user_id){
			$username = M('user')->where("id='{$user_id}'")->find();
			$map['username'] = $username['username'];
			$map['pay_password'] = md5($password);
			$use = M('user')->where($map)->find();
			if($use){
				$add['pay_password'] = md5($new_password);
				$result = M('user')->where("id='{$user_id}'")->save($add);
				if($result){
					$this->ajaxReturn(0);
				}else{
					$this->ajaxReturn(2);
				}
			}else{
				$this->ajaxReturn(1);
			}
		}else{
			$this->redirect('Login/index','',1,'未登录，请您登陆');
		}
	}

	/**
	 * 找回密码
	 */
	public function lose_password(){
	    $map['username'] = I('username');
        $end_time = D('shot_message')->where($map)->field('end_time,code')->find();
        $shot_message = I('vcode');
        if($shot_message != $end_time['code']){
            $this->error('验证码不正确');
        }else{
           if(time() > $end_time['end_time']){
			    $this->error('验证码超时，请重新获取');
		   }else{
			   if(I('password') == I('re_password')){
				   if(strlen(I('password'))>=6){
					   $result	= D('user')->where($map)->setField('pay_password',md5(I('password')));
					   if($result){
						   $this->success('支付密码重置成功');
					   }else{
						    $this->error('失败，不能与原密码一致');  
					   }
				   }else{
					  $this->error('密码位数必须大于六位，请重新输入');   
				   }
			   }else{
				  $this->error('两次密码不一致，请重新输入'); 
			   }
		   }

        }
	}
	
	
	
	
	
}