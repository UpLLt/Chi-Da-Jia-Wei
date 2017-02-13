<?php
namespace Lianbao_PC\Controller;
use Think\Controller;
class DetailController extends IndexBaseController {
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
	public function add_detail_list(){
		$user_id = session('user_id');
		$this->assign('user_id',$user_id);
		$this->display('add_detail');
	}

	public function add_detail(){

		$is_exis = D('user_shop')->where(array('user_id'=>session('user_id')))->field('id')->find();
		if($is_exis['id']){
			$this->error('该数据已存在，此处不允许添加');
		}
		if(empty($_FILES['business_license']['name'])){
			$this->error('请添加营业执照');
		}

		if($_FILES['business_license']['type'] == "image/jpeg" || $_FILES['business_license']['type'] == "image/jpg" || $_FILES['business_license']['type'] == "image/png" || $_FILES['business_license']['type'] == "image/gif"){
			if(D('user_shop')->create()){
				$result = D('user_shop')->add();
				if($result){
					$this->upload_model();
					$this->success('添加成功',U('Lianbao_PC/Userindex/userindex'));
				}else{
					$this->error('添加失败');
				}
			}else{
				exit(D('user_shop')->getError());
			}
		}else{
			$this->error('请传入正确的图片格式');
		}



	}


	public function upload_model()
	{
		$upload = new \Think\Upload();
		$upload->maxSize = 3145728;
		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');
		$upload->rootPath = './data/upload/';
		$upload->savePath = '';
		$info = $upload->upload();

		$save = '/data/upload/'.$info['business_license']["savepath"].$info['business_license']["savename"];
		$save = $this->geturl($save);
		$map['user_id'] = session('user_id');
		$data['business_license']= $save;
		$is_exis = D('user_shop')->where($map)->field('id')->find();
		if($is_exis['id']){
			$success = D('user_shop')
				->where($map)
				->save($data);
		}else{
			$add['user_id'] = session('user_id');
			$add['business_license'] = $save;
			$success = D('user_shop')->add($add);
		}
		if(!$success) {
			exit('请上传营业执照');
		}
	}
}