<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 9:38
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ApplyServiceController extends AdminbaseController{
    protected  $user_repairer_model;
    protected  $order_model;
    protected  $user_model;
    protected  $apply_service_model;
    protected  $user_service_model;
    protected  $wallet_repair_model;
    protected  $wallet_seller_model;
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }
    private function initialize()
    {
        $this->wallet_seller_model = D('wallet_seller');
        $this->wallet_repair_model = D('wallet_repairer');
        $this->user_repairer_model = D('user_repairer');
        $this->order_model         = D('order');
        $this->user_model          = D('user');
        $this->apply_service_model = D('apply_service');
        $this->user_service_model  = D('user_service');
    }
    public function apply_service_list(){
        $map['app_ser'] = array(array('eq',1),array('eq',2),array('eq',3),array('eq',4), 'or');;
        $user_repair    = $this->user_repairer_model->order('app_time desc')->where($map)->select();
        for($i=0;$i<count($user_repair);$i++){
            if($user_repair[$i]['app_ser']==1) $user_repair[$i]['status'] = '审核中';
            if($user_repair[$i]['app_ser']==2) $user_repair[$i]['status'] = '申请成功，提交资料';
            if($user_repair[$i]['app_ser']==3) $user_repair[$i]['status'] = '拒绝申请';
            if($user_repair[$i]['app_ser']==4) $user_repair[$i]['status'] = '资料提交成功';
        }

        $this->assign('list',$user_repair);
        $this->display('index');
    }

    public function detail(){

        $map['user_id'] = I('user_id');
        $user_repair    = $this->user_repairer_model->where($map)->find();
        if($user_repair['app_ser']==1) $user_repair['status'] = '审核中';
        if($user_repair['app_ser']==2) $user_repair['status'] = '申请成功，提交资料';
        if($user_repair['app_ser']==3) $user_repair['status'] = '拒绝申请';
        if($user_repair['app_ser']==4) $user_repair['status'] = '资料提交成功';
		$data = D('apply_service')->where(array('repair_id'=>I('user_id')))->find();
   
		$this->assign('data',$data);
        $this->assign('list',$user_repair);
        $this->display('detail');
    }

    public function agree(){
        $is_exist =  $this->user_repairer_model->where(array('user_id'=>I('user_id'),'app_ser'=>1))->find();
        if(!$is_exist['id']){
            $this->error('当前申请未处于待申请状态，不能点击同意');
        }
        $map['user_id']        = I('user_id');
        $save['app_ser']       = '2';
        $save['app_deal_time'] = time();
        $result = $this->user_repairer_model->where($map)->save($save);
        if($result){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    public function disagree(){
        $is_exist =  $this->user_repairer_model->where(array('user_id'=>I('user_id'),'app_ser'=>1))->find();
        if(!$is_exist['id']){
            $this->error('当前申请未处于待申请状态，不能点击同意');
        }
        $map['user_id']        = I('user_id');
        $save['app_ser']       = '3';
        $save['app_deal_time'] = time();
        $result = $this->user_repairer_model->where($map)->save($save);
        if($result){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    public function change_service(){
        $user_id = I('user_id');
        $is_two = $this->user_repairer_model->where(array('user_id'=>$user_id))->field('app_ser')->find();
        if($is_two['app_ser']== 1 || $is_two['app_ser']== 2 || $is_two['app_ser']== 3){
            $this->error('请在客户提供资料后转换');
        }
        $wallet_repair = $this->wallet_repair_model->where(array('user_id'=>$user_id))->find();
        if($wallet_repair['tixian_ing']){
            $this->error('转换失败，用户当前还存在体现金额，再转换');
        }
        $map_order['status'] = array(array('eq',8),array('eq',16),array('eq',9),array('eq',11),array('eq',12),array('eq',13),'or');
        $map_order['repair_person_id'] = $user_id;
        $result = $this->order_model->where($map_order)->field('id')->find();
        if($result['id']){
            $this->error('转换失败，请提醒用户完成所有工单后，再转换');
        }

        $user_repairer = $this->user_repairer_model->where(array('user_id'=>$user_id))->find();
        $apply_service = $this->apply_service_model->where(array('user_id'=>$user_id))->find();
        if(empty($user_repairer['qq']) || empty($user_repairer['phone']) ){
            $this->error('转换失败，请补全资料（QQ，收货地址，电话号码），再转换');
        }

        $password = $this->user_model->where(array('id'=>$user_id))->field('password')->find();
        $pass_ma['taking_password'] = $password['password'];
        $pass_ma['user_type'] = 3;
        $order = $this->user_model->where(array('id'=>$user_id))->save($pass_ma);



        $add['user_id']  =  $user_id;
        $add['rel_name'] =  $apply_service['faren_name'];
        $add['company']  =  $apply_service['company'];
        $add['id_card']  =  $user_repairer['id_card'];
        $add['phone']    =  $user_repairer['phone'];
        $add['email']    =  $apply_service['email'];
        $add['com_address']       =  $user_repairer['address'];
        $add['qq']                =  $user_repairer['qq'];
        $add['business_license']  =  $apply_service['business_license'];
        $add['tax_regis']         =  $apply_service['tax_regis'];
        $add['special_regis']     =  $apply_service['special_regis'];
        $add['shop_location']     =  $user_repairer['city'];
        $add['skill']             =  $user_repairer['skill'];
        $add['create_time']       =  time();

        $result = $this->user_service_model->add($add);

        $wallet_add['user_id']    = $user_id;
        $wallet_add['balance']    = $wallet_repair['balance'];
        $wallet_add['bond']       = $wallet_repair['bond_money'];
        $re     = $this->wallet_seller_model->add($wallet_add);

        if($result && $re){
            $this->wallet_repair_model->where(array('user_id'=>$user_id))->delete();
            $this->user_repairer_model->where(array('user_id'=>$user_id))->delete();
            $this->success('转换成功');
        }else{
            $this->error('转换失败');
        }
    }

    public function search(){
        $real_name = I('real_name');
        $user_repair = $this->user_repairer_model->where(array('real_name'=>$real_name))->select();
        for($i=0;$i<count($user_repair);$i++){
            if($user_repair[$i]['app_ser']==1) $user_repair[$i]['status'] = '审核中';
            if($user_repair[$i]['app_ser']==2) $user_repair[$i]['status'] = '申请成功，提交资料';
            if($user_repair[$i]['app_ser']==3) $user_repair[$i]['status'] = '拒绝申请';
        }
        $this->assign('list',$user_repair);
        $this->display('index');
    }

}