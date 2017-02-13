<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class UserindexController extends BaseController{
	public function userindex(){
	
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$status     = array(array('eq',1),array('eq',2),array('eq',7),array('eq',8),array('eq',9),array('eq',10),array('eq',11),array('eq',12),array('eq',13),array('eq',16), 'or');
		$biao       = 'order_user';
		$key_cond   = 'order_number|user_phone|user_name';
		$data = $this->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['status']==1)$data[$i]['status']="待接单";
			if($data[$i]['status']==2)$data[$i]['status']="已取消";
			if($data[$i]['status']==7)$data[$i]['status']="已完结";
			if($data[$i]['status']==8)$data[$i]['status']="待服务";
			if($data[$i]['status']==9)$data[$i]['status']="待预约";
			if($data[$i]['status']==10)$data[$i]['status']="待指派";
			if($data[$i]['status']==11)$data[$i]['status']="待收件";
			if($data[$i]['status']==12)$data[$i]['status']="待寄件";
			if($data[$i]['status']==13)$data[$i]['status']="待支付";
			if($data[$i]['status']==16)$data[$i]['status']="正在服务中";
		}
		$this->assign('list',$data);
		$this->display('userindex');
	}
	public function wait(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$status     = '1';
		$biao       = 'order_user';
		$key_cond   = 'order_number|user_phone|user_name';
		$data = $this->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['status']==1)$data[$i]['status']="待接单";
		}
		$this->assign('list',$data);
		$this->display('wait');
	}
	public function cancel(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$status     = '2';
		$biao       = 'order_user';
		$key_cond   = 'order_number|user_phone|user_name';
		$data = $this->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['status']==2)$data[$i]['status']="已取消";
		}
		$this->assign('list',$data);
		$this->display('cancel');
	}
	public function end(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$status     = '7';
		$biao       = 'order_user';
		$key_cond   = 'order_number|user_phone|user_name';
		$data = $this->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['status']==7)$data[$i]['status']="已完结";
		}
		$this->assign('list',$data);
		$this->display('end');
	}
	public function distance(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$map['fadan_id'] =  $user_id;
		$order_number = D('order_far_order')->field('order_number')->where($map)->select();
		for($i=0;$i<count($order_number);$i++){
			$where['order_number'] = $order_number[$i]['order_number'];
			$order_user = D('order_user')->where($where)->find();
			$order_pro = D('order_pro')->where($where)->find();
			$order = D('order')->where($where)->field('repair_price')->find();
			$data[$i]['order_number'] = $order_number[$i]['order_number'];
			$data[$i]['user_name']    = $order_user['user_name'];
			$data[$i]['user_city']    = $order_user['user_city'];
			$data[$i]['pro_price']    = $order_user['pro_price'];
			$data[$i]['repair_price'] = $order['repair_price'];
			$data[$i]['status']       = "远程费单";
			$data[$i]['far_status']   = "1";
		}

		$this->assign('list',$data);
		$this->display('distance');
	}
	public function parts(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$status     =  array(array('eq',12),array('eq',11),'or');
		$biao       = 'order_user';
		$key_cond   = 'order_number|user_phone|user_name';
		$data = $this->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['status']==12)$data[$i]['status']="配件单";
			if($data[$i]['status']==11)$data[$i]['status']="配件单";
			if($data[$i]['status']==20)$data[$i]['status']="返件单";
		}
		$this->assign('list',$data);
		$this->display('parts');
	}
	public function wait_pay(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.key');
		$user_id   = session('user_id');
		$status     =  array(array('eq',13),array('eq',20),'or');
		$biao       = 'order_user';
		$key_cond   = 'order_number|user_phone|user_name';
		$data = $this->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['status']==13)$data[$i]['status']="待付款";
			if($data[$i]['status']==20)$data[$i]['status']="返件单";
		}
		$this->assign('list',$data);
		$this->display('wait_pay');
	}

	public function order_cancel(){
		$id = I('get.id');
		$map['order_number'] = $id;
		$data['status']  = 2;
		$del   = M('order')->where($map)->save($data);
		$del_or   = M('order_user')->where($map)->save($data);
		if($del_or && $del){
			$this->success('取消订单成功','','/Userindex/userindex',1);
		}else{
			$this->success('取消订单失败','','/Userindex/userindex',1);
		}
	}
	public function select($biao,$user_id,$start_time="",$end_time="",$key="",$key_cond="",$status=""){
		$start_time = strtotime($start_time);
		$end_time   = strtotime($end_time);
		$map['user_id'] = $user_id;
		if(empty($start_time) || empty($end_time)){
			if(empty($key)){
				if(empty($status)){
					$count = M($biao)->where($map)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

				}else{
					$map['status'] = $status;
					$count = M($biao)->where($map)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

				}
			}else{
				$keywords = $key;
				$keys[$key_cond]= array('like',$keywords);
				if(empty($status)){
					$count = M($biao)->where($map)->where($keys)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->where($keys)->limit($Page->firstRow.','.$Page->listRows)->select();
				}else{
					$map['status'] = $status;
					$count = M($biao)->where($map)->where($keys)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->where($keys)->limit($Page->firstRow.','.$Page->listRows)->select();
				}
			}
		}else{
			$time = "create_time>='{$start_time}' and create_time<='{$end_time}'";
			if(empty($key)){
				if(empty($status)){
					$count = M($biao)->where($map)->where($time)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->where($time)->limit($Page->firstRow.','.$Page->listRows)->select();

				}else{
					$map['status'] = $status;
					$count = M($biao)->where($map)->where($time)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->where($time)->limit($Page->firstRow.','.$Page->listRows)->select();

				}
			}else{
				$keywords = $key;
				$keys[$key_cond]= array('like',$keywords);
				if(empty($status)){
					$count = M($biao)->where($map)->where($keys)->where($time)->count();
					$Page =  new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->where($keys)->where($time)->limit($Page->firstRow.','.$Page->listRows)->select();

				}else{
					$map['status'] = $status;
					$count = M($biao)->where($map)->where($keys)->where($time)->count();
					$Page = new \Think\Page($count,20);
					$list = M($biao)->where($map)->order('create_time desc')->where($keys)->where($time)->limit($Page->firstRow.','.$Page->listRows)->select();
				}
			}

		}
		if(empty($start_time)||empty($end_time)){
			$tim['start_time'] = strtotime("-1 month");
			$tim['end_time']   = time();
		}else{
			$tim['start_time'] = $start_time;
			$tim['end_time']   = $end_time;
		}
		$show = $Page->show();
		for($i=0;$i<count($list);$i++){
			$map_e['order_number']  =  $list[$i]['order_number'];
			$repair_price = D('order')->where($map_e)->field('repair_price')->find();
			$list[$i]['repair_price'] = empty($repair_price['repair_price']) ? 0 : $repair_price['repair_price'];
		}

		$this->assign('time',$tim);
		$this->assign('list',$list);
		$this->assign('page',$show);
		return $list;

	}

	/**
	 * 详情页面
	 */
	public function detail(){
		$far_status = I('far_status');
		$user_id = session('user_id');
		$where['user_id'] = $user_id;
		$user_repair = D('user_repairer')->where($where)->find();
		$map['order_number'] = I('order_number');
		$order_far_order = D('order_far_order')->where(array('order_number'=>I('order_number')))->find();

		$order = D('order')->where($map)->find();
		$status = $order['status'];
		$where['user_id'] = $order['repair_person_id'];
		$user_repair = D('user_repairer')->where($where)->find();
		$order_user = D('order_user')->where($map)->find();
		$order_pro = D('order_pro')->where($map)->find();
		$order_repair = D('order_repair')->where(array('order_number'=>I('order_number')))->find();

		$parts     = D('pro_parts')->where($map)->select();
		for($i=0;$i<count($parts);$i++){
			if($parts[$i]['type'] == 1 && $order_pro['order_type'] == 1) {
				$parts[$i]['parts_price'] = 0;
			}

			if($parts[$i]['type'] == 1 ) $parts[$i]['type'] = "厂商寄件";

			if($parts[$i]['type'] == 2 )  $parts[$i]['type'] = "自购件";

		}

		for($i=0;$i<count($parts);$i++){
			$parts[$i]['address'] = $user_repair['city'].'/'.$user_repair['address'];
			$parts[$i]['real_name'] = $user_repair['real_name'];
			$parts[$i]['phone'] = $user_repair['phone'];
		}
		$add_service	=  D('order_add_service')->where($map)->field('add_service')->find();

		if($order['status']==1){
			$order['status'] = '待接单';
		}
		if($order['status']==2){
			$order['status'] = '已取消';
		}
		if($order['status']==7){
			$order['status'] = '已完结';
		}
		if($order['status']==8){
			$order['status'] = '待服务';
		}
		if($order['status']==9){
			$order['status'] = '待预约';
		}
		if($order['status']==10){
			$order['status'] = '待指派';
		}
		if($order['status']==11){
			$order['status'] = '已寄件';
		}
		if($order['status']==12){
			$order['status'] = '待寄件';
		}
		if($order['status']==13){
			$order['status'] = '待付款';
		}
		if($order['status']==16){
			$order['status'] = '正在服务中';
		}
		if($order['status']==20){
			$order['status'] = '返件单';
		}
		if($order_pro['baoxiu_type']==1){
			$order_pro['baoxiu_type']= '保内(商家代付)';
		}
		if($order_pro['baoxiu_type']==2){
			$order_pro['baoxiu_type']= '保外(消费者自付)';
		}
		if($order_pro['order_type']==1){
			$order_pro['order_type']= '上门安装';
		}
		if($order_pro['order_type']==2){
			$order_pro['order_type']= '上门维修';
		}
		if($order_pro['order_type']==3){
			$order_pro['order_type']= '客户送修';
		}
		$this->assign('order_far_order',$order_far_order);
		$this->assign('far_status',$far_status);
		$this->assign('add_service',$add_service);
		$this->assign('status',$status);
		$this->assign('parts',$parts);
		$this->assign('order_repair',$order_repair);
		$this->assign('order',$order);
		$this->assign('order_user',$order_user);
		$this->assign('order_pro',$order_pro);
		$this->display('detail');
	}

	//厂家发货
	public function change_fahuo($order_number){
		$map['order_number'] = $order_number;
		$time = time();
		$save['send_time'] = $time;
		$save['status']    = 1;
		$where['order_number'] = I('order_number');
		$pro_parts = D('pro_parts')->where($where)->save($save);
		$order = D('order')->where($map)->setField('status',11);
		$order_user = D('order_user')->where($map)->setField('status',11);
		return $pro_parts;
	}

	/**
	 * 厂寄件
	 */
	public function logistics_list(){
		$this->assign('order_number',I('order_number'));
		$this->display('logistics');
	}

	/**
	 * 自购件
	 */
	public function logistics_own_list(){
		$this->assign('order_number',I('order_number'));

		$this->display('logistics_own');
	}



	/**
	 * 自购件
	 */
	public function logistics_own(){
		$order_number = I('order_number');
		$user_id      = session('user_id');
		$user_shop = D('user_shop')->where(array('user_id'=>$user_id))->field('company')->find();
		$old_parts    = I('old_parts');



		if( $old_parts == 1){

			if( empty(I('receive_person')) ||  empty(I('receive_phone')) ||  empty(I('receive_address')) || empty(I('pay_way')) ){
				$this->error('请补全快递返件信息');
			}
			$data['content'] = "工单号".$order_number."客户同意了你的需求,但是要求旧件回寄,收件人为".I('receive_person').",联系电话".I('receive_phone').",收货地址是".I('receive_address').",支付方式".I('pay_way');
			$data['create_time']    =  time();
			$data['person']         =  $user_shop['company'];
			$data['order_number'] = $order_number;
			$result = D('order_track')->add($data);

			if($result){
				D('order')->where(array('order_number'=>$order_number))->setField('re_parts_status',1);
			}
		}
		$this->change_fahuo($order_number);

		$this->success('新增成功', 'Userindex/send_parts_list');
	}


	/**
	 * 厂寄件
	 */
	public function logistics(){


		$order_number = I('order_number');
		$user_id      = session('user_id');
		$user_shop = D('user_shop')->where(array('user_id'=>$user_id))->field('company')->find();
		$old_parts    = I('old_parts');

		if( empty(I('express_order')) ||  empty(I('express_company')) ||  empty(I('express_pay')) || empty(I('express_time')) ){
			$this->error('请补全发送快递信息');
		}

		if($old_parts == 2 ){

			$data['content'] = "工单号".$order_number."客户同意了你的需求,快递单号为".I('express_order')."快递公司为".I('express_company').",付款方式是".I('express_pay').",发货时间是".I('express_time');
			$data['create_time']    =time();
			$data['person']  =$user_shop['company'];
			$data['order_number'] = $order_number;
			$result = D('order_track')->add($data);

		}else{

			if( empty(I('receive_person')) ||  empty(I('receive_phone')) ||  empty(I('receive_address')) || empty(I('pay_way')) ){
				$this->error('请补全快递返件信息');
			}

			$data1['content'] = "工单号".$order_number."客户同意了你的需求,但是要求旧件回寄,收件人为".I('receive_person').",联系电话".I('receive_phone').",收货地址是".I('receive_address').",支付方式".I('pay_way');
			$data1['create_time']    =  time();
			$data1['person']         =  $user_shop['company'];
			$data1['order_number'] = $order_number;
			D('order_track')->add($data1);

			$data['content'] = "工单号".$order_number."客户同意了你的需求,快递单号为".I('express_order')."快递公司为".I('express_company').",付款方式是".I('express_pay').",发货时间是".I('express_time');
			$data['create_time']    =time();
			$data['person']  =$user_shop['company'];
			$data['order_number'] = $order_number;
			$result = D('order_track')->add($data);


			if($result){
				D('order')->where(array('order_number'=>$order_number))->setField('re_parts_status',1);
			}

		}

		$this->change_fahuo($order_number);

		if($result){
			$this->success('新增成功', 'Userindex/send_parts_list');
		}else{
			$this->error('发件失败');
		}

	}




	//取消订单
	public function cancel_order(){
		$map['order_number'] = I('order_number');

		$reason = I('post.reason');
		if(empty($reason)){
			$this->error('请选择取消原因');
		}


		if($reason==1) $reason="工单信息资料有误（地址/电话号码/工单类型/保修类型/数量/）厂商修改后从新下单";
		if($reason==2) $reason="用户已自行处理/用户暂不处理/无法联系上用户（3天内）/用户无服务需求";
		if($reason==3) $reason="重复下单，厂商确认关闭";
		if($reason==4) $reason="厂商自主原因要求关闭";
		if($reason==5) $reason="受理前无网点关闭";
		if($reason==6) $reason="受理后无网点关闭";
		$is_exi = D('order_repair')->where($map)->field('id')->find();
		if($is_exi['id']){
			$order_repair =  D('order_repair')->where($map)->setField('cancel_order_reason',$reason);
		}else{

			$cancel_re['cancel_order_reason'] = $reason;
			$cancel_re['order_number']        = I('order_number');
			$order_repair =  D('order_repair')->add($cancel_re);
		}
		$order =  D('order')->where($map)->save(array('status'=>2,'repair_service_id'=>''));
		$order_user =  D('order_user')->where($map)->save(array('status'=>2,'repair_service_id'=>''));

		if($order && $order_user && $order_repair){
			$this->success('取消成功');
		}else{
			$this->error('取消失败');
		}
	}

	/**
	 * 发送配件列表
	 */
	public function send_parts_list(){
		$map['user_id'] = session('user_id');
		$map['status'] =  array(array('eq',11),array('eq',12), 'or');
		$order_number = D('order')->where($map)->field('order_number,repair_person_id')->select();
		for($i=0;$i<count($order_number);$i++){
			$where['order_number'] = $order_number[$i]['order_number'];
			$where['status'] = 2;

			$map_repa['user_id'] = $order_number[$i]['repair_person_id'];
			$user_repairer       = D('user_repairer')->field('real_name,phone,city,address')->where($map_repa)->find();
			$data[$i]            = D('pro_parts')->field('type,create_time,parts_count,parts_name,id,order_number,parts_price')->where($where)->select();
			for($j=0;$j<count($data[$i]);$j++){
				$data[$i][$j]['address'] = $user_repairer['city'].'/'.$user_repairer['address'];
				$data[$i][$j]['phone']   = $user_repairer['phone'];
				$data[$i][$j]['real_name'] = $user_repairer['real_name'];
				if( $data[$i][$j]['type'] == 1 ) $data[$i][$j]['url'] = "<a href='".U('Lianbao_PC/Userindex/logistics_list',array('order_number'=>$data[$i][$j]['order_number']))."' style=\"color: red;\">确认发货</a> ";
				if( $data[$i][$j]['type'] == 2 ) $data[$i][$j]['url'] = "<a href='".U('Lianbao_PC/Userindex/logistics_own_list',array('order_number'=>$data[$i][$j]['order_number']))."' style=\"color: red;\">同意自购件</a> ";
			}
		}
		$this->assign('list',$data);
		$this->display('send_parts');
	}

	/**
	 * 留言
	 */
	public function leaving_message(){
		$map['order_number'] = I('order_number');
		$list =	D('leaving_message')->where($map)->order('create_time desc')->select();
		$this->assign('list',$list);
		$this->assign('order_number',I('order_number'));
		$this->display('leaving_message');
	}

	/**
	 * 增加留言
	 */
	public function add_message(){
		$content = I('content');
		$order_number = I('order_number');
		if(empty($content)){
			$this->error('留言内容不能为空');
		}
		$map['user_id'] = session('user_id');
		$user_shop = D('user_shop')->where($map)->field('company')->find();
		$add['create_time'] = time();
		$add['order_number']= $order_number;
		$add['content']     = $content;
		$add['person']      = $user_shop['company'];
		$result = D('leaving_message')->add($add);

		$track['order_number'] = $order_number;
		$track['create_time']  = time();
		$track['content']      = "商家留言：".$content;
		$track['person']       = $user_shop['company'];
		$tra = D('order_track')->add($track);

		if($result){
			$this->success('添加成功');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	 * 确认支付
	 */
	public function examine_price()
	{
		$model = D('order');
		$model->startTrans();
		$roll_va = true;
		$is_status = D('order')->where(array('order_number'=>I('order_number')))->field('status')->find();

		if($is_status['status']!= 13){
			$this->error('工单状态不正确，不能支付');
		}
		$is_pay_pass = D('user')->where(array('id'=>session('user_id')))->field('pay_password')->find();
		if($is_pay_pass['pay_password']){
			if($is_pay_pass['pay_password'] != md5(I('pay_password'))){
				$this->error('支付密码不正确');
			}
		}else{
			$this->error('支付密码不存在，请设置支付密码');
		}

		$map['order_number'] = I('order_number');
		$id = D('order')->where($map)->field('user_id,repair_person_id,repair_service_id,service_price,parts_price,far_price,repair_price,logistics_price')->find();
		$order_pro = D('order_pro')->where($map)->field('baoxiu_type,order_type')->find();
		$wallet_buyers = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->find();

		$save_buyers['balance']   = $wallet_buyers['balance']   - $id['repair_price'];
		$save_buyers['use_money'] = $wallet_buyers['use_money'] + $id['repair_price'];
		$save_buyers['frozen_balance']   = $wallet_buyers['frozen_balance'] - $id['repair_price'];
		if($order_pro['baoxiu_type'] == 1 ){
			$save_change_wallet = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->save($save_buyers);

			if(empty($save_change_wallet)){
				$roll_va = false;
			}
		}




		//获取平台提成比例
		$proportion = D('wallet_admin')->field('proportion,no_ser_proportion')->find();
		$map_repairer['user_id'] = $id['repair_person_id'];
		$map_service['user_id'] = $id['repair_service_id'];
		$repairer = D('user_repairer')->where($map_repairer)->field('parent_id,type,proportion')->find();


		if (!empty($repairer['proportion'])) {
			$pintai      = $id['service_price'] * $proportion['proportion'];
			$pintai      = sprintf("%.2f",$pintai);

			$service_all = $id['service_price'] * (1 - $proportion['proportion']);
			$service_all      = sprintf("%.2f",$service_all);

			$repair_ticheng = $service_all  * (1 - $repairer['proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'];
			$repair_ticheng      = sprintf("%.2f",$repair_ticheng);

			$service_ticheng = $service_all * $repairer['proportion'];
			$service_ticheng      = sprintf("%.2f",$service_ticheng);

			//添加至服务中心钱包
			$seller = D('wallet_seller')->where($map_service)->field('balance,all_money')->find();
			$save_ser['balance'] = $seller['balance'] + $service_ticheng;
			$save_ser['all_money'] = $seller['all_money'] + $service_ticheng;
			$se = D('wallet_seller')->where($map_service)->save($save_ser);

			if(empty($se)){
				$roll_va = false;
			}

			//添加至师傅钱包
			$repair = D('wallet_repairer')->where($map_repairer)->field('balance,all_money,taking_money,service_coucheng')->find();
			$save_repair['balance'] = $repair['balance'] + $repair_ticheng;
			$save_repair['all_money'] = $repair['all_money'] + $repair_ticheng;
			$save_repair['taking_money'] = $repair['taking_money'] + $repair_ticheng;
			$save_repair['service_coucheng'] = $repair['service_coucheng'] + $service_ticheng;
			$result = D('wallet_repairer')->where($map_repairer)->save($save_repair);

			if(empty($result)){
				$roll_va = false;
			}

			//增加至income表
			$add_income['order_number'] = I('order_number');
			$add_income['create_time']  = time();
			$add_income['pintai']       = $pintai;
			$add_income['service']      = $service_ticheng;
			$add_income['repair']       = $repair_ticheng;
			$add_income['far_price']    = $id['far_price'];
			$income = D('income')->add($add_income);
			if(empty($income)){
				$roll_va = false;
			}

			//添加到wallet_service_consumm表

			$add_service['create_time']    = time();
			$add_service['order_number']   = I('order_number');
			$add_service['order_price']    = $id['repair_price'];
			$add_service['service_shouru'] = $service_ticheng;
			$add_service['pintai_shouru']  = $pintai;
			$add_service['repairer_price'] = $repair_ticheng;
			$add_service['type']           = "1";
			$add_service['user_id']        = $id['user_id'];
			$add_service['repairer_id']    = $id['repair_person_id'];
			$add_service['service_id']     = $id['repair_service_id'];
			$add_service['buyer_id']       = $id['user_id'];
			$add_service['liushuihao']     = I('order_number');

			$wallet_service_consum = D('wallet_service_consum')->add($add_service);


			if(empty($wallet_service_consum)){
				$roll_va = false;
			}


		}
		if ($repairer['type'] == 1) {
			$pintai      = $id['service_price'] * $proportion['proportion'];
			$service_all = $id['service_price'] * (1 - $proportion['proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'] ;
			$seller = D('wallet_seller')->where($map_service)->field('balance,all_money')->find();
			$save_ser['balance'] = $seller['balance'] + $service_all;
			$save_ser['all_money'] = $seller['all_money'] + $service_all;
			$se = D('wallet_seller')->where($map_service)->save($save_ser);
			if(empty($se)){
				$roll_va = false;
			}
			$add_income['order_number'] = I('order_number');
			$add_income['create_time']  = time();
			$add_income['pintai']       = $pintai;
			$add_income['service']      = $service_all;
			$add_income['repair']       = 0;
			$add_income['far_price']    = $id['far_price'];
			$income = D('income')->add($add_income);

			if(empty($income)){
				$roll_va = false;
			}

			//添加到wallet_service_consumm表

			$add_service['create_time']    = time();
			$add_service['order_number']   = I('order_number');
			$add_service['order_price']    = $id['repair_price'];
			$add_service['service_shouru'] = $service_all;
			$add_service['pintai_shouru']  = $pintai;
			$add_service['repairer_price'] = 0;
			$add_service['type']           = "1";
			$add_service['user_id']        = $id['user_id'];
			$add_service['repairer_id']    = $id['repair_person_id'];
			$add_service['service_id']     = $id['repair_service_id'];
			$add_service['buyer_id']       = $id['user_id'];
			$add_service['liushuihao']     = I('order_number');

			$wallet_service_consum = D('wallet_service_consum')->add($add_service);

			if(empty($wallet_service_consum)){
				$roll_va = false;
			}

		}

		if ($repairer['type'] == 3 || $repairer['type'] == 4 || empty($repairer['parent_id'])) {


			$pintai      = $id['service_price'] * $proportion['no_ser_proportion'];
			$pintai      = sprintf("%.2f",$pintai);

			$repair_all  = $id['service_price'] * (1 - $proportion['no_ser_proportion']) + $id['parts_price'] + $id['far_price'] + $id['logistics_price'] ;

			$repair_all      = sprintf("%.2f",$repair_all);

			$repair = D('wallet_repairer')->where($map_repairer)->field('balance,all_money,taking_money')->find();
			$save_repair['balance'] = $repair['balance'] + $repair_all;
			$save_repair['all_money'] = $repair['all_money'] + $repair_all;
			$save_repair['taking_money'] = $repair['taking_money'] + $repair_all;
			$re = D('wallet_repairer')->where($map_repairer)->save($save_repair);

			if(empty($re)){
				$roll_va = false;
			}


			$add_income['order_number'] = I('order_number');
			$add_income['create_time']  = time();
			$add_income['service']      = 0;
			$add_income['pintai']       = $pintai;
			$add_income['repair']       = $repair_all;
			$add_income['far_price']    = $id['far_price'];
			$income = D('income')->add($add_income);

			if(empty($income)){
				$roll_va = false;
			}



			//添加到wallet_service_consumm表

			$add_service['create_time']    = time();
			$add_service['order_number']   = I('order_number');
			$add_service['order_price']    = $id['repair_price'];
			$add_service['service_shouru'] = 0;
			$add_service['pintai_shouru']  = $pintai;
			$add_service['repairer_price'] = $repair_all;
			$add_service['type']           = "1";
			$add_service['user_id']        = $id['user_id'];
			$add_service['repairer_id']    = $id['repair_person_id'];
			$add_service['service_id']     = $id['repair_service_id'];
			$add_service['buyer_id']       = $id['user_id'];
			$add_service['liushuihao']     = I('order_number');

			$wallet_service_consum = D('wallet_service_consum')->add($add_service);

			if(empty($wallet_service_consum)){
				$roll_va = false;
			}

		}

		$status['status'] = '7';
		$order = D('order')->where($map)->save($status);

		$order_user = D('order_user')->where($map)->save($status);
		$end_time['end_time'] = time();
		$end_time['pay_time'] = time();
		$time = D('order_repair')->where($map)->save($end_time);

		if(empty($time) ||  empty($order) || empty($order_user)){
			$roll_va = false;
		}


		if($order_pro['baoxiu_type']==2){

			if($repairer['type'] == 1){
				$save_se_balance =  D('wallet_seller')->where(array('user_id'=>$id['repair_service_id']))->field('balance')->find();
				$save['balance'] = $save_se_balance['balance']-$id['repair_price'];
				$walle_seller = D('wallet_seller')->where(array('user_id'=>$id['repair_service_id']))->save($save);
				if(empty($walle_seller)){
					$roll_va = false;
				}
			}else{

				$save_se_balance =  D('wallet_repairer')->where(array('user_id'=>$id['repair_person_id']))->field('balance')->find();
				$save['balance'] = $save_se_balance['balance']-$id['repair_price'];
				$redaa           =  D('wallet_repairer')->where(array('user_id'=>$id['repair_person_id']))->save($save);
				if(empty($redaa)){
					$roll_va = false;
				}
			}
		}

		$shop_name = D('user_shop')->where(array('user_id'=>session('user_id')))->field('company')->find();
		$data['content'] = $shop_name['company']."已完成付款";
		$data['create_time']    =time();
		$data['person']  =$shop_name['company'];
		$data['order_number'] = I('order_number');
		$result = D('order_track')->add($data);
		if(empty($result)){
			$roll_va = false;
		}

		if($roll_va == true){
			$model->commit();
			$this->success('付款成功',U('Userindex/userindex'));
		} else {
			$model->rollback();
			$this->error('付款失败');
		}
	}

	function geturl($param, $root = true)
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


	public function agree_far_order(){
		$save['far_price']  = I('far_price');
		$order = D('order')->where(array('order_number'=>I('order_number')))->field('repair_price')->find();
		$save['repair_price'] = $order['repair_price'] + I('far_price');
		$result = D('order')->where(array('order_number'=>I('order_number')))->save($save);
		if($result){

			$res = D('order_far_order')->where(array('order_number'=>I('order_number')))->setField('status',2);

		}
		if($res){
			$shop_name = D('user_shop')->where(array('user_id'=>session('user_id')))->field('company')->find();
			$data['content'] = $shop_name['company']."已同意远程费单";
			$data['create_time']    =time();
			$data['person']  =$shop_name['company'];
			$data['order_number'] = I('order_number');
			$r = D('order_track')->add($data);
		}

		if($r){
			$this->success('远程费用确认成功');
		}else{
			$this->error('远程费用确认失败');
		}


	}

	/**
	 * 确认收货
	 */
	public function receive_parts(){
		$order_number = I('order_number');
		$map['order_number'] = $order_number;
		$save['status']   = 13;
		$save['end_time'] = time();
		D('order')->where($map)->setField('status', 13);
		D('order_user')->where($map)->setField('status', 13);
		D('order_repair')->where($map)->setField('end_time',time());
		$user_shop = D('user_shop')->where(array('user_id'=>session('user_id')))->field('company')->find();


		$data['content'] = $user_shop['company']."确认收货";
		$data['create_time']    =time();
		$data['person']  = $user_shop['company'];
		$data['order_number'] = $order_number;
		$result = D('order_track')->add($data);

		if(  $result  ){
			$this->success('确认收货成功');
		}else{
			$this->error('确认收货失败');
		}
	}


}