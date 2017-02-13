<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class ConsumeMoneyController extends BaseController {
	public function index(){
		$submit     = I('post.daochu');		
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = '';
		$user_id    = session('user_id');
		$status     = '';
		$biao       = 'wallet_service_consum';
		$key_cond   = '';
		$data       = A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['type'] == 1 )  {
				$data[$i]['type'] = '工单支出';
				$data[$i]['order_price'] = "-".$data[$i]['order_price'];
			}
			if($data[$i]['type'] == 5 )  {
				$data[$i]['type'] = '用户充值';
				$data[$i]['order_price'] = "+".$data[$i]['order_price'];
			}
			if($data[$i]['type'] == 4 )  {

				$data[$i]['type'] = '购买单量';
				$data[$i]['order_price'] = "-".$data[$i]['order_price'];
			}

		}
		$this->assign('list',$data);
		if(!$submit){
			$this->display('index');
		}else{
			$name = '流水日志';
			$this->Excel($data,$name);
			$this->display('index');
		}
	}
	public function Excel($data,$name){
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
			            ->setCellValue('A'.$num, $v['create_time'])
                        ->setCellValue('B'.$num, $v['liushuihao'])
                        ->setCellValue('C'.$num, $v['user_id'])
						->setCellValue('D'.$num, $v['type'])
						->setCellValue('E'.$num, $v['order_price']);
			}
		$objPHPExcel->getActiveSheet()->setTitle('流水日志');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$name.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}