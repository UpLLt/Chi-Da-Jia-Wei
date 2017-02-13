<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class BaseController extends Controller{
	public function __construct(){
		parent::__construct();
		$this->is_login();
		$this->login_status();
		$this->is_detail();
		$this->username();
	}

	/**
	 * 检测是否登陆
	 */
	private function is_login(){
		$user_id    = session('user_id');
		$map['id'] = $user_id;
		$map['user_type'] = 2;
		$map['status'] = 1;
		$result = D('user')->where($map)->field('username')->find();
		if(!$result){
			$this->error('未登陆，请您登陆',U('Lianbao_PC/login/index'));
		}
	}

	/**
	 * 拼接url
	 * @param $param
	 * @return string
	 */
	public function geturl($param, $root = true)
	{
		$url       = '';
		$http_host = "http://" . $_SERVER['HTTP_HOST'];
		if ($root) $http_host .= __ROOT__;
		if (is_string($param))
			return $http_host . $param;
		if (is_array($param)) {
			foreach ($param as $k => $v) {
				if ($k != count($param - 1))
					$url .= $v . '/';
				else
					$url .= $v;
			}
			return $http_host . $url;
		}
	}
	private function is_detail(){
		$map['user_id'] =  session('user_id');
		$result = D('user_shop')->where($map)->field('id')->find();
		if(!$result){
			$this->error( '请您去填写详细的公司信息',U('Lianbao_PC/Detail/add_detail_list'));
		}else{
		  $status	= D('user')->where(array('id'=> session('user_id')))->field('examine_status')->find();
		  if($status['examine_status'] !=2 ){
			  $this->error('您的账号尚未通过审核，请耐心等待',U('Lianbao_PC/Index/index'));
		  }
		}


	}

	private function login_status(){
		if(session('user_id')){
			$map['user_id'] = session('user_id');
			$username = M('user')->where($map)->find();

			
			$login =   "<li><a href= ". U('Lianbao_PC/Register/index') ." target='_blank'>注册</a></li>".
					   "<li><a href= ". U('Lianbao_PC/login/loginout') ." target='_blank'>退出</a></li>".
			           "<li><p>".$username['username']." -欢迎您！</p></li>";   
		
		
		}else{
			$login =   "<li><a href=". U('Lianbao_PC/Register/index') ." target='_blank'>注册</a></li>".
					   "<li><a href=". U('Lianbao_PC/login/index')    ." target='_blank'>登录</a></li>".
			           "<li><p>驰达家维 -欢迎您！</p></li>";			   
			 }
		$this->assign('str',$login);
	}
	
	private function username(){
		$username = D('user')->where(array('id'=>session('user_id')))->field('username')->find();
		$this->assign('username',$username);
	}

	
}