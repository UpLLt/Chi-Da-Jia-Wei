<?php

/**
 * 会员
 */
namespace User\Controller;
use Common\Controller\AdminbaseController;
class IndexadminController extends AdminbaseController {
	/**
	 * 用户——个人
	 */
    function index_own(){
    	$users_model=M("User");
		$map['user_type'] = 1;
    	$count=$users_model->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $users_model
		->where($map)
		->join('as a left join lb_user_personal as b on a.id = b.user_id')
		->field('a.*,b.address')
    	->order("create_time DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
			if($lists[$i]['examine_status']==2) $lists[$i]['status'] = "正常";
			$lists[$i]['city'] = $lists[$i]['address'];

//			$lists[$i]['coupon']  =  "<a href='".U('Coupon/Coupon/add_list',array('uid'=>$lists[$i]['id']))."'>增加优惠劵</a>";
		}
		$this->assign('user_type',1);
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	$this->display(":index");
    }

	/**
	 * 用户——商家
	 */
	function index_shop(){
		$users_model=M("User");
		$map['user_type'] = 2;
		$count=$users_model->where($map)->count();
		$page = $this->page($count, 20);
		$lists = $users_model
			->where($map)
			->join('as a left join lb_user_shop as b on a.id = b.user_id')
			->field('a.*,b.com_address')
			->order("create_time DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
			if($lists[$i]['examine_status']==2) $lists[$i]['status'] = "正常";
			$lists[$i]['city'] = $lists[$i]['com_address'];
			$lists[$i]['recode']  =  "<a href='".U('Admin/Order/repair_index',array('id'=>$lists[$i]['id']))."'>维修记录</a>";
		}
		$this->assign('user_type',2);
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":index");
	}

	/**
	 * 用户——服务中心
	 */
	function index_service(){
		$users_model=M("User");
		$map['user_type'] = 3;
		$count=$users_model->where($map)->count();
		$page = $this->page($count, 20);
		$lists = $users_model
			->where($map)
			->join('as a left join lb_user_service as b on a.id = b.user_id')
			->field('a.*,b.shop_location')
			->order("create_time DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
			if($lists[$i]['examine_status']==2) $lists[$i]['status'] = "正常";
			$lists[$i]['city'] = $lists[$i]['shop_location'];

			$lists[$i]['str']  =  "<a href='".U('indexadmin/change_area',array('id'=>$lists[$i]['id']))."'>改变区域</a>";
			$lists[$i]['recode']  =  "<a href='".U('Admin/Order/repair_index',array('id'=>$lists[$i]['id']))."'>维修记录</a>";

		}


		$this->assign('user_type',3);
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":index");
	}

	/**
	 * 用户——维修师傅
	 */
	function index_repair(){
		$users_model=M("User");
		$map['user_type'] = 4;
		$count=$users_model->where($map)->count();
		$page = $this->page($count, 20);
		$lists = $users_model
			->join('as a left join lb_user_repairer as b on a.id = b.user_id')
			->field('a.*,b.city')
			->where($map)
			->order("create_time DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
			if($lists[$i]['examine_status']==2) $lists[$i]['status'] = "正常";
			$lists[$i]['recode']  =  "<a href='".U('Admin/Order/repair_index',array('id'=>$lists[$i]['id']))."'>维修记录</a>";
		}
		$this->assign('user_type',4);
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":index");
	}


	/**
	 * 数量统计
	 */
	public function number_count(){
		$data['own'] = D('user')->where(array('user_type'=>1))->count();
		$data['sho'] = D('user')->where(array('user_type'=>2))->count();
		$data['ser'] = D('user')->where(array('user_type'=>3))->count();
		$data['rep'] = D('user')->where(array('user_type'=>4))->count();
		$this->assign('list',$data);
		$this->display(':number_count');
	}


	/**
	 * 查询
	 */

	function search(){

		$key = I('keyword');

		if( I('user_type') == 4){
			$this->repair_search();
		}else{

			$_GET['keyword'] = $key;
			$_GET['user_type'] =I('user_type');
			if($key){
				$map['username'] = $key;
			}

			$users_model=M("User");
			$map['user_type'] =  I('user_type');

			$count=$users_model->where($map)->count();
			$page = $this->page($count, 20);
			$lists = $users_model
				->where($map)
				->join('as a left join lb_user_repairer as b on a.id = b.user_id')
				->field('a.*,b.city')
				->order("create_time DESC")
				->limit($page->firstRow . ',' . $page->listRows)
				->select();
			for($i = 0;$i<count($lists);$i++){
				if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
				if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
				if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
				if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
				if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
				if($lists[$i]['examine_status']==2) $lists[$i]['status'] = "正常";
			}
			$this->assign('user_type',I('user_type'));
			$this->assign('lists', $lists);
			$this->assign("page", $page->show('Admin'));
			$this->display(":index");
		}


	}

	/**
	 * 维修师傅查询
	 */

	function repair_search(){
		$users_model= M("User");
		$key = I('keyword');
		$_GET['keyword'] = $key;
		$_GET['user_type'] =I('user_type');

		if(is_numeric($key)){
			if($key){
				$map['username'] = $key;
			}

			$map['user_type'] =  I('user_type');

			$count=$users_model->where($map)->count();
			$page = $this->page($count, 20);
			$lists = $users_model
				->where($map)
				->join('as a left join lb_user_repairer as b on a.id = b.user_id')
				->field('a.*,b.city')
				->order("create_time DESC")
				->limit($page->firstRow . ',' . $page->listRows)
				->select();

		}else{

			$city['city'] = array('like',array('%'.$key.'%'));
			$count = D('user_repairer')->where($city)->count();
			$page  = $this->page($count,20);
			$repair = D('user_repairer')
						->where($city)
						->field('user_id,city')
						->limit( $page->firstRow. ',' .$page->listRows )
						->select();

			foreach( $repair as $value ){
				$map['id'] = $value['user_id'];
				$a =  $users_model->where($map)->find();
				$a['city'] = $value['city'];
				$lists[]   = $a;
			}


		}


		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
			if($lists[$i]['examine_status']==2) $lists[$i]['status'] = "正常";
		}
		$this->assign('user_type',I('user_type'));
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":index");

	}


	/**
	 * 审核师傅列表
	 */
	public function examine_repair(){
		$map['examine_status'] = 1;
		$map['user_type'] = 4;
		$users_model=M("User");
		$count=$users_model->where($map)->count();
		$page = $this->page($count, 20);
		$lists = $users_model
			->order("create_time DESC")
			->where($map)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
		}
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":examine_user");
	}

	/**
	 * 审核商家列表
	 */

	public function examine_shop(){
		$map['examine_status'] = 1;
		$map['user_type'] = 2;
		$users_model=M("User");
		$count=$users_model->where($map)->count();
		$page = $this->page($count, 20);
		$lists = $users_model
			->order("create_time DESC")
			->where($map)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		for($i = 0;$i<count($lists);$i++){
			if($lists[$i]['user_type']==1) $lists[$i]['user_type'] = "个人买家";
			if($lists[$i]['user_type']==2) $lists[$i]['user_type'] = "商家";
			if($lists[$i]['user_type']==3) $lists[$i]['user_type'] = "服务中心";
			if($lists[$i]['user_type']==4) $lists[$i]['user_type'] = "维修师傅";
			if($lists[$i]['examine_status']==1) $lists[$i]['status'] = "待审核";
		}
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":examine_user");
	}



	/**
	 * 审核商家 维修师傅
	 */
	public function  examine_user(){
		$id = I('id');
		$username = D('user')->where(array('id'=>$id))->field('username')->find();
		$result = D('user')->where(array('id'=>$id))->setField('examine_status',2);
		if($result) {
			$this->send_info($username['username']);
			$this->success('审核成功');

		}else{
			$this->error('审核失败');
		}

	}

	/**
	 * 审核失败列表
	 */

	public function shenehe_shibai(){
		$count = D('examine_fail')->order('create_time desc')->count();
		$page = $this->page($count, 20);
		$lists = D('examine_fail')
			->order("create_time DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		$this->assign('lists', $lists);
		$this->assign("page", $page->show('Admin'));
		$this->display(":shenhe_shibai");
	}

	/**
	 * 审核失败列表
	 */
	public function fail_exaime_list(){
		$id = I('id');
		$username = D('user')->where(array('id'=>$id))->field('username')->find();
		$this->assign('username',$username);
		$this->assign('id',$id);
		$this->display('fail_exaime');

	}

	/**
	 * 审核失败
	 */
	public function fail_exaime(){
		$id = I('id');
		if( strlen(I('content')) <= 0 ){
			$this->error('审核失败内容不能为空');
		}
		if( strlen(I('content')) >= 90 ){
			$this->error('审核失败内容不能大于30个字符');
		}
		$username = D('user')->where(array('id'=>$id))->field('username,user_type')->find();
		if( $username['user_type'] == 2 ) {
			D('wallet_buyers')->where(array('user_id'=>$id))->delete();
		}
		if( $username['user_type'] == 4 ) {

			D('user_repairer')->where(array('user_id'=>$id))->delete();

		}

		$result = D('user')->where(array('id'=>$id))->delete();
		if($result){
			$data['create_time'] = time();
			$data['username']    = $username['username'];
			$data['content']     = I('content');
			$res = D('examine_fail')->add($data);
			if($res){
				if( $username['user_type'] == 2 ){
					$this->success('成功',U('indexadmin/examine_shop'));
				}else{
					$this->success('成功',U('indexadmin/examine_repair'));
				}

				$this->send_info_fiel($data['username'],$data['content']);
			}else{
				$this->error('失败');
			}
		}else{
			$this->error('失败');
		}




	}


	private function send_info($mobile){

		$content = "您好：恭喜您，您已经通过了审核，如非本人操作，请无需理会【驰达家维】";
		vendor ("Cxsms.Cxsms");
		$options = array(
			'userid'  =>'1167',
			'account' =>'18781176753',
			'password'=>'5280201',
		);
		$Cxsms  = new \Cxsms($options);
		$result = $Cxsms->send($mobile,$content);
		if($result && $result['returnsms']['returnstatus']=='Success'){

		}else{
				$this->error('发送短信失败');
		}
	}

	/*
	 * 审核失败 发送短信
	 */
	private function send_info_fiel($mobile,$cont){

		$content = "您好：抱歉，您未能通过审核，原因是：".$cont."，您的账号已删除，请您核对信息后重新注册【驰达家维】";
		vendor ("Cxsms.Cxsms");
		$options = array(
			'userid'  =>'1167',
			'account' =>'18781176753',
			'password'=>'5280201',
		);
		$Cxsms  = new \Cxsms($options);
		$result = $Cxsms->send($mobile,$content);
		if($result && $result['returnsms']['returnstatus']=='Success'){

		}else{
			$this->error('发送短信失败');
		}
	}

	public function repair_detail(){

		$user_id = I('id');

		$user_type = D('user')->where(array('id'=>$user_id))->field('username,user_type')->find();
		$data['username'] = $user_type['username'];

		if( $user_type['user_type'] == 1){
			$data = D('user_personal')->where(array('user_id'=>$user_id))->find();
			$data['username'] = $user_type['username'];
			$this->assign('list',$data);
			$this->display('personal_detail');


		}else if($user_type['user_type'] == 2){
			$data = D('user_shop')->where(array('user_id'=>$user_id))->find();
			$data['username'] = $user_type['username'];
			$this->assign('list',$data);
			$this->display('shop_detail');

		}else if($user_type['user_type'] == 3){
			$data = D('user_service')->where(array('user_id'=>$user_id))->find();
			$data['username'] = $user_type['username'];
			$this->assign('list',$data);
			$this->display('service_detail');

		}else if($user_type['user_type'] == 4){
			$data = D('user_repairer')->where(array('user_id'=>$user_id))->find();
			$data['username'] = $user_type['username'];
			$user_service = D('user_service')->where(array('user_id'=>$data['parent_id']))->field('company')->find();
			$data['parent_com'] = $user_service['company'];
			if( $data['type'] ==1 )  $data['type'] = "直属成员";
			if( $data['type'] ==2 )  $data['type'] = "附属成员";
			if( $data['type'] ==3 )  $data['type'] = "申请成员中";
			if( $data['type'] ==4 )  $data['type'] = "申请成员中";
			$this->assign('list',$data);
			$this->display('repairer_detail');
		}

	}

    function ban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("User")->where(array("id"=>$id))->setField('status','2');
    		if ($rst) {
    			$this->success("会员拉黑成功！", U("indexadmin/index"));
    		} else {
    			$this->error('会员拉黑失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("User")->where(array("id"=>$id))->setField('status','1');
    		if ($rst) {
    			$this->success("会员启用成功！", U("indexadmin/index"));
    		} else {
    			$this->error('会员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

	/**
	 * 师傅提现列表
	 */
	public function repair_tixian_list(){
		$count=D('wallet_repairer')->where(array('tixian_status'=>1))->count();

		$page = $this->page($count, 20);
		$balance = D('wallet_repairer')
					->where(array('tixian_status'=>1))
					->field('user_id,balance,tixian_ing,tixian_card_id')
					->limit($page->firstRow . ',' . $page->listRows)
					->select();
		for($i=0;$i<count($balance);$i++){
			$username = D('user')->where(array('id'=>$balance[$i]['user_id']))->field('username')->find();
			$balance[$i]['username'] = $username['username'];
			$bank = D('user_card')->where(array('id'=>$balance[$i]['tixian_card_id']))->field('card_name,card_number,card_bank,phone')->find();
			$balance[$i]['card_name']   = $bank['card_name'];
			$balance[$i]['card_number'] = $bank['card_number'];
			$balance[$i]['card_bank']   = $bank['card_bank'];
			$balance[$i]['phone']       = $bank['phone'];
		}
		$this->assign('lists', $balance);
		$this->assign("page", $page->show('Admin'));
		$this->display("repair_tixian");
	}

	/**
	 * 师傅提现记录
	 */
	public function repair_tixian_record(){
		$map['repairer_id'] = array('exp',' is not NULL');
		$status['type'] = array(array('eq',2),array('eq',3), 'or');
		$count = D('wallet_service_consum')->where($map)->where($status)->count();
		$page = $this->page($count, 20);
		$list = D('wallet_service_consum')
			->where($status)
			->where($map)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		for($i=0;$i<count($list);$i++){
			$username = D('user')->where(array('id'=>$list[$i]['repairer_id']))->field('username')->find();
			$list[$i]['username'] = $username['username'];
			if( $list[$i]['type'] == 2 ) $list[$i]['type'] = "提现成功";
			if( $list[$i]['type'] == 3 ) $list[$i]['type'] = "提现失败";
		}
		$this->assign('list',$list);
		$this->assign("page", $page->show('Admin'));
		$this->display("repair_tixian_list");
	}

	/**
	 * 服务中心提现列表
	 */
	public function service_tixian_list(){
		$count=D('wallet_seller')->where(array('tixian_status'=>1))->count();

		$page = $this->page($count, 20);
		$balance = D('wallet_seller')
			->where(array('tixian_status'=>1))
			->field('user_id,balance,tixian_jinxinshi,tixian_card_id')
			->limit($page->firstRow . ',' . $page->listRows)
			->select();


		for($i=0;$i<count($balance);$i++){
			$username = D('user')->where(array('id'=>$balance[$i]['user_id']))->field('username')->find();
			$balance[$i]['username'] = $username['username'];
			$bank = D('user_card')->where(array('id'=>$balance[$i]['tixian_card_id']))->field('card_name,card_number,card_bank,phone')->find();
			$balance[$i]['card_name']   = $bank['card_name'];
			$balance[$i]['card_number'] = $bank['card_number'];
			$balance[$i]['card_bank']   = $bank['card_bank'];
			$balance[$i]['phone']       = $bank['phone'];
		}

		$this->assign('lists', $balance);
		$this->assign("page", $page->show('Admin'));
		$this->display("service_tixian");
	}


	/**
	 * 服务中心提现记录
	 */
	public function tixian_record(){
		$map['service_id'] = array('exp',' is not NULL');
		$status['type'] = array(array('eq',2),array('eq',3), 'or');
		$count = D('wallet_service_consum')->where($map)->where($status)->count();
		$page = $this->page($count, 20);
		$list = D('wallet_service_consum')
			->where($status)
			->where($map)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();


		for($i=0;$i<count($list);$i++){
			$username = D('user')->where(array('id'=>$list[$i]['service_id']))->field('username')->find();
			$list[$i]['username'] = $username['username'];
			if( $list[$i]['type'] == 2 ) $list[$i]['type'] = "提现成功";
			if( $list[$i]['type'] == 3 ) $list[$i]['type'] = "提现失败";
		}
		$this->assign('list',$list);
		$this->assign("page", $page->show('Admin'));
		$this->display("service_tixian_list");
	}


	/**
	 * 师傅提现成功
	 */
	public function repair_tixian(){
		$user_id = I('user_id');
		$tixian = D('wallet_repairer')->where('user_id='.$user_id)->field('tixian_ing,tixian_all')->find();
		$save['tixian_ing'] =  0 ;
		$save['tixian_status'] = 2;
		$save['tixian_all']    = $tixian['tixian_all'] + $tixian['tixian_ing'];
		$result = D('wallet_repairer')->where('user_id='.$user_id)->save($save);

		$add['liushuihao']     = $this->liushuihao();
		$add['card_id']        = I('card_id');
		$add['repairer_id']    = $user_id;
		$add['create_time']    = time();
		$add['repairer_price'] = $tixian['tixian_ing'];
		$add['type']           = 2;

		$res = D('wallet_service_consum')->add($add);

		if($res && $result){
			$this->success('提现成功');
		}else{
			$this->error('提现失败');
		}
	}


	/**
	 * 服务中心提现成功
	 */
	public function service_tixian(){
		$user_id = I('user_id');
		$tixian = D('wallet_seller')->where('user_id='.$user_id)->field('tixian_jinxinshi,all_tixian')->find();
		$save['tixian_jinxinshi'] =  0 ;
		$save['tixian_status'] = 2;
		$save['all_tixian']    = $tixian['all_tixian'] + $tixian['tixian_jinxinshi'];
		$result = D('wallet_seller')->where('user_id='.$user_id)->save($save);

		$add['liushuihao']    = $this->liushuihao();
		$add['service_id']    = $user_id;
		$add['create_time']   = time();
		$add['card_id']        = I('card_id');
		$add['service_shouru']= $tixian['tixian_jinxinshi'];
		$add['type']          = 2;

		$res = D('wallet_service_consum')->add($add);

		if($res && $result){
			$this->success('提现成功');
		}else{
			$this->error('提现失败');
		}
	}


	/**
	 * 师傅提现失败
	 */
	public function tixian_fail(){
		$user_id = I('user_id');
		$tixian = D('wallet_repairer')->where('user_id='.$user_id)->field('tixian_ing,balance')->find();
		$save['tixian_status'] = 3;
		$save['balance']       = $tixian['balance'] + $tixian['tixian_ing'];
		$save['tixian_ing']    = 0;
		$result = D('wallet_repairer')->where('user_id='.$user_id)->save($save);


		$add['liushuihao']     = $this->liushuihao();
		$add['repairer_id']    = $user_id;
		$add['create_time']    = time();
		$add['card_id']        = I('card_id');
		$add['repairer_price'] = $tixian['tixian_ing'];
		$add['type']           = 3;

		$res = D('wallet_service_consum')->add($add);

		if( $res && $result ){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}

	/**
	 * 服务中心提现失败
	 */
	public function service_tixian_fail(){
		$user_id = I('user_id');
		$tixian = D('wallet_seller')->where('user_id='.$user_id)->field('tixian_jinxinshi,all_tixian')->find();
		$save['tixian_status'] = 3;
		$save['balance']       = $tixian['balance'] + $tixian['tixian_jinxinshi'];
		$save['all_tixian']    = 0;
		$result = D('wallet_seller')->where('user_id='.$user_id)->save($save);


		$add['liushuihao']    = $this->liushuihao();
		$add['service_id']    = $user_id;
		$add['create_time']   = time();
		$add['card_id']        = I('card_id');
		$add['service_shouru']= $tixian['tixian_jinxinshi'];
		$add['type']          = 3;

		$res = D('wallet_service_consum')->add($add);

		if( $res && $result ){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	/**
	 * @return 工单完结码
	 */
	function liushuihao(){
		$a = date('YmdHis',time());
		$b = rand(100,999);
		$c = $a.$b;
		return $c;

	}
	/**
	 * 师傅维修记录
	 */
	public function repair_index(){
		$count = D('order')->where(array('repair_peson_id'=>I('user_id')))->count();
		$page = $this->page($count, 20);
		$list = D('order')->where(array('repair_peson_id'=>I('user_id')))->field('order_number,status,repair_price')->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		for($i=0;$i<count($list);$i++){
			if ($list[$i]['status'] == 1) $list[$i]['status'] = "待接单";
			if ($list[$i]['status'] == 2) $list[$i]['status'] = "已取消";
			if ($list[$i]['status'] == 7) $list[$i]['status'] = "已完结";
			if ($list[$i]['status'] == 8) $list[$i]['status'] = "待服务";
			if ($list[$i]['status'] == 9) $list[$i]['status'] = "待预约";
			if ($list[$i]['status'] == 10) $list[$i]['status'] = "待指派";
			if ($list[$i]['status'] == 11) $list[$i]['status'] = "待收件";
			if ($list[$i]['status'] == 12) $list[$i]['status'] = "待寄件";
			if ($list[$i]['status'] == 13) $list[$i]['status'] = "待收款";
			if ($list[$i]['status'] == 16) $list[$i]['status'] = "正在服务中";
			$order_user = D('order_user')->where(array('order_number'=>$list[$i]['order_number']))->field('user_name,user_address')->find();
			$list[$i]['user_name']    = $order_user['user_name'];
			$list[$i]['user_address'] = $order_user['user_address'];
		}
		$show = $page->show('Admin');
		$this->assign('list', $list);
		$this->assign('page', $show);

	}

	/*
	 * 改变服务中心区域页面
	 */
	public function change_area(){
		$username = D('user')->where(array("id"=>I('id')))->field('id,username')->find();
		$shop_location = D('user_service')->where(array("user_id"=>I('id')))->field('shop_location')->find();
		$username['shop_location'] = $shop_location['shop_location'];
		$this->assign('username',$username);
		$this->display("change_area");
	}

	/*
	 * 改变服务中心区域
	 */
	public function service_area(){

		$result = D('user_service')->where(array('user_id'=>I('id')))->setField('shop_location',I('area'));

		if($result){
			$this->success('成功',U('indexadmin/index_service'));
		}else{
			$this->error('失败');
		}
	}



}
