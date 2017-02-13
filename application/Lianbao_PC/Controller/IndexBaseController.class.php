<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class IndexBaseController extends Controller{
	public function __construct(){
		parent::__construct();
		$this->login_status();
	}

	private function login_status(){
		
		$login =   "<li><a href=". U('Lianbao_PC/Register/index') ." target='_blank'>注册</a></li>".
				   "<li><a href=". U('Lianbao_PC/login/index')    ." target='_blank'>登录</a></li>".
				   "<li><p>驰达家维 -欢迎您！</p></li>";			   
		 
		$this->assign('str',$login);
	}
	
}