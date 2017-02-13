<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class InformController extends BaseController {
    public function index(){
		$biao       = 'inform_user';				
		$User       = M($biao);
		$map['type']= 2;
		$count      = $User->where($map)->count();
		$Page       = new \Think\Page($count,20);
		$show       = $Page->show();
		$list       = $User->order('create_time')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
		$time['start_time'] ='1430452800';
		$time['end_time']   = time();
		$this->assign('time',$time);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function search(){
		$start_time = strtotime(I('post.start_time'));
		$end_time   = strtotime(I('post.end_time'));
		$key        = I('post.filter');
		$biao       = 'inform_user';
			
		if(!empty($key)){
			$map['title'] =	$key;
		}
		
		$map['create_time'] = array('between',array($start_time,$end_time));
		$map['type']        = 2;
		$User       = M($biao);
		$list       = 	$User->order('create_time')
						->where($map)
						->select();
		$time['start_time'] = $start_time;
		$time['end_time']   = $end_time;
	
		$this->assign('time',$time);
		$this->assign('list',$list);
		$this->display('index');
	}
	public function detail(){
		$map['id'] = I('id');
		$list = D('inform_user')->where($map)->find();
		$list['content'] = html_entity_decode($list['content']);
		$this->assign('list',$list);
		$this->display('detail');
	}

    
}