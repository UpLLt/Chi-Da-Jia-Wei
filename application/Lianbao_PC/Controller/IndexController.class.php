<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class IndexController extends IndexBaseController {
	
    public function index(){

		$this->banner();
		$this->novice_guild();
		$this->setshop();
		$this->tradeshow();
		$this->advantage();
		$this->order_count();
		$this->display('index');
    }
	public function	order_count(){
		$order_count   = M('order')->count();
		$user          = M('user')->count();
		$order_success = M('order')->where('status=0')->count();
		$map['status'] = array(array('eq',1),array('eq',8),array('eq',9),array('eq',10),array('eq',11),array('eq',12),array('eq',13),array('eq',16),'or');
		$order_ing     = M('order')->where($map)->count();
		$order_end     = M('order')->where(array('status'=>7))->count();

		$count['order_count'] 	= $order_count;
		$count['user'] 			= $user;
		$count['order_success'] = $order_success;
		$count['order_ing'] 	= $order_ing;
		$count['end']           = $order_end;
		$this->assign('count',$count);
		//dump($user);exit;
	}
	
	public function banner(){
		$banner   = M('links')->where("type = 1")->limit(1)->find();
		$banner_b = M('links')->where("type = 8")->limit(1)->find();
		$banner_c = M('links')->where("type = 9")->limit(1)->find();
		$fadan    = M('links')->where("type = 2")->limit(1)->find();
		$jiedan   = M('links')->where("type = 3")->limit(1)->find();
		$pingtai  = M('links')->where("type = 4")->limit(1)->find();
		$dizhi    = M('links')->where("type = 5")->limit(1)->find();
		$rexian   = M('links')->where("type = 6")->limit(1)->find();


		$this->assign('pingtai',$pingtai);
		$this->assign('dizhi',$dizhi);
		$this->assign('rexian',$rexian);
		$this->assign('fadan',$fadan);
		$this->assign('jiedan',$jiedan);
		$this->assign('banner',$banner);
		$this->assign('banner_b',$banner_b);
		$this->assign('banner_c',$banner_c);
	}
	public function novice_guild(){
		$map['status']  =1;
		$map['term_id'] =5;
		$novice_guild = M('posts')->order('post_date DESC')->where($map)->limit(6)->select();
		$this->assign('novice_guild',$novice_guild);
	}
	public function	setshop(){
		$map['status']  =1;
		$map['term_id'] =7;
		$setshop = M('posts')->order('post_date DESC')->where($map)->limit(6)->select();
		for($i=0;$i<count($setshop);$i++){
			$a = json_decode($setshop[$i]['smeta'],true);
			$setshop[$i]['smeta'] = $a['thumb'];
		}
		$this->assign('collaborate_shop',$setshop);	
	}
	public function	tradeshow(){
		$map['status']  =1;
		$map['term_id'] =9;
		$setshop = M('posts')->order('post_date DESC')->where($map)->limit(6)->select();
		for($i=0;$i<count($setshop);$i++){
			$a = json_decode($setshop[$i]['smeta'],true);
			$setshop[$i]['smeta'] = $a['thumb'];
		}
		$this->assign('trade_show',$setshop);	
	}
	public function	advantage(){
		$map['status']  =1;
		$map['term_id'] =3;
		$setshop = M('posts')->order('post_date DESC')->where($map)->limit(1)->find();
		$a = json_decode($setshop['smeta'],true);
		$setshop['smeta'] = $a['thumb'];
		$this->assign('advantage',$setshop);
	}
	
}