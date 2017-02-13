<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class PublishWorkOrderController extends BaseController {
	public function index(){
		$this->auto_price();
        $this->auto_receive_parts();
		$this->deal_order();
		$this->display('index');
	}

	public function install_index(){
		$user_id = session('user_id');
			$product = M('product')->where("user_id='{$user_id}'")->select();
			$huishou = strtotime("+1 days");
			$this->assign('huishou',$huishou);
			$this->assign('product',$product);
			$this->display('install');
	}
	public function repair_index(){
		$user_id = session('user_id');
			$product = M('product')->where("user_id='{$user_id}'")->select();
			$huishou = strtotime("+1 days");
			$this->assign('huishou',$huishou);
			$this->assign('product',$product);
			$this->display('repair');
	}
	public function send_repair_index(){
		$user_id = session('user_id');
		$product = M('product')->where("user_id='{$user_id}'")->select();
		$huishou = strtotime("+1 days");
		$this->assign('huishou',$huishou);
		$this->assign('product',$product);
		$this->display('send_repair');

	}
	//安装单
	public function install_add(){
		$examine = is_examine();
		if($examine['examine_status'] == 1){
			$this->ajaxReturn(2);exit;
		}
		$user_id  = session('user_id');
		$order_num             = order_number();
		$price                 = I('post.money_product');
		$order['service_price']= $price;
		$order['repair_price'] = $price;
		$order['user_id']	   = $user_id;
		$order['pro_price']    = $price;
		$order['order_number'] = $order_num;
		$order['create_time']  = time();
		$order['status']= 1;
		$order['order_type']   = 1;
		$order_add_service['add_service']  = "上门安装";
		$order_add_service['create_time']  = time();
		$order_add_service['order_number'] = $order_num;
		D('order_add_service')->add($order_add_service);
		$user['pro_price']     = I('money_product');
		$user['user_city']     = I('post.city');
		$user['order_number']  = $order_num;
		$user['user_name'] 	   = I('post.rel_name');
		$user['user_address']  = I('post.address');
		$user['create_time']   = time();
		$user['user_id']      = $user_id;
		$user['status']       = 1;
		$user['user_phone']  = I('post.tel');
		//添加产品信息的表
		$pro_map['id']        = I('post.id');
		$product_mess         = M('product')->where($pro_map)->find();
		$pro['order_number']  = $order_num;
		$pro['pro_name']      = $product_mess['product'];
		$pro['pro_xinhao']    = $product_mess['product_name'];
		$pro['pro_price']     = I('money_product');
 		$pro['pro_property']  = $product_mess['property'];
		$pro['pro_count']     = 1;
		$pro['pro_product']   = $product_mess['pro_product'];
		$pro['baoxiu_type']   = I('post.reference');
		$pro['recovery_time'] = I('post.recover_time');
		$pro['remarks']       = I('post.remaks');
		$pro['order_type']    = 1;
			$result_1 = M('order')->add($order);
			$result_2 = M('order_user')->add($user);
			$result_3 = M('order_pro')->add($pro);
        $user_shop = D('user_shop')->where("user_id=".$user_id)->field('company')->find();
		$add_service['order_number'] = $order_num;
		$add_service['add_service']  = $price;
		$add_service['create_time']  = time();
		D('order_add_service')->add($add_service);
		$track['order_number'] = $order_num;
		$track['create_time']  = time();
		$track['content']      = "厂商发布工单成功";
		$track['person']       = $user_shop['company'];
		$tra = D('order_track')->add($track);
			if($result_1 && $result_2 &&  $result_3 && $tra){
				$this->exist_service($user['user_city']);
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
	}



	//维修单
	public function repair(){
		$examine = is_examine();
		if($examine['examine_status'] == 1){
			$this->ajaxReturn(2);exit;
		}
		$user_id  = session('user_id');
		$order_num            = order_number();
		//添加表单基本信息
		$price                = I('post.money_product');
		$order['repair_price']= '0';
		$order['user_id']	  = $user_id;
		$order['pro_price']   = $price;
		$order['order_number']= $order_num;
		$order['create_time'] = time();
		$order['status']      = 1;
		$order['order_type']  = 2;
		//添加表单用户基本信息
		$user['pro_price']    = I('money_product');
		$user['user_city']    = I('post.city');
		$user['order_number'] = $order_num;
		$user['user_name'] 	  = I('post.rel_name');
		$user['user_address'] = I('post.address');
		$user['create_time']  = time();
		$user['user_id']      = $user_id;
		$user['status']       = 1;
		$user['user_phone']  = I('post.tel');
		//添加产品信息的表
		$pro_map['id']        = I('post.id');
		$product_mess         = M('product')->where($pro_map)->find();
		$pro['order_number']  = $order_num;
		$pro['pro_name']      = $product_mess['product'];
		$pro['pro_xinhao']    = $product_mess['product_name'];
		$pro['pro_price']     = I('money_product');
		$pro['pro_property']  = $product_mess['property'];
		$pro['pro_product']   = $product_mess['pro_product'];
		$pro['baoxiu_type']   = I('post.reference');
		$pro['recovery_time'] = I('post.recover_time');
		$pro['remarks']       = I('post.remaks');
		$pro['order_type']    = 2;
		$pro['pro_count']     = 1;
		$result_1 = M('order')->add($order);
		$result_2 = M('order_user')->add($user);
		$result_3 = M('order_pro')->add($pro);
		$user_shop = D('user_shop')->where("user_id=".$user_id)->field('company')->find();
		$track['order_number'] = $order_num;
		$track['create_time']  = time();
		$track['content']      = "厂商发布工单成功";
		$track['person']       = $user_shop['company'];
		$tra = D('order_track')->add($track);
		if($result_1 && $result_2 &&  $result_3 && $tra){
			$this->exist_service($user['user_city']);
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}

	}

	//客户送修单
	public function send_repair(){
		$examine = is_examine();
		if($examine['examine_status'] == 1){
			$this->ajaxReturn(2);exit;
		}
		$user_id  = session('user_id');
		$order_num            = order_number();
		//添加表单基本信息
		$price                = I('post.money_product');
		$order['repair_price']= '0';
		$order['user_id']	  = $user_id;
		$order['pro_price']   = $price;
		$order['order_number']= $order_num;
		$order['create_time'] = time();
		$order['status']      = 1;
		$order['order_type']  = 3;
		//添加表单用户基本信息
		$user['pro_price']    = I('money_product');
		$user['user_city']    = I('post.city');
		$user['order_number'] = $order_num;
		$user['user_name'] 	  = I('post.rel_name');
		$user['user_address'] = I('post.address');
		$user['create_time']  = time();
		$user['user_id']      = $user_id;
		$user['status']       = 1;
		$user['user_phone']  = I('post.tel');
		//添加产品信息的表
		$pro_map['id']        = I('post.id');
		$product_mess         = M('product')->where($pro_map)->find();
		$pro['order_number']  = $order_num;
		$pro['pro_name']      = $product_mess['product'];
		$pro['pro_xinhao']    = $product_mess['product_name'];
		$pro['pro_price']     = I('money_product');
		$pro['pro_property']  = $product_mess['property'];
		$pro['pro_product']   = $product_mess['pro_product'];
		$pro['baoxiu_type']   = I('post.reference');
		$pro['recovery_time'] = I('post.recover_time');
		$pro['remarks']       = I('post.remaks');
		$pro['order_type']    = 3;
		$pro['pro_count']     = 1;
		$result_1 = M('order')->add($order);
		$result_2 = M('order_user')->add($user);
		$result_3 = M('order_pro')->add($pro);
		$user_shop = D('user_shop')->where("user_id=".$user_id)->field('company')->find();
		$track['order_number'] = $order_num;
		$track['create_time']  = time();
		$track['content']      = "厂商发布工单成功";
		$track['person']       = $user_shop['company'];
		$tra = D('order_track')->add($track);
		if($result_1 && $result_2 &&  $result_3 && $tra){
			$this->exist_service($user['user_city']);
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
	}

	private function exist_service($city){

		$map_shop['shop_location'] = array('like',array('%'.$city.'%'));

		$service = D('user_service')->where($map_shop)->field('id')->find();
		$content = "【驰达家维】：您有新订单，请登录师傅端查看；";
		if(empty($service['id'])){
			 $user_id = D('user_repairer')->where(array('city'=>$city))->field('user_id')->select();
			for($i=0;$i<count($user_id);$i++){
				$username = D('user')->where(array('id'=>$user_id[$i]['user_id']))->field('username')->find();
				send_info_fiel($username['username'],$content);
			}
		}


	}

	/**
	 * 搜索产品
	 */
	public function select_pro(){

		$order_type = I('type');
		$user_id = session('user_id');
		$product = I('post.product');
		$map['user_id'] = $user_id;
		$map['id'] = $product;
		$price = M('product')->where($map)->field('product_price,pro_price_send')->find();
		if( $order_type==2){
			$data = $price['product_price'];
		}
		if( $order_type==3 ){
			$data = $price['pro_price_send'];
		}
		json_encode($data);
		$this->ajaxReturn($data);
}
	public function select_pro_install(){
		$order_type = I('order_type');
		$user_id = session('user_id');
		$product = I('post.product');
		$map['user_id'] = $user_id;
		$map['id'] = $product;
		$price = M('product')->where($map)->field('install_price,install_price_wai')->find();
		if( $order_type==1 ){
			$data = $price['install_price'];
		}
		if( $order_type==2 ){
			$data = $price['install_price_wai'];
		}
		json_encode($data);
		$this->ajaxReturn($data);
	}

	public function select_ser(){
		$id['id']   = I('id');
		$product = D('product')->where($id)->field('product_name,pro_product,property')->find();
		$map['pro_pinlei'] = $product['product_name'];
		$map['product']    = $product['pro_product'];
		$map['pro_property'] = $product['property'];
		$map['order_type']   = '维修';
		$service = D('pro_price_detail')->where($map)->field('id,service_project')->select();
		$str = '';
		for($i=0;$i<count($service);$i++){
			$str .= "<option value='".$service[$i]['id']."'>".$service[$i]['service_project']."</option>";
		}
		$this->ajaxReturn($str);
	}


	public function select_send(){
		$id['id']   = I('id');
		$product = D('product')->where($id)->field('product_name,pro_product,property')->find();
		$map['pro_pinlei'] = $product['product_name'];
		$map['product']    = $product['pro_product'];
		$map['pro_property'] = $product['property'];
		$map['order_type']   = '送修';
		$service = D('pro_price_detail')->where($map)->field('id,service_project')->select();
		$str = '';
		for($i=0;$i<count($service);$i++){
			$str .= "<option value='".$service[$i]['id']."'>".$service[$i]['service_project']."</option>";
		}
		$this->ajaxReturn($str);
	}


	public function judge_price(){
		$user_id       = session('user_id');
		$procuct_id    = I('product');
		$reference     = I('reference');
		$order_type    = I('order_type');
		$map['id']     = $procuct_id;
		$where['user_id']  =  $user_id;
		//安装单
		if($order_type==1){

			if($reference==1){

				$wallet_balance = D('wallet_buyers')->where(array('user_id'=>$user_id))->field('balance')->find();
				$product = D('product')->where($map)->field('install_price')->find();

				$danliang= D('wallet_admin')->field('install')->find();
				$wallet  = D('wallet_danliang')->where($where)->field('install')->find();

				if($wallet['install'] >= $danliang['install'] && $wallet_balance['balance']>=$product['product_price']){
					D('wallet_danliang')->where($where)->setField('install',$wallet['install'] - $danliang['install']);
					$array = array('status'=>1,'content'=>'');
				}
				if($wallet['install'] < $danliang['install'])
				{
					$array = array('status'=>2,'content'=>'安装单量必须大于'.$danliang['install'].'');
				}

				if($wallet_balance['balance'] < $product['install_price']){

					$array = array('status'=>2,'content'=>'用户余额必须大于'.$product['install_price'].'');

				}
			}
			if($reference==2){
				$array = array('status'=>1,'content'=>'');
			}

		}
		//维修单
		if($order_type==2){


			if($reference==1){


				$wallet_balance = D('wallet_buyers')->where(array('user_id'=>$user_id))->field('balance')->find();
				$product = D('product')->where($map)->field('product_price')->find();
				$danliang= D('wallet_admin')->field('repair')->find();
				$wallet  = D('wallet_danliang')->where($where)->field('repair')->find();

				if($wallet['repair'] >= $danliang['repair'] && $wallet_balance['balance']>=$product['product_price']){
					D('wallet_danliang')->where($where)->setField('repair',$wallet['repair'] -  $danliang['repair']);
					$array = array('status'=>1,'content'=>'');
				}
				if($wallet['repair'] < $danliang['repair'])
				{
					$array = array('status'=>2,'content'=>'维修单量必须大于'.$danliang['repair'].'');
				}
				if($wallet_balance['balance'] < $product['product_price'])
				{
					$array = array('status'=>2,'content'=>'用户余额必须大于'.$product['product_price'].'');

				}
			}
			if($reference==2){
				$array = array('status'=>1,'content'=>'');
			}
		}

		if($order_type==3){
			if($reference==1){


				$wallet_balance = D('wallet_buyers')->where(array('user_id'=>$user_id))->field('balance')->find();
				$product = D('product')->where($map)->field('pro_price_send')->find();
				$danliang= D('wallet_admin')->field('send')->find();
				$wallet  = D('wallet_danliang')->where($where)->field('send')->find();

				if($wallet['send'] >= $danliang['send'] && $wallet_balance['balance']>=$product['pro_price_send']){
					D('wallet_danliang')->where($where)->setField('send', $wallet['send'] -  $danliang['send']);
					$array = array('status'=>1,'content'=>'');
				}
				if($wallet['send'] < $danliang['send'])
				{
					$array = array('status'=>2,'content'=>'送修单量必须大于'.$danliang['send'].'');
				}
				if($wallet_balance['balance'] < $product['pro_price_send'])
				{
					$array = array('status'=>2,'content'=>'用户余额必须大于'.$product['pro_price_send'].'');

				}

			}
			if($reference==2){
				$array = array('status'=>1,'content'=>'');
			}
		}

		$this->ajaxReturn($array);

	}


	private function deal_order(){

		$order_number = D('order')->where("status = 1 and create_time<=".strtotime('-1 day') )->field('order_number')->select();
		for($i=0;$i<count($order_number);$i++){
			$map['order_number'] = $order_number[$i]['order_number'];
		 	$res_order = D('order')->where($map)->setField('status',2);
			$res_user  = D('order_user')->where($map)->setField('status',2);
		}
	}


    /**
     *自动确认收货
     */
    public function auto_receive_parts()
    {
        $order_number_order = D('order')->where("status = 20" )->field('order_number')->select();
        for($j=0;$j<count($order_number_order);$j++){
            $map_repair['order_number'] = $order_number_order[$j]['order_number'];
            $map_repair['confirm_time']     = array('elt',strtotime('-7 day'));
            $ord_nu_re = D('order_repair')->where($map_repair)->field('order_number')->find();
            $ord_num[] = $ord_nu_re;
        }

        for($i=0;$i<count($ord_num);$i++){
            $this->receive_parts($ord_num[$i]['order_number']);
        }
    }


    /**
     * 确认收货
     */
    private function receive_parts($order_number){
        $map['order_number'] = $order_number;
        $save['status']   = 13;

        $res_order = D('order')->where($map)->setField('status', 13);
        $res_user  = D('order_user')->where($map)->setField('status', 13);
                     D('order_repair')->where($map)->setField('end_time',time());
        $data['content'] = "自动确认收货";
        $data['create_time']    =time();
        $data['person']  ="驰达家维";
        $data['order_number'] = $order_number;
        $result = D('order_track')->add($data);

    }




	/**
	 *自动支付
	 */
	public function auto_price()
	{
		$order_number_order = D('order')->where("status = 13" )->field('order_number')->select();
		for($j=0;$j<count($order_number_order);$j++){
			$map_repair['order_number'] = $order_number_order[$j]['order_number'];
			$map_repair['end_time']     = array('elt',strtotime('-7 day'));
			$ord_nu_re = D('order_repair')->where($map_repair)->field('order_number')->find();
			$ord_num[] = $ord_nu_re;
		}


           if(!empty($ord_num[0])){

               for($i=0;$i<count($ord_num);$i++){
				   $content = "系统自动扣款";
				   $user    = "驰达家维";
                   $this->auto_a_price($ord_num[$i]['order_number'],$content,$user);
               }
           }

	}


    /**
     * 确认支付
     */
    public function auto_a_price($order_number,$content,$user)
    {


        $model = D('order');
        $model->startTrans();
        $roll_va = true;
        $is_status = D('order')->where(array('order_number'=>$order_number))->field('status')->find();

        if($is_status['status'] != 13){
            $this->error('工单状态不正确，不能支付');
        }

        $map['order_number'] = $order_number;
        $id = D('order')->where($map)->field('user_id,repair_person_id,repair_service_id,service_price,parts_price,far_price,repair_price,logistics_price')->find();
        $order_pro = D('order_pro')->where($map)->field('baoxiu_type,order_type')->find();
        $wallet_buyers = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->find();
        $save_buyers['balance']   = $wallet_buyers['balance']   - $id['repair_price'];
        $save_buyers['use_money'] = $wallet_buyers['use_money'] + $id['repair_price'];
        $save_buyers['frozen_balance']   = $wallet_buyers['frozen_balance'] - $id['repair_price'];
        $save_change_wallet = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->save($save_buyers);

        if(empty($save_change_wallet)){
            $roll_va = false;
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
            $add_income['order_number'] = $order_number;
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
            $add_service['order_number']   = $order_number;
            $add_service['order_price']    = $id['repair_price'];
            $add_service['service_shouru'] = $service_ticheng;
            $add_service['pintai_shouru']  = $pintai;
            $add_service['repairer_price'] = $repair_ticheng;
            $add_service['type']           = "1";
            $add_service['user_id']        = $id['user_id'];
            $add_service['repairer_id']    = $id['repair_person_id'];
            $add_service['service_id']     = $id['repair_service_id'];
            $add_service['buyer_id']       = $id['user_id'];
            $add_service['liushuihao']     = $order_number;

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
            $add_income['order_number'] = $order_number;
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
            $add_service['order_number']   = $order_number;
            $add_service['order_price']    = $id['repair_price'];
            $add_service['service_shouru'] = $service_all;
            $add_service['pintai_shouru']  = $pintai;
            $add_service['repairer_price'] = 0;
            $add_service['type']           = "1";
            $add_service['user_id']        = $id['user_id'];
            $add_service['repairer_id']    = $id['repair_person_id'];
            $add_service['service_id']     = $id['repair_service_id'];
            $add_service['buyer_id']       = $id['user_id'];
            $add_service['liushuihao']     = $order_number;

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




            //添加到wallet_service_consumm表

            $add_service['create_time']    = time();
            $add_service['order_number']   = $order_number;
            $add_service['order_price']    = $id['repair_price'];
            $add_service['service_shouru'] = 0;
            $add_service['pintai_shouru']  = $pintai;
            $add_service['repairer_price'] = $repair_all;
            $add_service['type']           = "1";
            $add_service['user_id']        = $id['user_id'];
            $add_service['repairer_id']    = $id['repair_person_id'];
            $add_service['service_id']     = $id['repair_service_id'];
            $add_service['buyer_id']       = $id['user_id'];
            $add_service['liushuihao']     = $order_number;

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

        $data['content'] = $content;
        $data['create_time']    =time();
        $data['person']   =  $user;
        $data['order_number'] = $order_number;
        $result = D('order_track')->add($data);
        if(empty($result)){
            $roll_va = false;
        }

        if ($roll_va == true) {
            $model->commit();
        } else {
            $model->rollback();
        }
    }



}