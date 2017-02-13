<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class WalletController extends BaseController {
	//钱包的金额
	public function index(){
		$user_id = session('user_id');

			$wallet = M('wallet_buyers')->where("user_id='{$user_id}'")->find();
			$wallet['frozen_balance'] = $wallet['balance'] +  $wallet['bond'];
			M('wallet_buyers')->where("user_id='{$user_id}'")->setField('frozen_balance',$wallet['frozen_balance']);
			if(empty($wallet)){
				$add['user_id'] = $user_id;
				$add['create_time'] = time();
				$add['balance'] = 0;
				$add['frozen_balance'] = 0;
				$add['bond'] = 0;
				$add['present_money'] = 0;
				$add['recharge_money'] = 0;
				$add['frozen_money'] = 0;
				$add['use_money'] = 0;
				M('wallet_buyers')->add($add);
			}

			$this->assign('data',$wallet);
			$this->display('index');

	}
	//充值记录;
	public function recharg_history(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'wallet_recharge';
		$key_cond   = '';
		$data = A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);



		if($submit){
			$name = "充值记录";
			$this->Excel_chongzhi($data,$name);
		}
			$this->display('recharg_history');
		
	}
	//查询未付款订单
	public function recharg_wait(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '1';
		$biao       = 'wallet_recharge';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		if($submit){
			$name = "未支付订单";
			$this->Excel_chongzhi($data,$name);
		}
		$this->display('recharg_wait');
	
 }
	//查询已取消订单
	public function recharg_cancel(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '2';
		$biao       = 'wallet_recharge';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		if($submit){
			$name = "已取消订单";
			$this->Excel_chongzhi($data,$name);
		}
		$this->display('recharg_cancel');
	
 }
	//查询付款成功订单
	public function recharg_pay(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '0';
		$biao       = 'wallet_recharge';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		if($submit){
			$name = "成功付款订单";
			$this->Excel_chongzhi($data,$name);
		}
		$this->display('recharg_pay');
	
 }
	//单量查询
	public function danliang(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'wallet_buydanliang';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		
		for($i=0;$i<=count($data);$i++){
			if($data[$i]['buy_type']==1){
				$data[$i]['buy_type']="安装单量";				
			}
			if($data[$i]['buy_type']==2){
				$data[$i]['buy_type']="维修单量";				
			}
			if($data[$i]['buy_type']==3){
				$data[$i]['buy_type']="送修单量";				
			}
	
		}
		$this->assign('data',$data);		
		//dump($data);exit;
		if($submit){
			$name = "单量记录";
			$this->Excel_danliang($data,$name);
		}
		$this->display('danliang');
	} 
	//安装单购买查询
	public function danliang_install(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = 1;
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'wallet_buydanliang';
		$key_cond   = 'buy_type';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		
		for($i=0;$i<=count($data);$i++){
			if($data[$i]['buy_type']==1){
				$data[$i]['buy_type']="安装单量";				
			}
			if($data[$i]['buy_type']==2){
				$data[$i]['buy_type']="维修单量";				
			}
			if($data[$i]['buy_type']==3){
				$data[$i]['buy_type']="送修单量";				
			}
	
		}
		$this->assign('data',$data);		
		//dump($data);exit;
		if($submit){
			$name = "安装单量记录";
			$this->Excel_danliang($data,$name);
		}
		$this->display('danliang_install');
	} 
	//送修单量购买查询
	public function danliang_send(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = 2;
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'wallet_buydanliang';
		$key_cond   = 'buy_type';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		
		for($i=0;$i<=count($data);$i++){
			if($data[$i]['buy_type']==1){
				$data[$i]['buy_type']="安装单量";				
			}
			if($data[$i]['buy_type']==2){
				$data[$i]['buy_type']="维修单量";				
			}
			if($data[$i]['buy_type']==3){
				$data[$i]['buy_type']="送修单量";				
			}
	
		}
		$this->assign('data',$data);		
		//dump($data);exit;
		if($submit){
			$name = "安装单量记录";
			$this->Excel_danliang($data,$name);
		}
		$this->display('danliang_send');
	} 
	//维修单量查询
	public function danliang_repair(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = 3;
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'wallet_buydanliang';
		$key_cond   = 'buy_type';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		
		for($i=0;$i<=count($data);$i++){
			if($data[$i]['buy_type']==1){
				$data[$i]['buy_type']="安装单量";				
			}
			if($data[$i]['buy_type']==2){
				$data[$i]['buy_type']="维修单量";				
			}
			if($data[$i]['buy_type']==3){
				$data[$i]['buy_type']="送修单量";				
			}
	
		}
		$this->assign('data',$data);		
		//dump($data);exit;
		if($submit){
			$name = "安装单量记录";
			$this->Excel_danliang($data,$name);
		}
		$this->display('danliang_repair');
	} 
	//每月账单
	public function month_bill(){
		$user_id   = session('user_id');

			$create_time = M('user')->where("id='{$user_id}'")->field('create_time')->find();

			$data = month_sel($create_time);
			$time = $data['time'];
			array_pop($data);
			$status = '';

			$bill = $this->month_data($data,$time,$user_id,$status);
			$this->assign('list',$bill);
			$this->display('month_bill');	

	}
	//每月账单查询送修
		public function month_send(){
		$user_id   = session('user_id');
		if($user_id){
			$create_time = M('user')->where("id='{$user_id}'")->field('create_time')->find();
			$data = month_sel($create_time);
			$time = $data['time'];
			array_pop($data);
			$status = '3';
			$bill = $this->month_data($data,$time,$user_id,$status);
			$this->assign('list',$bill);
			$this->display('month_send');	
		}else{
			$this->redirect('Login/index','',1, '未登陆，请您登陆');
		}			
	}
	//每月账单查询维修
	public function month_repair(){
		$user_id   = session('user_id');
		if($user_id){
			$create_time = M('user')->where("id='{$user_id}'")->field('create_time')->find();
			$data = month_sel($create_time);
			$time = $data['time'];
			array_pop($data);
			$status = '2';
			$bill = $this->month_data($data,$time,$user_id,$status);
			$this->assign('list',$bill);
			$this->display('month_repair');	
		}else{
			$this->redirect('Login/index','',1, '未登陆，请您登陆');
		}			
	}
	public function month_install(){
		$user_id   = session('user_id');
		if($user_id){
			$create_time = M('user')->where("id='{$user_id}'")->field('create_time')->find();
			$data = month_sel($create_time);
			$time = $data['time'];
			array_pop($data);
			$status = '1';
			$bill = $this->month_data($data,$time,$user_id,$status);
			$this->assign('list',$bill);
			$this->display('month_install');	
		}else{
			$this->redirect('Login/index','',1, '未登陆，请您登陆');
		}			
	}
	
	//其他支出
	public function other_pay(){
		$submit     = I('post.daochu');
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'wallet_other_pay';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		if($submit){
			$name = "其他支出记录";
			$this->Excel_other($data,$name);
		}
		$this->display('other_pay');
	}
	//全部保证金记录
		public function bond(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id    = session('user_id');
		$status     = '';
		$biao       = 'wallet_bond';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
			for($i=0;$i<count($data);$i++){
				if($data[$i]['pay_type']==1)  $data[$i]['pay_type'] = "缴纳";
				if($data[$i]['pay_type']==2)  $data[$i]['pay_type'] = "提取";
			}


		$this->assign('list',$data);
		$this->display('bond');
	}
	//提取保证金记录
		public function bond_extract(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   	= session('user_id');
		$status     = '2';
		$biao       = 'wallet_bond';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		$this->display('bond_extract');
	}
	//缴纳保证金记录
		public function bond_pay(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '1';
		$biao       = 'wallet_bond';
		$key_cond   = '';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		$this->display('bond_pay');
	}
	//冻结金额的查询
		public function frozen_money(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.wnumber');
		$user_id   	= session('user_id');
		$status     = '';
		$biao       = 'wallet_frozen';
		$key_cond   = 'frozen_number';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		$this->display('frozen_money');
	}
		public function jiao_bond(){
			$money = I('post.money');
			//dump($money);exit;
			$user_id  = session('user_id');
			$time      = time();
			if($user_id){
				$wal = M('wallet_buyers')->where("user_id='{$user_id}'")->find();
				//dump($wal);exit;
				$wallet['balance'] = $wal['balance']-$money;
				$wallet['frozen_balance'] = $wal['frozen_balance']-$money;
				$wallet['bond']        = $wal['bond']+$money;
				if($wallet['balance']>=0){					
					$result_one = M('wallet_buyers')->where("user_id='{$user_id}'")->save($wallet);
					$bond['bond_number']    = rand_num(5);
					$bond['status'] = '1';
					$bond['user_id'] = $user_id;
					$bond['create_time'] = $time;
					$bond['bond']        = $money;
					$bond['pay_type']    = '1';
					$result_two = M('wallet_bond')->where("user_id='{$user_id}'")->add($bond);
					if($result_two && $result_one){
							$this->ajaxReturn(0);
					}else{
						    $this->ajaxReturn(2);
					}					
 				}else{
					$this->ajaxReturn(1);									
				}
			}else{
				$this->redirect('Login/index','',1, '未登陆，请您登陆');
			}
			
		}
		//导出Excel数据
		public function Excel_chongzhi($data,$name){
			import("Org.Util.PHPExcel");
			error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			$objPHPExcel = new \PHPExcel();
			$objPHPExcel->getProperties()->setCreator("123")
						->setLastModifiedBy("321")
						->setTitle("数据EXCEL导出")
						->setSubject("数据EXCEL导出")
						->setDescription("备份数据")
						->setKeywords("excel")
						->setCategory("result file");
						
			foreach($data as $k => $v){
				$num=$k+1;
				$objPHPExcel->setActiveSheetIndex(0) 
							->setCellValue('A'.$num, $v['recharge_money'])    
							->setCellValue('B'.$num, $v['recharge_number'])
							->setCellValue('C'.$num, $v['recharge_title'])
							->setCellValue('D'.$num, $v['pay_money'])
							->setCellValue('E'.$num, $v['present_money'])
							->setCellValue('F'.$num, $v['status'])
							->setCellValue('G'.$num, $v['pay_type'])
							->setCellValue('H'.$num, $v['pay_name']);
				}
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
	}
			public function Excel_danliang($data,$name){
			import("Org.Util.PHPExcel");
			error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			$objPHPExcel = new \PHPExcel();
			$objPHPExcel->getProperties()->setCreator("123")
						->setLastModifiedBy("321")
						->setTitle("数据EXCEL导出")
						->setSubject("数据EXCEL导出")
						->setDescription("备份数据")
						->setKeywords("excel")
						->setCategory("result file");
						
			foreach($data as $k => $v){
				$num = $k+1;
				$objPHPExcel->setActiveSheetIndex(0) 
							->setCellValue('A'.$num, $v['user_id'])    
							->setCellValue('B'.$num, $v['buy_number'])
							->setCellValue('C'.$num, $v['buy_count'])
							->setCellValue('D'.$num, $v['buy_type'])
							->setCellValue('E'.$num, $v['remarks'])
							->setCellValue('F'.$num, $v['buy_price'])
							->setCellValue('G'.$num, $v['install'])
							->setCellValue('H'.$num, $v['repair'])
							->setCellValue('I'.$num, $v['send']);
														
				}
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
	}
		public function Excel_other($data,$name){
			import("Org.Util.PHPExcel");
			error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			$objPHPExcel = new \PHPExcel();
			$objPHPExcel->getProperties()->setCreator("123")
						->setLastModifiedBy("321")
						->setTitle("数据EXCEL导出")
						->setSubject("数据EXCEL导出")
						->setDescription("备份数据")
						->setKeywords("excel")
						->setCategory("result file");
						
			foreach($data as $k => $v){
				$num=$k+1;
				$objPHPExcel->setActiveSheetIndex(0) 
							->setCellValue('A'.$num, $v['other_pay_number'])    
							->setCellValue('B'.$num, $v['other_pay_money'])
							->setCellValue('C'.$num, $v['other_pay_type'])
							->setCellValue('D'.$num, $v['remarks'])
							->setCellValue('E'.$num, $v['user_id']);
														
				}
			$objPHPExcel->getActiveSheet()->setTitle($name);
			$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$name.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
	}
	//账单数据的处理
	public function month_data($data,$time,$user_id,$status){

		   if(empty($status)){
			   for($i=0;$i<count($data);$i++){
				   $map['user_id'] = $user_id;
				   $repair_price   = D('order')->where($data[$i])->where($map)->field('SUM(repair_price)')->find();
				   $recharge_money = D('wallet_recharge')->where($data[$i])->where($map)->field('SUM(recharge_money)')->find();
				   $install        = D('wallet_buydanliang')->where($data[$i])->where($map)->field('SUM(install)')->find();
				   $repair         = D('wallet_buydanliang')->where($data[$i])->where($map)->field('SUM(repair)')->find();
				   $send           = D('wallet_buydanliang')->where($data[$i])->where($map)->field('SUM(send)')->find();
				   $time           = $time[$i];

				   $bill[$i]['repair_price']  = $repair_price['sum(repair_price)'];
				   $bill[$i]['recharge_money']= $recharge_money['sum(recharge_money)'];
				   $bill[$i]['install']       = $install['sum(install)'];
				   $bill[$i]['repair']        = $repair['sum(repair)'];
				   $bill[$i]['send']          = $send['sum(send)'];
				   $bill[$i]['time']          = $time;

			     }
		   }

		return $bill;


	}
}