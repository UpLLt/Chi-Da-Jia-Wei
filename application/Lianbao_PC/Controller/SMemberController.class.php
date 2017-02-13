<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 9:38
 */
namespace Lianbao_PC\Controller;
use Think\Controller;
class SMemberController extends ServiceBaseController {
    protected $order_pro_model;
    protected $order_user_model;
    protected $id;
    protected $user_repairer_model;
    protected $order_model;
    protected $user_repairer_service_model;
    protected $wallet_repairer_model;
    public function __construct(){
        parent::__construct();
        $this->initialize();
    }

    private function initialize() {
        $this->order_pro_model                 = D('order_pro');
        $this->order_user_model                = D('order_user');
        $this->wallet_repairer_model           = D('wallet_repairer');
        $this->user_repairer_service_model     = D('user_repairer_service');
        $this->order_model                     = D('order');
        $this->user_repairer_model             = D('user_repairer');
        $this->id = session('user_id');
    }

    /**
     * 直属成员
     */
    public function index(){
        $map['parent_id'] = $this->id;
        $map['type']      = 1;
        $zhishu_repairer = $this->user_repairer_model->where($map)->select();

        for($i=0;$i<count($zhishu_repairer);$i++){
            $where['status'] ='7';
            $where['repair_person_id'] = $zhishu_repairer[$i]['user_id'];
            $order = $this->order_model->where($where)->count();
            $zhishu_repairer[$i]['end_order'] = $order;
            $where_2['user_id'] = $zhishu_repairer[$i]['user_id'];
            $service = $this->user_repairer_service_model->where($where_2)->field('service_pinlei')->select();
            $zhishu_repairer[$i]['service'] = $service;
        }

        $this->assign('zhishu_repairer',$zhishu_repairer);
        $this->display('member');
    }
    /**
     * 附属成员
     */
    public function fumember(){
        $map['parent_id'] = $this->id;
        $map['type']      = 2;
        $zhishu_repairer = $this->user_repairer_model->where($map)->select();

        for($i=0;$i<count($zhishu_repairer);$i++){
            $where['status'] ='7';
            $where['repair_person_id'] = $zhishu_repairer[$i]['user_id'];
            $order = $this->order_model->where($where)->count();
            $zhishu_repairer[$i]['end_order'] = $order;
            $where_2['user_id'] = $zhishu_repairer[$i]['user_id'];
            $service = $this->user_repairer_service_model->where($where_2)->field('service_pinlei')->select();
            $zhishu_repairer[$i]['service'] = $service;
        }

        $this->assign('zhishu_repairer',$zhishu_repairer);
        $this->display('fumember');

    }
    public function member_show(){
        $user_id = I('get.user_id');
        $map['repair_person_id'] =$user_id;
        $order_count =  $this->order_model->where($map)->count();
        $wallet = $this->wallet_repairer_model->where(array('user_id'=>$user_id))->field('all_money,service_coucheng')->find();
        $wallet['all_money']      = sprintf("%.2f",$wallet['all_money']);
        $wallet['service_coucheng']      = sprintf("%.2f",$wallet['service_coucheng']);



        $repairer['order_count']        = $order_count;
        $repairer['all_money']          = $wallet['all_money'];
        $repairer['service_coucheng']   = $wallet['service_coucheng'];
        $where['repair_person_id'] = $user_id;
        $count      = $this->order_model->where($where)->count();
        $Page       = new \Think\Page($count,10);
        $show       = $Page->show();
        $list       = $this->order_model->where($where)->order('create_time')->limit($Page->firstRow.','.$Page->listRows)->field('order_number')->select();


       for($i=0;$i<count($list);$i++){
            $map['order_number'] = $list[$i]['order_number'];
            $list[$i] = $this->order_user_model->where($map)->find();
            $abc = $this->order_pro_model->where($map)->field('pro_name')->find();
            $list[$i]['pro_name'] = $abc['pro_name'];


                $where['order_number']  = $list[$i]['order_number'];
                $pro_name = $this->order_pro_model->where($where)->field('pro_name')->find();
                $list[$i]['pro_name'] =  $pro_name['pro_name'];
                if( $list[$i]['status']==10) {
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/assign_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "待指派";
                }
                if( $list[$i]['status']==9){
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/order_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "待预约";
                }
                if( $list[$i]['status']==11){

                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/recipient_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "待收件";
                }
                if( $list[$i]['status']==12){
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/send_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "待寄件";
                }
                if( $list[$i]['status']==8){
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/service_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "待服务";
                }
                if( $list[$i]['status']==13){
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/gathering_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "待收款";
                }
                if( $list[$i]['status']==7){
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/end_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "已完结";
                }
                if( $list[$i]['status']==16){
                    $list[$i]['str'] =U('Lianbao_PC/SMyOrder/service_detail',array('order_number'=>$list[$i]['order_number']));
                    $list[$i]['leixin'] = "正在服务中";
                }






        }

        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->assign('repairer',$repairer);
        $this->display('member_show');
    }

