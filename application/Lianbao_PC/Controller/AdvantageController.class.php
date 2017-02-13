<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class AdvantageController extends IndexBaseController {
	public function index(){
		$map['status']  = 1;
		$map['term_id'] = 3;
		$list = M('posts')->where($map)->order('post_date DESC')->limit(1)->select();
		$this->assign('list',$list);
		$this->display('index');
	}



}