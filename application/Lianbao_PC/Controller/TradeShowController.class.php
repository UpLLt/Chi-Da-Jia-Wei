<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class TradeShowController extends IndexBaseController {
	public function index(){
		$this->tradeshow();
		$this->display();	
	}
	public function tradeshow(){
		$map['status']  = 1;
		$map['term_id'] = 9;
		$count = M('posts')->where($map)->count();
		$Page  = new \Think\Page($count,4);
		$show  = $Page->show();
		$list  = M('posts')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
		for($i=0;$i<count($list);$i++){
			$a = json_decode($list[$i]['smeta'],true);
			$list[$i]['smeta'] = $a['thumb'];
		}
		$this->assign('page',$show);
		$this->assign('list',$list);
	}
	public function tradeshow_detail(){
		$id = I('get.id');
		$User = M('posts');
		$list = $User->where("id='{$id}'")->find();
		$this->assign('list',$list);
		$up['id']       = array('lt',$id);
		$up['status']   =1;
		$up['term_id']  =9;
		$down['id']     = array('gt',$id);
		$down['status'] =1;
		$down['term_id']=9;
		$map['status']  =1;
		$map['term_id'] =9;
		$front = M('posts')->where($up)->order('id desc')->limit('1')->find();
		$after = M('posts')->where($down)->order('id asc')->limit('1')->find();
		$a     = M('posts')->where($map)-> field('id')->order('id desc')->limit('1')->find();
		$b     = M('posts')->where($map)-> field('id')->order('id asc')->limit('1')->find();
		if(empty($front)){
			$front['post_title'] = "上一页,没有了";
			$front['id'] = $b['id'];
		}
		if(empty($after)){
			$after['post_title'] = "下一页,没有了";
			$after['id'] = $a['id'];
		}
		$this->assign('front',$front);
		$this->assign('after',$after);
		 $this->display('tradeshow_detail');	
		
	}

	

}