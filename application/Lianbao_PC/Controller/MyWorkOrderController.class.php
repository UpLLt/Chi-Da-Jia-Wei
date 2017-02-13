<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class MyWorkOrderController extends BaseController{
	public function index(){
		$this->display('index');		
	}
	//发单产品首页
	/**
	 *
     */
	public function product_index(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id    = session('user_id');
		$status     = '';
		$biao       = 'product';
		$key_cond   = 'product';
		$data       = A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);


		for($i=0;$i<count($data);$i++){
			if(empty($data[$i]['product_price'])){
				if(empty($data[$i]['install_price'])){
					$data[$i]['product_price'] = $data[$i]['pro_price_send'];
				}else{
					$data[$i]['product_price'] =  $data[$i]['install_price'];
				}
			}
		}
		$this->assign('list',$data);
		$this->display('product');	
	}
	//品牌列表
	public function brand_list_index(){
		$start_time = I('post.start_time');
		$end_time   = I('post.end_time');
		$key        = I('post.filter');
		$user_id   = session('user_id');
		$status     = '';
		$biao       = 'pro_brand';
		$key_cond   = 'brand';
		$data =A('Userindex')->select($biao,$user_id,$start_time,$end_time,$key,$key_cond,$status);
		for($i=0;$i<count($data);$i++){
			if($data[$i]['brand_status']==1){
				$data[$i]['brand_status']="正常";
			}else{
				$data[$i]['brand_status']="冻结";
			}
		}

		$this->assign('list',$data);
		$this->display('brand_list');	
	}

	//增加品牌页面
	public function add_brand_index(){
		$this->display('add_brand');
	}
	//增加产品页面
	public function add_product_index(){
		$user_id = session('user_id');	
		//查询用户产品数据
		$sql = M('pro_brand')->field("brand")->where("user_id='{$user_id}'")->select();
		$a = array(
			'brand'=>'请选择',
		);
		$b = array(
			'pro_pinlei'=>'请选择',
		);
		$sql[]=$a;		
		krsort($sql);	
		//查询价格中的品类信息	
		$type = M('pro_price')->field('pro_pinlei')->select();	
		foreach ($type as $v){
			$v = implode(",",$v);              
			$temp[] = $v;
		}	
		$temp = array_unique($temp);
		rsort($temp);
		for($i=0;$i<count($temp);$i++){		
			$abc[$i]['pro_pinlei'] = $temp[$i];
		}
		$i = count($temp);
		$abc[$i] = $b;
		krsort($abc);	
		$this->assign('brand',$sql);
		$this->assign('type',$abc);
		$this->display('add_product');	
	}
	//品牌增加功能的实现模块
	public function add_brand(){
	 	$brand = I('post.brand');
		$user_id  =  session('user_id');
		$brand_num = rand_num(6);
		$time      = time();
		$status    = '1';
		if($user_id){
			$data['user_id']        = $user_id;
			$data['brand']          = $brand;
			$data['create_time']    = $time;
			$data['brand_number']   = $brand_num;
			$data['brand_status']   = $status;
			$sql = M('pro_brand')->add($data);
			if($sql){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->redirect('Login/index','',1, '未登陆，请您登陆');
		} 
	}

	//产品增加功能的实现模块
	public function add_product(){
		$user_id   = session('user_id');
		$time       = time();
		if($user_id){
		$perpoty_one   = I('post.perpoty_one');
		$perpoty_two   = I('post.perpoty_two');
		$perpoty_three = I('post.perpoty_three');
		$perpoty_four  = I('post.perpoty_four');
		$brand         = I('post.band_id');
		$pro_pinlei	   = I('post.pinlei');
		$name          = I('post.name');
		$product       = I('post.product');
		if(!empty($perpoty_four)){
			$perpoty_name = $perpoty_one.",".$perpoty_two.",".$perpoty_four.",".$perpoty_three;
		}
		if(!empty($perpoty_three) && empty($perpoty_four)){
			$perpoty_name = $perpoty_one.",".$perpoty_two.",".$perpoty_three;				
		}
		if(!empty($perpoty_two) && empty($perpoty_four) && empty($perpoty_three)){
			$perpoty_name = $perpoty_one.",".$perpoty_two;				
		}
		if(!empty($perpoty_one) && empty($perpoty_four) && empty($perpoty_three) && empty($perpoty_two) ){
			$perpoty_name = $perpoty_one;
	}
	    $map['pro_pinlei']   = $pro_pinlei;
		$map['product']      = $product;
		$map['pro_property'] = $perpoty_name;
		$map['order_type']   = '安装';
	    $test_product = M('pro_price_detail')->where($map)->find();
		$map_re['pro_pinlei']   = $pro_pinlei;
		$map_re['product']      = $product;
		$map_re['pro_property'] = $perpoty_name;
		$map_re['order_type']   = '维修';
		$product_pri            = M('pro_price_detail')->where($map_re)->max('pro_price');
		$map_rc['pro_pinlei']   = $pro_pinlei;
		$map_rc['product']      = $product;
		$map_rc['pro_property'] = $perpoty_name;
		$map_rc['order_type']   = '送修';
		$product_pri_send       = M('pro_price_detail')->where($map_rc)->max('pro_price');

			$data['product_price']     =    $product_pri;
			$data['pro_price_send']    =    $product_pri_send;
			$data['install_price']     =	$test_product['pro_price'];
			$data['install_price_wai'] =    $test_product['pro_price_wai'];
			$data['user_id']           =	$user_id;
			$data['create_time']       =	time();		
			$data['product']           =	$name;
			$data['product_brand']     =    $brand;
			$data['product_number']    =    rand_num(6);
			$data['product_brand']     =    $brand;
			$data['status']            =    1;
			$data['pro_product']       =    $product;
			$data['pro_price_id']	   =    $test_product['id'];
			$data['product_service']   =    "未开通服务";	
			$data['property']          =    $perpoty_name;
			$data['product_name']      =    $pro_pinlei;

			$sql = M('product')->add($data);
			if($sql){
				$this->ajaxReturn(2);	
			}			

			$this->ajaxReturn(1);	
		

		
		}else{			
			$this->redirect('Login/index','',1, '未登陆，请您登陆');				
		}
		
	}
	public function product_del(){
		$user_id = session('user_id');
		$id = I('get.id');
		$del = M('product')->where("id='{$id}'")->delete();
		if($del){
			$this->success('删除成功','','/MyWorkOrder/product_index',1);			
		}else{
			$this->error('删除失败');
		}
	}
	public function brand_delete(){
		$id = I('get.id');
		$map['brand_number'] = $id ;
		$del = M('pro_brand')->where($map)->delete();
		if($del){
			$this->success('删除成功','','/MyWorkOrder/brand_list_index',1);			
		}else{
			$this->error('删除失败');
		}
	}
	public function select_price(){
		$pinlei   = I('post.pinlei');
		$product  = M('pro_price')->field("product")->where("pro_pinlei='{$pinlei}'")->select();
		$c = array(
			'product'=>'请选择',
		);
		foreach ($product as $v){
			$v = implode(",",$v);              
			$temp[] = $v;
		}	
		$temp = array_unique($temp);
		rsort($temp);
		for($i=0;$i<count($temp);$i++){		
			$abc[$i]['product'] = $temp[$i];
		}
		$i = count($temp);
		$abc[$i] = $c;
		krsort($abc);	
		foreach ($abc as $e){
		$test[] = $e['product']; 
		}
		json_encode($test);
		$this->ajaxReturn($test);	
	}
	public function product_price(){
		$pinlei            = I('post.pinlei');
		$product           = I('post.product');
		$map['pro_pinlei'] = $pinlei;
		$map['product']    = $product;
		$value_one   = M('pro_price')->field('property_name_one,property_one')->where($map)->find();
		$value_two   = M('pro_price')->field('property_name_two,property_two')->where($map)->find();
		$value_three = M('pro_price')->field('property_name_three,property_three')->where($map)->find();
		$value_four  = M('pro_price')->field('property_name_four,property_four')->where($map)->find();
		if($value_two['property_two']!==null){
			$value_one['property_two']      = $value_two['property_two'];
			$value_one['property_name_two'] = $value_two['property_name_two'];
		}if($value_three['property_three']!==null){
			$value_one['property_three']      = $value_three['property_three'];
			$value_one['property_name_three'] = $value_three['property_name_three'];
		}if($value_four['property_four']!==null){
			$value_one['property_four']      = $value_four['property_four'];
			$value_one['property_name_four'] = $value_four['property_name_four'];			
		}
		foreach( $value_one as $k=>$v){
			if( !$v )
				unset( $value_one[$k] );
		}
		json_encode($value_one);
		$this->ajaxReturn($value_one);		
	}
	public function test_product(){
		$perpoty_one   = I('post.perpoty_one');
		$perpoty_two   = I('post.perpoty_two');
		$perpoty_three = I('post.perpoty_three');
		$perpoty_four  = I('post.perpoty_four');
		$pro_pinlei	   = I('post.pinlei');
		$product       = I('post.product');
		if(!empty($perpoty_four)){
			$perpoty_name = $perpoty_one.",".$perpoty_two.",".$perpoty_three.",".$perpoty_four;			
		}
		if(!empty($perpoty_three) && empty($perpoty_four)){
			$perpoty_name = $perpoty_one.",".$perpoty_two.",".$perpoty_three;				
		}
		if(!empty($perpoty_two) && empty($perpoty_four) && empty($perpoty_three)){
			$perpoty_name = $perpoty_one.",".$perpoty_two;				
		}
		if(!empty($perpoty_one) && empty($perpoty_four) && empty($perpoty_three) && empty($perpoty_two) ){
			$perpoty_name = $perpoty_one;
	}
		$map['pro_pinlei']   = $pro_pinlei;
		$map['product']      = $product;
		$map['pro_property'] = $perpoty_name;
	    $test_product = M('pro_price_detail')->where($map)->find();
		if($test_product){
			$this->ajaxReturn(0);
		}else{
			$this->ajaxReturn(1);
		}
	}	
}