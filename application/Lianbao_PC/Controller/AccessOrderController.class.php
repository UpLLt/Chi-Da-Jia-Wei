<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class AccessOrderController extends IndexBaseController {
	public function index(){
		$map['status']  = 1;
		$map['term_id'] = 4;
		$list = M('posts')->where($map)->order('post_date DESC')->limit(1)->find();
		$this->assign('list',$list);
		$this->display('index');
	}
}