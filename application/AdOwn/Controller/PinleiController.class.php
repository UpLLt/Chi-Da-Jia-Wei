<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 17:01
 */
namespace AdOwn\Controller;
use AdOwn\Model\LinksModel;
use Common\Controller\AdminbaseController;
class PinleiController extends AdminbaseController{

      protected $pro_pinlei_model;

      public function __construct()
      {
          parent::__construct();
          $this->pro_pinlei_model = D('pro_pinlei');
      }

      public function index(){
           $list = $this->pro_pinlei_model->order('create_time desc')->select();
           $this->assign('pinlei',$list);
           $this->display('index');
      }

      public function edit_list(){
          $list = $this->pro_pinlei_model->where(array('id'=>I('id')))->find();
          $this->assign('list',$list);
          $this->display('edit');
      }

      public function edit(){
          if($this->pro_pinlei_model->create()){
              $result = $this->pro_pinlei_model->where(array('id'=>I('id')))->save();
              if($result){
                  $this->success('成功',U('Pinlei/index'));
              }else{
                  $this->error('失败');
              }
          }else{
              $this->error($this->pro_pinlei_model->getError());
          }
      }
}