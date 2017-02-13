<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class ComplainController extends BaseController {
	public function index(){
		$this->display('index');
	}
	//投诉单的增加
	public function complain(){
		$id = D('order')->where(array('order_number'=>I('post.wnumber')))->field('id,repair_person_id,repair_service_id')->find();

		if(empty($id['id'])){
			$this->ajaxReturn(0);
		}
		$user_id                    = session('user_id');
		$data['user_id']            = session('user_id');
		$data['order_number']       = I('post.wnumber');
		$data['complaint_object']   = I('post.who');
		$data['create_time']     	= time();
		$data['content']            = I('post.reason');
		$data['repair_person_id']   = $id['repair_person_id'];
		$data['repair_service_id']  = $id['repair_service_id'];
		$data['type']               = 1;
		if($user_id){
			$result = M('complaint')->add($data);
			if($result){
				$this->ajaxReturn(1);
			}
		}else{
			$this->redirect('Login/index','',1, '未登陆，请您登陆');
			
		}
	}

}