<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class MustReadController extends BaseController {
	public function index(){
		$platform = D('policy')->where(array('type'=>8))->field('content')->find();
		$platform['content'] = htmlspecialchars_decode($platform['content']);
		$this->assign('list',$platform);
		$this->display();
	}
}