<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 17:01
 */
namespace Coupon\Controller;
use Common\Controller\AdminbaseController;
use Coupon\Model\TicketModel;

class IndexController extends AdminbaseController{

      protected $ticket_model;
      public function __construct()
      {
          parent::__construct();
          $this->ticket_model = new TicketModel();
      }

    /**
     * 首页
     */
      public function index(){
          $list = $this->ticket_model->select();
          $this->assign('list',$list);
          $this->display('index');

      }

    public function add_list(){
        $this->display('add');
    }

    public function add(){
        if($this->ticket_model->create()){
            $result = $this->ticket_model->add();
            if($result){
                $this->success('成功',U('Index/index'));
            }else{
                $this->error('失败');
            }
        }else{
            $this->error($this->ticket_model->getError());
        }
    }

    public function delete(){
        $result = $this->ticket_model->where(array('id'=>I('id')))->delete();
        if($result){
            $this->success('成功');
        }else{
            $this->error('失败');
        }

    }

    public function edit_list(){
        $list = $this->ticket_model->where(array('id'=>I('id')))->find();
        $this->assign('list',$list);
        $this->display('edit');
    }

    public function edit(){

        if($this->ticket_model->create()){
            $result = $this->ticket_model->where(array('id'=>I('id')))->save();
            if($result){
                $this->success('成功',U('Index/index'));
            }else{
                $this->error('失败');
            }
        }else{
            $this->error($this->ticket_model->getError());
        }
    }

}