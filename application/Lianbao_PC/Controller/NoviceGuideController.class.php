<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class NoviceGuideController extends IndexBaseController {
	public function index(){
		$map['status']  = 1;
		$map['term_id'] = 5;
		$count = M('posts')->where($map)->count();
		$Page  = new \Think\Page($count,1);
		$show  = $Page->show();
		$list  = M('posts')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	public function detail(){
		$map['id'] = I('get.id');
		$list = M('posts')->where($map)->find();
		$this->assign('list',$list);
		$this->display('detail');

	}
}