    /**
     * 申请成员
     */
    public function apply(){
        $map['parent_id'] = $this->id;
        $map['type']      = array(array('eq', 3), array('eq', 4), 'or');
        $zhishu_repairer = $this->user_repairer_model->where($map)->select();
        for($i=0;$i<count($zhishu_repairer);$i++){
            $where_2['user_id'] = $zhishu_repairer[$i]['user_id'];
            $service = $this->user_repairer_service_model->where($where_2)->field('service_pinlei')->select();
            $zhishu_repairer[$i]['service'] = $service;

        }
        $this->assign('zhishu_repairer',$zhishu_repairer);
        $this->display('apply');
    }

    /**
     * 同意申请
     */
    public function agree_apply(){
        $map['user_id'] = I('post.user_id');
        $type = $this->user_repairer_model->where($map)->field('type')->find();
        if($type['type'] == 3 ){
            $result = $this->user_repairer_model->where($map)->setField('type','1');
        }
        if($type['type'] == 4 ){
            $save['type'] = 2;
            $save['proportion'] ='0.05';
            $result = $this->user_repairer_model->where($map)->setField($save);

        }

        if($result){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }

    /**
     * 驳回申请
     */

    public function not_agree_apply(){
        $map['user_id']    = I('post.user_id');
        $save['parent_id'] = '';
        $save['type']      = '';
        $result = $this->user_repairer_model->where($map)->save($save);
        if($result){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    /**
     * 申请详情查看编辑页面
     */

    public function apply_show(){
        $map['user_id'] = I('get.user_id');
        $repairer = $this->user_repairer_model->where($map)->find();
        $wufu  = $this->user_repairer_service_model->where($map)->field('service_address,service_pinlei')->select();

        for($i=0;$i<count($wufu);$i++){
            $wufu[$i]['number'] =$i+1;
            $wufu[$i]['service_address'] = explode(',',$wufu[$i]['service_address']);
        }
        if($repairer['type']==1)$repairer['type']='直属成员';
        if($repairer['type']==2)$repairer['type']='附属成员';
        if($repairer['type']==3)$repairer['type']='申请成为直属成员';
        if($repairer['type']==4)$repairer['type']='申请成为附属成员';
        $repairer['service'] = $wufu;
        $repairer['id_card'] = id_card($repairer['id_card']);
        $this->assign('repairer',$repairer);
        $this->display('apply_show');

    }

    /**
     * 编辑页面
     */
    public function affiliate_show(){
        $map['user_id'] = I('get.user_id');
        $repairer = $this->user_repairer_model->where($map)->find();
        $wufu  = $this->user_repairer_service_model->where($map)->field('service_address,service_pinlei')->select();

        for($i=0;$i<count($wufu);$i++){
           $wufu[$i]['number'] =$i+1;
           $wufu[$i]['service_address'] = explode(',',$wufu[$i]['service_address']);
        }
        $repairer['member_status'] = $repairer['type'];
        if($repairer['type']==1)$repairer['type']='直属成员';
        if($repairer['type']==2)$repairer['type']='附属成员';
        if($repairer['type']==3)$repairer['type']='申请成为直属成员';
        if($repairer['type']==4)$repairer['type']='申请成为附属成员';
        $repairer['service'] = $wufu;
        $repairer['id_card'] = id_card($repairer['id_card']);
        $this->assign('repairer',$repairer);
        $this->display('affiliate_show');

    }
    /**
     * 解除关系
     */
    public function jiechuguanxi(){
        $map['user_id'] = I('post.user_id');
        $save['parent_id'] = '';
        $save['type']      = '';
        $save['proportion']= '';
        $result = $this->user_repairer_model->where($map)->save($save);
        if($result){
            $this->ajaxReturn(1);
        }
    }

    /**
     * 修改利率
     */
     public function xiugaililv(){
      $result = D('user_repairer')->where(array('user_id'=>I('repair_person_id')))->setField('proportion',I('proportion'));
      if($result){
          $this->success('修改成功');
      }else{
          $this->error('修改失败,不能够与原利率相同');
      }

     }



}