<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/17
 * Time: 10:04
 */
namespace Lianbao_PC\Controller;
use Think\Controller;
class STakingOrderController extends ServiceBaseController {
    protected $user_service_model;
    protected $id;
    protected $order_user_model;
    protected $order_pro_model;
    protected $order_repair_model;
    protected $order_model;
	
	public function __construct(){
		parent::__construct();
		$this->initialize();
	}
    public function initialize() {
        $this->order_pro_model    =  D('order_pro');
        $this->user_service_model =  D('user_service');
        $this->order_user_model   =  D('order_user');
        $this->order_repair_model =  D('order_repair');
        $this->order_model        =  D('order');
        $this->id                 =  session('user_id');
    }
    public function index(){
        $this->taking_order();
        $this->display();
    }
   /*
    *获取待接单订单信息
    */
    private function taking_order(){
        $proportion = D('wallet_admin')->field('proportion')->find();
        $map_user['user_id']   = $this->id;
        $map['user_id']        = $this->id;



        $shop_location  = $this->user_service_model
            ->where($map_user)
            ->field('shop_location')
            ->find();

        $location = explode(",",$shop_location['shop_location']);

        for($i=0;$i<count($location);$i++){
            $loca['user_city'][] = array('eq',$location[$i]);
        }
            $loca['user_city'][] = 'or';

        $loca['status']         = 1;


        $count        = $this->order_user_model
            ->where($loca)
            ->count();
        $Page       = new \Think\Page($count,10);
        $show       = $Page->show();
        $taking_order = $this->order_user_model
            ->order('create_time DESC')
            ->where($loca)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();







        for($i=0;$i<count($taking_order);$i++){
            $where['order_number']  = $taking_order[$i]['order_number'];
            $repair_price = $this->order_model->where($where)->field('service_price,parts_price,logistics_price,far_price')->find();
            $pro_name = $this->order_pro_model->where($where)->field('pro_name')->find();
            $taking_order[$i]['pro_name']      =  $pro_name['pro_name'];
            $taking_order[$i]['repair_price']  = $repair_price['service_price']*(1-$proportion['proportion']) + $repair_price['parts_price'] + $repair_price['logistics_price'] + $repair_price['far_price'];
            $taking_order[$i]['repair_price']  = sprintf("%.2f", $taking_order[$i]['repair_price']);
        }
        $this->assign('page',$show);
        $this->assign('taking_order',$taking_order);
    }
    /*
    *查看详情
    */
     public function detail(){
         $proportion = D('wallet_admin')->field('proportion')->find();
         $map['order_number']= I('get.order_number');
         $detail = $this->order_user_model->where($map)->find();
         $time   = $detail['create_time'];
         $time   = time_difference($time);
         $detail['hours'] = $time['hours'];
         $detail['mins']  = $time['mins'];
         $order_pro =   $this->order_pro_model
                        ->where($map)
                        ->field('pro_xinhao,pro_property,order_type,remarks,pro_name')
                        ->find();
         $detail['pro_xinhao']   = $order_pro['pro_xinhao'];
         $detail['pro_property'] = $order_pro['pro_property'];
         $detail['remarks']      = $order_pro['remarks'];
         if($order_pro['order_type']==1) $detail['baoxiu_type'] = "安装单";
         if($order_pro['order_type']==2) $detail['baoxiu_type'] = "维修单";
         if($order_pro['order_type']==3) $detail['baoxiu_type'] = "送修单";
         if($order_pro['order_type']==4) $detail['baoxiu_type'] = "清洗单";
         $repair_price = $this->order_model->where($map)->field('service_price,parts_price,logistics_price,far_price')->find();
         $detail['repair_price'] = $repair_price['service_price']*(1-$proportion['proportion']) + $repair_price['parts_price'] + $repair_price['logistics_price'] + $repair_price['far_price'];
         $detail['repair_price']  = sprintf("%.2f", $detail['repair_price'] );
         $this->assign('detail',$detail);
         $this->display('detail');
     }

    public function taking(){
        $order_number = I('post.order_number');
        $add['service_id'] =  $this->id;
        $add['order_number'] = $order_number;
        $add['taking_time']  = time();
        $is_exi = D('order_repair')->where(array('order_number'=>$order_number))->field('id')->find();
        if($is_exi['id']){
            $this->order_repair_model->where(array('order_number'=>$order_number))->save($add);
        }else{
            $z_repair = $this->order_repair_model->add($add);
        }

        $map['order_number'] =  $order_number;
        $save['status']      =  10;
        $save['repair_service_id'] = $this->id;
        $save['app_danchu']  =  1;
        $save_user['status'] =  10;
        $save_user['repair_service_id'] = $this->id;;
        $z_user = $this->order_user_model->where($map)->save($save_user);
        $z_order =$this->order_model->where($map)->save($save);
        $user_id = session('user_id');
        $user_shop = D('user_service')->where("user_id=".$user_id)->field('company,rel_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time']  = time();
        $track['content']      = $user_shop['company']."接单";
        $track['person']       = $user_shop['rel_name'];
        $tra = D('order_track')->add($track);
        if(!empty($z_repair) &&  $z_user &&  $z_order){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }

}