<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/5
 * Time: 9:13
 */
namespace Repair\Controller;

use Think\Controller;

class MyOrderController extends BaseController
{
    protected $order_model;
    protected $order_user_model;
    protected $order_pro_model;
    protected $user_repairer_model;
    protected $user_service_model;
    protected $pro_parts_model;
    protected $order_repair_model;
    protected $shot_message_model;
    protected $order_add_service_model;
    protected $pro_price_detail_model;
    protected $pro_parts_price_model;
    protected $pro_price_service_model;
    protected $user_rec_address_model;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    private function initialize()
    {
        $this->user_rec_address_model  = D('user_rec_address');
        $this->pro_price_service_model = D('pro_price_service');
        $this->pro_parts_price_model = D('pro_parts_price');
        $this->order_add_service_model = D('order_add_service');
        $this->order_model = D('order');
        $this->order_user_model = D('order_user');
        $this->order_pro_model = D('order_pro');
        $this->user_repairer_model = D('user_repairer');
        $this->user_service_model = D('user_service');
        $this->pro_parts_model = D('pro_parts');
        $this->order_repair_model = D('order_repair');
        $this->shot_message_model = D('shot_message');
        $this->pro_price_detail_model = D('pro_price_detail');
    }

    /**
     *我的工单
     */
    public function order()
    {
        $status = I('post.status');
        $user_id = I('post.user_id');
        $this->checkparam(array($status, $user_id));
        $this->myorder($status, $user_id);
    }

    /**
     * 客户投诉单
     */
    public function complaint()
    {
        $user_id = I('post.user_id');
        $map['repair_person_id'] = $user_id;
        $number = D('complaint')->where($map)->field('order_number')->select();
        for ($j = 0; $j < count($number); $j++) {
            $map_number['order_number'] = $number[$j]['order_number'];
            $z = $this->order_model->where($map_number)->field('repair_person_id,order_type,order_number')->order('create_time desc')->find();
            if (!empty($z)) {
                $order_number[$j] = $z;
            }
        }
        sort($order_number);

        for ($i = 0; $i < count($order_number); $i++) {


            $map_repair['user_id'] = $order_number[$i]['repair_person_id'];
            $repair_type = $this->user_repairer_model->where($map_repair)->field('type')->find();
            $data[$i]['repair_type'] = empty($repair_type['type']) ? '' : $repair_type['type'];
            $where['order_number'] = $order_number[$i]['order_number'];
            $user_address = $this->order_user_model->field('user_address,user_phone,status')->where($where)->find();
            $pro_name = $this->order_pro_model->field('pro_product,pro_property')->where($where)->find();
            $data[$i]['user_phone'] = $user_address['user_phone'];
            $data[$i]['pro_property'] = $pro_name['pro_property'];
            $data[$i]['order_number'] = $order_number[$i]['order_number'];
            $data[$i]['user_address'] = $user_address['user_address'];
            $data[$i]['pro_product'] = $pro_name['pro_product'];
            $data[$i]['order_count'] = 1;
            if ($order_number[$i]['order_type'] == 1) $data[$i]['order_type'] = '安装';
            if ($order_number[$i]['order_type'] == 2) $data[$i]['order_type'] = '维修';
            if ($order_number[$i]['order_type'] == 3) $data[$i]['order_type'] = '送修';
            if ($order_number[$i]['order_type'] == 4) $data[$i]['order_type'] = '清洗';
            $data[$i]['status'] = "被投诉";
            $data = empty($data) ? '' : $data;

        }
        exit($this->returnApiSuccess($data));
    }

    /**
     * 接单区
     */
    public function taking_order()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->checkparam(array($user_id, $token));
        $this->checkparam($user_id, $token);
        $user_map['user_id'] = $user_id;


        $city = $this->user_repairer_model
            ->where($user_map)
            ->field('city')
            ->find();



        $map_shop['shop_location'] = array('like',array('%'.$city['city'].'%'));

        $service = $this->user_service_model
                   ->where($map_shop)
                   ->field('shop_location')
                   ->find();



        if ( $service['shop_location'] ) {
            $map['repair_person_id'] = $user_id;
            $map['status'] = 9;
            $order_number = $this->order_model->where($map)->field('order_number')->order('create_time desc')->select();
            for ($i = 0; $i < count($order_number); $i++) {
                $where['order_number'] = $order_number[$i]['order_number'];
                $user_address = $this->order_user_model->field('user_address')->where($where)->find();
                $pro_name = $this->order_pro_model->field('order_type,pro_product,pro_property')->where($where)->find();

                if($order_number[$i]['order_number'])     $data[$i]['order_number'] = $order_number[$i]['order_number'];
                if($user_address['user_address'])         $data[$i]['user_address'] = $user_address['user_address'];
                if($pro_name['pro_product'])              $data[$i]['pro_product']  =  $pro_name['pro_product'];
                if($pro_name['pro_property'])         $data[$i]['property'] = $pro_name['pro_property'];
                if($order_number[$i]['order_number']) $data[$i]['order_number'] = $order_number[$i]['order_number'];
                if($order_number[$i]['order_number']) $data[$i]['count'] = 1;
                if($order_number[$i]['order_number']) $data[$i]['type']  = 1;
                if($pro_name['order_type'] == 1) $data[$i]['order_type']  = "安装";
                if($pro_name['order_type'] == 2) $data[$i]['order_type']  = "维修";
                if($pro_name['order_type'] == 3) $data[$i]['order_type']  = "送修";
                if($pro_name['order_type'] == 4) $data[$i]['order_type']  = "清洗";
            }

        } else {

            $is_exist = D('user')->where(array('id' => I('post.user_id')))->field('examine_status')->find();
            if ($is_exist['examine_status'] == 1) {
                exit($this->returnApiError(BaseController::FATAL_ERROR, "该用户尚未通过审核，不能接单"));
            }

            $map_user['user_city'] = $city['city'];
            $map_user['status'] = 1;


            $order_number = $this->order_user_model
                ->where($map_user)
                ->field('order_number')
                ->order('create_time')
                ->select();
            $order_number['status'] = 2;
            for ($i = 0; $i < count($order_number); $i++) {
                $where['order_number'] = $order_number[$i]['order_number'];
                $user_address = $this->order_user_model->field('user_address')->where($where)->find();
                $pro_name = $this->order_pro_model->field('order_type,pro_product,pro_property')->where($where)->find();
                if($user_address['user_address'])  $data[$i]['user_address'] = $user_address['user_address'];
                if($pro_name['pro_product']) $data[$i]['pro_product'] = $pro_name['pro_product'];
                if($pro_name['pro_property']) $data[$i]['property'] = $pro_name['pro_property'];
                if($order_number[$i]['order_number']) $data[$i]['order_number'] = $order_number[$i]['order_number'];
                if( $data[$i]['order_number'] )    $data[$i]['type'] = 2;
                if( $data[$i]['order_number'] )    $data[$i]['count'] = 1;
                if($pro_name['order_type'] == 1) $data[$i]['order_type']  = "安装";
                if($pro_name['order_type'] == 2) $data[$i]['order_type']  = "维修";
                if($pro_name['order_type'] == 3) $data[$i]['order_type']  = "送修";
                if($pro_name['order_type'] == 4) $data[$i]['order_type']  = "清洗";

            }
        }


            exit($this->returnApiSuccess($data));

    }

    /**
     * 接单
     */
    public function click_taking_order()
    {
        $order_number = I('post.order_number');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $map['order_number'] = $order_number;
        $this->checkparam(array($order_number, $user_id));
        $this->checktoken($user_id, $token);
        $is_have = $this->order_user_model->where(array('order_number'=>$order_number))->field('status')->find();
        if($is_have['status'] == 9 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR, "已接单，请勿重复接取"));
        }

        $is_exist = D('user')->where(array('id' => I('post.user_id')))->field('examine_status')->find();
        if ($is_exist['examine_status'] == 1) {
            exit($this->returnApiError(BaseController::FATAL_ERROR, "该用户尚未通过审核，不能接单"));
        }

        $is_wai =  D('order_pro')->where(array('order_number'=>$order_number))->field('baoxiu_type')->find();
        $wallet_balance = D('wallet_repairer')->where(array('user_id'=>$user_id))->field('balance')->find();
        if($wallet_balance['balance'] < 0 && $is_wai['baoxiu_type'] == 2){
            exit($this->returnApiError(BaseController::FATAL_ERROR, "该用户余额为负数，不能接取保外单"));
        }
        $res_user = $this->order_user_model
            ->where($map)
            ->setField('status', 9);
        $save['status'] = 9;
        $save['repair_person_id'] = $user_id;
        $res_order = $this->order_model
            ->where($map)
            ->save($save);
        $add_repair['order_number'] =  $order_number;
        $add_repair['taking_time']  =  time();
        $add_repair['assign_time']  =  time();
        $add_repair['assign_end_time'] = strtotime("+1 day");
            $this->order_repair_model->add($add_repair);
        $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time'] = time();
        $track['content'] = $user_shop['real_name'] . "接单";
        $track['person'] = $user_shop['real_name'];
        $tra = D('order_track')->add($track);
        $add_re['order_number'] = $order_number;
        $add_re['taking_time'] = time();
        D('order_repair')->add($add_re);
        if ($res_order && $res_user) {
            exit($this->returnApiSuccess());
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '接单失败'));
        }

    }


    /**
     * 模版文件
     * @param $status
     * @param $user_id
     */
    public function myorder($status, $user_id)
    {
        if ($status == 8) {
            $map['status'] = array(array('eq', 8), array('eq', 16), 'or');
        }
        else if ($status == 13){
            $map['status'] = array(array('eq', 13), array('eq', 20), 'or');
        }
        else {
            $map['status'] = $status;
        }
        $this->checkparam(array($user_id));
        $map['repair_person_id'] = $user_id;
        $order_number = $this->order_model->where($map)->field('source,repair_person_id,order_type,order_number')->order('create_time desc')->select();


        for ($i = 0; $i < count($order_number); $i++) {
            $map_repair['user_id'] = $order_number[$i]['repair_person_id'];
            $repair_type = $this->user_repairer_model->where($map_repair)->field('type')->find();
            $data[$i]['repair_type'] = empty($repair_type['type']) ? '' : $repair_type['type'];
            $where['order_number'] = $order_number[$i]['order_number'];
            $user_address = $this->order_user_model->field('user_address,user_phone,status')->where($where)->find();
            $pro_name = $this->order_pro_model->field('pro_product,pro_property')->where($where)->find();
            $data[$i]['user_phone'] = $user_address['user_phone'];
            $data[$i]['pro_property'] = $pro_name['pro_property'];
            $data[$i]['order_number'] = $order_number[$i]['order_number'];
            $data[$i]['user_address'] = $user_address['user_address'];
            $data[$i]['pro_product'] = $pro_name['pro_product'];
            $data[$i]['order_count'] = 1;
            $data[$i]['source']      = empty( $order_number[$i]['source'] ) ? '' : $order_number[$i]['source'] ;
            $data[$i]['source_name'] = empty( $order_number[$i]['source'] ) ? '商家用户订单' : "个人用户订单" ;
            if ($order_number[$i]['order_type'] == 1) $data[$i]['order_type'] = '安装';
            if ($order_number[$i]['order_type'] == 2) $data[$i]['order_type'] = '维修';
            if ($order_number[$i]['order_type'] == 3) $data[$i]['order_type'] = '送修';
            if ($order_number[$i]['order_type'] == 4) $data[$i]['order_type'] = '清洗';
            if ($user_address['status'] == 8) $data[$i]['status'] = '待服务';
            if ($user_address['status'] == 11) $data[$i]['status'] = '待收件';
            if ($user_address['status'] == 12) $data[$i]['status'] = '待寄件';
            if ($user_address['status'] == 13) $data[$i]['status'] = '待收款';
            if ($user_address['status'] == 15) $data[$i]['status'] = '投诉';
            if ($user_address['status'] == 7) $data[$i]['status'] = '已完结';
            if ($user_address['status'] == 16) $data[$i]['status'] = '服务进行中';
            if ($user_address['status'] == 20) $data[$i]['status'] = '商家待收件';
            if ($status['status'] == 9) {
                $data[$i]['status'] = '待预约';
                $map_re['order_number'] = $order_number[$i]['order_number'];
                $repair = $this->order_repair_model->where($map_re)->field('assign_end_time')->find();
                $time = time();
                $data[$i]['now_time'] = date("Y-m-d H:i:s", $time);
                $data[$i]['end_time'] = date("Y-m-d H:i:s", $repair['assign_end_time']);
                if (time() > $repair['taking_time']) {
                    $this->order_repair_model->where($map_re)->setField('yuyue_chaoshi', 1);
                }
            }
            $data = empty($data) ? '' : $data;

        }

        exit($this->returnApiSuccess($data));
    }

    /**
     * 详情页
     */
    public function detail()
    {
        $order_number = I('order_number');
        $this->checkparam(array($order_number));
        $map['order_number'] = $order_number;
        $complaint = D('complaint')->where($map)->field('complaint_problem')->find();
        $data['complaint'] = empty($complaint['complaint_problem']) ? '' : $complaint;
        $order = $this->order_model
            ->where($map)
            ->field('source,repair_person_id,status,order_type')
            ->find();
        $map_repair['user_id'] = $order['repair_person_id'];

        $repair_type = $this->user_repairer_model->where($map_repair)->field('type')->find();
        $data['repair_type'] = empty($repair_type['type']) ? '' : $repair_type['type'];
        if (empty($complaint['complaint_problem'])) {
            $data['complaint_status'] = '';
        } else {
            $data['complaint_status'] = '被投诉';
        }

        if ($order['status'] == 8) $data['status'] = '待服务';
        if ($order['status'] == 9) $data['status'] = '待预约';
        if ($order['status'] == 16) $data['status'] = '正在服务中';
        if ($order['status'] == 11) $data['status'] = '待收件';
        if ($order['status'] == 12) $data['status'] = '待寄件';
        if ($order['status'] == 13) $data['status'] = '待收款';
        if ($order['status'] == 15) $data['status'] = '投诉';
        if ($order['status'] == 7) $data['status']  = '已完结';
        if ($order['status'] == 20) $data['status'] = '商家待收件';
        if ($order['order_type'] == 1) {
            $data['order_type'] = '安装';
            $data['service'] = '整机安装';
            $data['type'] = 1;
        }
        if ($order['order_type'] == 2) {
            $data['order_type'] = '维修';
            $data['service'] = '产品维修';
            $data['type'] = 2;
        }

        if ($order['order_type'] == 3) {
            $data['order_type'] = '送修';
            $data['service'] = '客户送修';
            $data['type'] = 3;
        }

        if ($order['order_type'] == 4) {
            $data['order_type'] = '清洗';
            $data['service'] = '上门清洗';
            $data['type'] = 4;
        }

        $pro = $this->order_pro_model
            ->where($map)
            ->field('pro_product,baoxiu_type,pro_property,remarks')
            ->find();
        $data['source']            = empty( $order['source'] ) ? '' : $order['source'];
        $data['source_name']       = empty( $order['source'] ) ? '商家用户订单' : "个人用户订单" ;
        $data['order_number'] = $order_number;
        $data['remarks'] = empty($pro['remarks']) ? $pro['pro_property'] : $pro['remarks'];
        $data['product'] = $pro['pro_product'];
        $data['pro_property'] = $pro['pro_property'];
        $data['product_count'] = 1;
        if ($pro['baoxiu_type'] == 1) {
            $data['baoxiu_type'] = '保内';
            $data['pay_money'] = '联保代付';
        }
        if ($pro['baoxiu_type'] == 2) {
            $data['baoxiu_type'] = '保外';
            $data['pay_money'] = '客户自付';
        }
        $pro = $this->order_user_model
            ->where($map)
            ->field('user_name,user_phone,user_city,user_address,pro_price')
            ->find();

        $repair_price = $this->order_model
            ->where($map)
            ->field('repair_price,service_price,parts_price,logistics_price,far_price')
            ->find();
        $data['user_name'] = $pro['user_name'];
        $data['user_phone'] = $pro['user_phone'];
        $pro['user_city'] = str_replace('/', '', $pro['user_city']);
        $data['user_addresss'] = $pro['user_city'] . $pro['user_address'];

        $user_repairer = D('user_repairer')->where(array('user_id'=>$order['repair_person_id']))->field('type,proportion')->find();
        $wallet_admin  = D('wallet_admin')->field('no_ser_proportion,proportion')->find();

        //直属成员
        if($user_repairer['type'] == 1){
            $data['product_price'] = $repair_price['service_price']*(1-$wallet_admin['proportion']) + $repair_price['parts_price'] + $repair_price['logistics_price'] + $repair_price['far_price'];
            $data['product_price'] = sprintf("%.2f",  $data['product_price']);
        }
        //附属成员
        if($user_repairer['type'] == 2 ){

            $data['product_price'] = $repair_price['service_price']*(1-$wallet_admin['proportion'])*( 1 - $user_repairer['proportion']) + $repair_price['parts_price'] + $repair_price['logistics_price'] + $repair_price['far_price'];
            $data['product_price'] = sprintf("%.2f",  $data['product_price']);
        }
        //申请中成员 或者 无服务中心成员
        if($user_repairer['type'] == 3 || $user_repairer['type'] == 4 || empty($user_repairer['type'])){
            $data['product_price'] = $repair_price['service_price']*(1-$wallet_admin['no_ser_proportion']) + $repair_price['parts_price'] + $repair_price['logistics_price'] + $repair_price['far_price'];
            $data['product_price'] = sprintf("%.2f",  $data['product_price']);
        }


        $data['parts'] = $this->pro_parts_model->where($map)->order('create_time desc')->field('type,parts_name,parts_count,parts_picture,all_picture,id')->select();
        for ($i = 0; $i < count($data['parts']); $i++) {
            if ($data['parts'][$i]['parts_name'] == '') $data['parts'][$i]['parts_name'] = '';
            if ($data['parts'][$i]['parts_count'] == '') $data['parts'][$i]['parts_name'] = '';

        }
        $add_service = $this->order_add_service_model->where($map)->field('id desc')->field('add_service')->find();

        if ($add_service['add_service']) {
            $data['add_service'] = explode(',', $add_service['add_service']);
        } else {
            $data['add_service'] = array();
        }

        $repair = $this->order_repair_model->where($map)->field('gaiyue_reason,yuyue_time,taking_time,assign_time')->find();
        $data['gaiyue_status'] = empty($repair['gaiyue_reason']) ? 1 : 2;
        if ($repair['yuyue_time'] == '') $repair['yuyue_time'] = '';
        if ($repair['taking_time'] == '') {
            $data['taking_time'] = '';
        } else {
            $data['taking_time'] = date('Y-m-d H-i-s', $repair['taking_time']);
        }
        $data['yuyue_time'] = $repair['yuyue_time'];
        $data['shot_send'] = $this->shot_mess_send($order_number);
        exit($this->returnApiSuccess($data));
    }

    /**
     *开始服务
     */
    public function start_order()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $order_number = I('post.order_number');
        $this->checkparam(array($user_id, $token, $order_number));
        $this->checktoken($user_id, $token);
        $map['order_number'] = $order_number;

        $source =  $this->order_model->where($map)->field('source')->find();
        if( $source['source'] == 1 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR, '订单状态错误'));
        }
        $save['start_time'] = time();
        $where['status'] = '16';
        $this->order_repair_model->where($map)->save($save);
        $this->order_model->where($map)->save($where);
        $this->order_user_model->where($map)->save($where);

        $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time'] = time();
        $track['content'] = "维修商开始服务";
        $track['person'] = $user_shop['real_name'];
        $tra = D('order_track')->add($track);

        exit($this->returnApiSuccess());
    }

    /**
     * 工单完结码
     */
    public function end_code()
    {
        $order_number = I('post.order_number');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->checkparam(array($user_id, $token, $order_number));
        $this->checktoken($user_id, $token);


        $map['order_number'] = $order_number;

        $source =  $this->order_model->where($map)->field('source')->find();
        if( $source['source'] == 1 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR, '订单状态错误'));
        }

        $phone = $this->order_user_model->where($map)->field('user_phone')->find();
        $rand_number = rand_twelve();
        $content = "您好，您的工单完结码是" . $rand_number . ".如非本人操作，请无需理会,【驰达家维】";
        vendor("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms = new \Cxsms($options);
        $result = $Cxsms->send($phone['user_phone'], $content);
        if ($result && $result['returnsms']['returnstatus'] == 'Success') {
            $save_shot['username'] = $order_number;
            $save_shot['create_time'] = time();
            $save_shot['code'] = $rand_number;
            $save_shot['end_time'] = strtotime("+15 minute");
            $where['username'] = $order_number;
            $res = $this->shot_message_model->where($where)->field('username')->find();
            if ($res['username']) {
                $mess['create_time'] = time();
                $mess['code'] = $rand_number;
                $mess['end_time'] = strtotime("+15 minute");
                $result = $this->shot_message_model->where($where)->save($mess);
            } else {
                $result = $this->shot_message_model->add($save_shot);
            }
            if ($result) {
                exit($this->returnApiSuccess());
            } else {
                exit($this->returnApiError(BaseController::FATAL_ERROR, '获取工单完结码失败'));
            }
        }



    }

    /**
     * 工单完结
     */
    public function end_order()
    {
        $end_number = I('post.end_number');
        $order_number = I('post.order_number');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->checkparam(array($user_id, $token, $order_number, $end_number));
        $this->checktoken($user_id, $token);
        $map['order_number'] = $order_number;
        $where['username'] = $order_number;

        $re_service = $this->order_model->where($map)->field('service_price')->find();
        if(empty($re_service['service_price'])){

            exit($this->returnApiError(BaseController::FATAL_ERROR, '完结失败，服务项不能为空，请添加服务项。'));

        }
        $code = $this->shot_message_model->where($where)->field('code,end_time')->find();
        $_mess_number = $code['code'];
        $end_time = time();
        if ($end_number == $_mess_number) {
            if (time() < $code['end_time']) {
                $re_parts_status = $this->order_model->where($map)->field('re_parts_status')->find();
                if($re_parts_status['re_parts_status'] == 1 ) {

                    $res_end = $this->order_repair_model->where($map)->setField('confirm_time', $end_time);
                    $res_order = $this->order_model->where($map)->setField('status', 20);
                    $res_user = $this->order_user_model->where($map)->setField('status', 20);
                    $this->upload__end($order_number);

                }else{


                    $res_end = $this->order_repair_model->where($map)->setField('end_time', $end_time);
                    $res_order = $this->order_model->where($map)->setField('status', 13);
                    $res_user = $this->order_user_model->where($map)->setField('status', 13);
                    $this->upload__end($order_number);

                }



                if ($res_end && $res_order && $res_user) {
                    $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
                    $track['order_number'] = $order_number;
                    $track['create_time'] = time();
                    $track['content'] = "维修商完成维修";
                    $track['person'] = $user_shop['real_name'];
                    $tra = D('order_track')->add($track);
                    $baoxiu_type  =  $this->order_pro_model->where(array('order_number'=>$order_number))->field('baoxiu_type')->find();
                   if($baoxiu_type['baoxiu_type'] == 2 ){
                      $this->examine_price($order_number);
                   }


                    exit($this->returnApiSuccess());
                } else {
                    exit($this->returnApiError(BaseController::FATAL_ERROR, '失败'));
                }
            } else {
                exit($this->returnApiError(BaseController::FATAL_ERROR, '验证码失效'));
            }
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '请输入正确的工单完结码'));
        }
    }


    private function upload__end($order_number)
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();

        if($info['front_picture']['savename']){
            $data['end_picture_1'] = '/data/upload/'.$info['front_picture']["savepath"].$info['front_picture']["savename"];
            $data['end_picture_1'] = $this->geturl($data['end_picture_1']);
        }
        if($info['side_picture']['savename']){
            $data['end_picture_2'] = '/data/upload/'.$info['side_picture']["savepath"].$info['side_picture']["savename"];
            $data['end_picture_2'] = $this->geturl($data['end_picture_2']);
        }
        if($info['end_code_picture']['savename']){
            $data['wanjiema'] = '/data/upload/'.$info['end_code_picture']["savepath"].$info['end_code_picture']["savename"];
            $data['wanjiema'] = $this->geturl($data['wanjiema']);
        }
        if($info['buying_picture']['savename']){
            $data['buying_picture'] = '/data/upload/'.$info['buying_picture']["savepath"].$info['buying_picture']["savename"];
            $data['buying_picture'] = $this->geturl($data['buying_picture']);
        }
        $this->order_model->where(array('order_number' => $order_number))->save($data);

    }

    /**
     * 拒绝接单
     */
    public function refuse_order()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $order_number = I('post.order_number');
        $this->checkparam(array($user_id, $token, $order_number));
        $this->checktoken($user_id, $token);
        $map['order_number'] = $order_number;

        $is_repair_service = $this->order_model->where($map)->field('repair_service_id')->find();
        if(empty($is_repair_service['repair_service_id'])){
            $ser_status['status'] = 1;
            $ser_status['repair_person_id'] = "";
            $ser_status['repair_service_id'] = "";
            $this->order_model->where($map)->save($ser_status);
            $this->order_repair_model->where($map)->delete();
            $this->order_user_model->where($map)->setField('status',1);
        }else{

            $status['status'] = 10;

            $user = $this->order_user_model->where($map)->save($status);
            $status['repair_person_id'] = '';

            $order = $this->order_model->where($map)->save($status);
            $rep['repair_person_id'] = '';


            if ($order && $user) {
                $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
                $track['order_number'] = $order_number;
                $track['create_time'] = time();
                $track['content'] = $user_shop['real_name'] . "取消维修单";
                $track['person'] = $user_shop['real_name'];
                $tra = D('order_track')->add($track);
                exit($this->returnApiSuccess());
            } else {
                exit($this->returnApiError(BaseController::FATAL_ERROR, '取消失败'));
            }
        }


    }


    /**
     * 取消订单
     */
    public function cancel_order()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $order_number = I('post.order_number');
        $cancel_order_reason = I('post.reason');
        $this->checkparam(array($user_id, $token, $order_number, $cancel_order_reason));
        $this->checktoken($user_id, $token);
        $map['order_number'] = $order_number;
        $save['status'] = '14';
        $reason['cancel_order_reason'] = $cancel_order_reason;
        $reason['cancel_order_time'] = time();
        $repair = $this->order_repair_model->where($map)
            ->save($reason);
        $order_user = $this->order_user_model
            ->where($map)
            ->save($save);
        $order = $this->order_model
            ->where($map)
            ->save($save);
        if ($repair && $order && $order_user) {
            exit($this->returnApiSuccess());
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '取消失败'));
        }

    }


    public function yuyue_time()
    {
        $data['one'] = array('time' => date("Y-m-d"));
        $data['two'] = date("Y-m-d", strtotime("+1 day"));
        $data['three'] = date("Y-m-d", strtotime("+2 day"));
        $data['four'] = date("Y-m-d", strtotime("+3 day"));

        $time = date('His', time());
        if ($time > 0 && $time < 120000) {
            $abc[] = array('time' => date("Y-m-d"), 'time_son' => array(0 => '12:00-17:00', 1 => '17:00-22:00'));
        }

        if ($time > 120000 && $time < 180000) {
            $abc[] = array('time' => date("Y-m-d"), 'time_son' => array(0 => '17:00-22:00'));
        }
        $abc[] = array('time' => date("Y-m-d", strtotime("+1 day")), 'time_son' => array(0 => '07:00-12:00 ', 1 => '12:00-17:00', 2 => '17:00-22:00'));
        $abc[] = array('time' => date("Y-m-d", strtotime("+2 day")), 'time_son' => array(0 => '07:00-12:00 ', 1 => '12:00-17:00', 2 => '17:00-22:00'));
        $abc[] = array('time' => date("Y-m-d", strtotime("+3 day")), 'time_son' => array(0 => '07:00-12:00 ', 1 => '12:00-17:00', 2 => '17:00-22:00'));

        exit($this->returnApiSuccess($abc));

    }

    /**
     * 添加预约。。更改预约
     */
    public function add_yuyue()
    {
        $order_number = I('post.order_number');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $yuyue_time = I('post.yuyue_time');
        $gaiyue_reason = I('post.gaiyue_reason');
        $this->checkparam(array($order_number, $user_id, $token, $yuyue_time));
        $this->checktoken($user_id, $token);
        $map['order_number'] = $order_number;
        $status = $this->order_model->where($map)->field('status')->find();

        $yuyue['gaiyue_reason'] = $gaiyue_reason;
        $yuyue['yuyue'] = time();
        $yuyue['yuyue_time'] = $yuyue_time;

        $res_yuyue = $this->order_repair_model->where($map)->save($yuyue);


        if ($status['status'] !== 8) {
            $save['status'] = 8;
            $this->order_model->where($map)->save($save);
            $this->order_user_model->where($map)->save($save);
        }
        if ($res_yuyue) {

            $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
            $track['order_number'] = $order_number;
            $track['create_time'] = time();
            $track['content'] = "维修商预约上门时间为：" . $yuyue_time;
            if ($gaiyue_reason) {
                $track['content'] = "维修商改约上门时间为：" . $yuyue_time . "改约原因为：" . $gaiyue_reason;
            } else {
                $track['content'] = "维修商预约上门时间为：" . $yuyue_time;
            }
            $track['person'] = $user_shop['real_name'];
            $tra = D('order_track')->add($track);

            exit($this->returnApiSuccess());
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '改约失败，预约时间已经是该时间'));
        }
    }

    /**
     *服务项
     */

    public function service_list()
    {
        $order_number = I('post.order_number');
        $map_pro['order_number'] = $order_number;
        $service = $this->order_pro_model->where($map_pro)->field('pro_xinhao,pro_property,pro_product,order_type,baoxiu_type')->find();
        if ($service['order_type'] == 1) $map_price['order_type'] = '安装';
        if ($service['order_type'] == 2) $map_price['order_type'] = '维修';
        if ($service['order_type'] == 3) $map_price['order_type'] = '送修';
        $map_price['pro_pinlei'] = $service['pro_xinhao'];
        $map_price['pro_property'] = $service['pro_property'];
        $map_price['product'] = $service['pro_product'];
        $service_pro = $this->pro_price_detail_model->where($map_price)->field('id,service_project,service_content')->select();
        if ($service['order_type'] == 2) {
            for ($i = 0; $i < count($service_pro); $i++) {
                $map_ser['pid'] = $service_pro[$i]['id'];
                if ($service['baoxiu_type'] == 1) {
                    $c = $this->pro_price_service_model->where($map_ser)->field('id,price,service')->select();
                }
                if ($service['baoxiu_type'] == 2) {
                    $c = $this->pro_price_service_model->where($map_ser)->field('id,price_wai,service')->select();
                }
                $service_pro[$i]['service_content'] = $c;
            }
        }
        if ($service['order_type'] == 3) {
            for ($i = 0; $i < count($service_pro); $i++) {
                $map_ser['pid'] = $service_pro[$i]['id'];
                if ($service['baoxiu_type'] == 1) {
                    $c = $this->pro_price_service_model->where($map_ser)->field('id,price,service')->select();
                }
                if ($service['baoxiu_type'] == 2) {
                    $c = $this->pro_price_service_model->where($map_ser)->field('id,price_wai,service')->select();
                }
                $service_pro[$i]['service_content'] = $c;
            }
        }


        if ($service['order_type'] == 1) {
            for ($i = 0; $i < count($service_pro); $i++) {
                $service_pro[$i]['service_content'] = array(0 => array('service' => '上门安装'));
            }
        }
        exit($this->returnApiSuccess($service_pro));
    }

    /**
     * 配件列表
     */
    public function parts_list()
    {
        $order_number = I('post.order_number');
        $map_pro['order_number'] = $order_number;
        $service = $this->order_pro_model->where($map_pro)->field('pro_xinhao,pro_property,pro_product,order_type')->find();
        $map_parts['parts_pinlei'] = $service['pro_xinhao'];
        $map_parts['pro_property'] = $service['pro_property'];
        $map_parts['parts_product'] = $service['pro_product'];
        $parts = $this->pro_parts_price_model->where($map_parts)->field('parts_name,parts_price,id')->select();
        exit($this->returnApiSuccess($parts));
    }

    /**
     * 添加更改服务项
     */
    public function add_service()
    {


        $service = '';
        $service_price = '';
        $ser_id = I('post.id');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $order_number = I('order_number');
        $this->checkparam(array($ser_id, $user_id, $token, $order_number));
        $this->checktoken($user_id, $token);
        $id = explode(',', $ser_id);
        $judge_type = $this->order_pro_model->where(array('order_number' => $order_number))->field('order_type,baoxiu_type')->find();
        if ($judge_type['order_type'] == 1) exit($this->returnApiError(BaseController::FATAL_ERROR, '安装单不允许添加服务'));

        for ($i = 0; $i < count($id); $i++) {
            $map_id['id'] = $id[$i];
            $res_ser = $this->pro_price_service_model->where($map_id)->field('price,price_wai,service')->find();
            $service .= $res_ser['service'] . ',';

            if ($judge_type['baoxiu_type'] == 1) {
                $service_price += $res_ser['price'];
            }
            if ($judge_type['baoxiu_type'] == 2) {
                $service_price += $res_ser['price_wai'];
            }

        }

        $service = rtrim($service, ',');

        //修改价格
        $map['order_number'] = $order_number;
        $map_order_price['order_number'] = I('post.order_number');
        $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();
        $order_save['service_price'] = $service_price;
        $order_save['repair_price'] = $service_price + $order_price['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'];
        $this->order_model->where($map)->save($order_save);

        //修改服务项目
        $update['create_time'] = time();
        $update['add_service'] = $service;
        $add_service = $this->order_add_service_model->where(array('order_number' => $order_number))->field('id')->find();
        if ($add_service['id']) {
            $result = $this->order_add_service_model->where($map)->save($update);
        } else {
            $update['order_number'] = $order_number;
            $result = $this->order_add_service_model->add($update);
        }


        if ($result) {
            //添加跟踪日志
            $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
            $track['order_number'] = $order_number;
            $track['create_time'] = time();
            $track['content'] = "更改服务项目为" . $service;
            $track['person'] = $user_shop['real_name'];
            $tra = D('order_track')->add($track);
            exit($this->returnApiSuccess());
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '添加失败'));
        }

    }

    /**
     * 增加配件
     */
    public function add_parts()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $parts_price = I('post.parts_price');
        $order_number = I('post.order_number');
        $parts_count = I('post.parts_count');
        $parts_type = I('post.parts_type');
        $parts_picture = I('post.parts_picture');
        $all_picture = I('post.all_picture');
        $parts_name = I('post.parts_name');
        $this->checktoken($user_id, $token);
        $this->checkparam(array($user_id, $token, $parts_count, $order_number, $parts_name));
        $is_exis = $this->user_repairer_model->where(array('user_id'=>$user_id))->field('phone,qq')->find();
        $rec_address = $this->user_rec_address_model->where(array('user_id'=>$user_id))->field('rec_address')->find();
        if(empty($is_exis['phone']) || empty($is_exis['qq']) || empty($rec_address['rec_address'])){
            exit($this->returnApiError(BaseController::FATAL_ERROR, '个人信息不完整，请补全个人信息'));
        }
        if ($parts_type == 1) {
            $map_pro['order_number'] = $order_number;
            $service = $this->order_pro_model->where($map_pro)->field('pro_xinhao,pro_property,pro_product,order_type')->find();
            $map_parts['parts_name'] = $parts_name;
            $map_parts['parts_pinlei'] = $service['pro_xinhao'];
            $map_parts['pro_property'] = $service['pro_property'];
            $map_parts['parts_product'] = $service['pro_product'];
            $parts = $this->pro_parts_price_model->where($map_parts)->field('parts_price')->find();
            $parts_price = $parts['parts_price'];
        }
        if ($parts_type == 2) {
            $parts_price = I('post.parts_price');
        }
        $save['order_number'] = $order_number;
        $save['create_time'] = time();
        $save['parts_price'] = $parts_price;


        $save['parts_count'] = $parts_count;
        $save['parts_name'] = $parts_name;
        $save['type'] = $parts_type;
        $save['status'] = 2;
        $result = $this->pro_parts_model->add($save);
        $this->upload_model($order_number);
        if ($result) {
            if ($parts_type == 1) {
                $where['type'] = 1;
            }
            if ($parts_type == 2) {
                $where['type'] = 2;
            }
            $where['order_number'] = $order_number;
            $data = $this->pro_parts_model->where($where)->field('id,parts_name,parts_count,type,parts_picture,all_picture')->select();

            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['parts_picture'] = $this->geturl('/' . $data[$i]['parts_picture']);
                $data[$i]['all_picture'] = $this->geturl('/' . $data[$i]['all_picture']);
            }
            $where_save['order_number'] = $order_number;
            $this->order_model->where($where_save)->setField('status', '12');
            $this->order_user_model->where($where_save)->setField('status', '12');
            $user_shop = D('user_repairer')->where("user_id=" . $user_id)->field('real_name')->find();
            $track['order_number'] = $order_number;
            $track['create_time'] = time();
            $track['content'] = "维修商申请增加配件";
            $track['person'] = $user_shop['real_name'];
            $tra = D('order_track')->add($track);

            //修改价格
            $map_order_price['order_number'] = I('post.order_number');
            $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();
            $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();


            if($baoxiu_type['baoxiu_type'] == 1 && $parts_type == 1 ){

                $order_save['parts_price'] = $order_price['parts_price'];

            }else{
                $order_save['parts_price'] = $order_price['parts_price'] + $parts_price * $parts_count;
            }

            $order_save['repair_price'] = $order_price['service_price'] + $order_save['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'];
            $this->order_model->where($map_order_price)->save($order_save);


            exit($this->returnApiSuccess($data));

        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '添加失败'));
        }

    }

    /**
     *
     * @param $field
     * @param $order_number
     */
    public function upload_model($order_number)
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();


        $data['parts_picture'] = '/data/upload/' . $info['parts_picture']["savepath"] . $info['parts_picture']["savename"];
        $data['parts_picture'] =  $this->geturl(  $data['parts_picture'] );
        $data['all_picture']   = '/data/upload/' . $info['all_picture']["savepath"] . $info['all_picture']["savename"];
        $data['all_picture'] =  $this->geturl(  $data['all_picture'] );
        $map['order_number'] = $order_number;
        $result = $this->pro_parts_model->where($map)->order('id desc')->field('id')->find();
        $map['id'] = $result['id'];
        $data['order_number'] = $order_number;
        $data['create_time'] = time();
        $success = $this->pro_parts_model
            ->where($map)
            ->save($data);
        if (!$success) {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '上传文件失败'));
        }
    }

    /**
     * 删除配件
     */
    public function del_parts()
    {
        $order_number = I('order_number');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $id = I('post.id');
        $this->checkparam(array($user_id, $token, $id));
        $this->checktoken($user_id, $token);
        $ma['id'] = $id;
        $result = $this->pro_parts_model->where($ma)->delete();
        $map['order_number'] = $order_number;
        $is_exis = $this->pro_parts_model->where($map)->field('id')->find();
        if (!$is_exis['id']) {
            $where_save['order_number'] = $order_number;
            $this->order_model->where($where_save)->setField('status', '16');
            $this->order_user_model->where($where_save)->setField('status', '16');
        }

        if ($result) {

            $data = $this->pro_parts_model->where($ma)->field('type,parts_price,order_number,parts_count')->find();


            $map_order_price['order_number'] = $data['order_number'];
            $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();

            $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();


            if($baoxiu_type['baoxiu_type'] == 1 && $data['type'] == 1 ){


                $order_save['parts_price'] = $order_price['parts_price'] ;

            }else{

                $order_save['parts_price'] = $order_price['parts_price'] - $data['parts_price'] * $data['parts_count'];

            }


            $order_save['repair_price'] = $order_price['service_price'] + $order_save['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'];
            $this->order_model->where($map_order_price)->save($order_save);

            exit($this->returnApiSuccess());
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '删除失败'));
        }


    }

    /**
     * 修改配件单个数据
     */
    public function edit_parts_list()
    {
        $id = I('post.id');
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->checkparam(array($id, $user_id, $token));
        $this->checktoken($user_id, $token);
        $map['id'] = $id;
        $parts = $this->pro_parts_model->where($map)->field('type,parts_name,parts_count,parts_picture,all_picture,parts_price,type')->find();
        $parts['parts_picture'] = $this->geturl('/' . $parts['parts_picture']);
        $parts['all_picture'] = $this->geturl('/' . $parts['all_picture']);
        exit($this->returnApiSuccess($parts));
    }


    /**
     * 修改配件
     */
    public function edit_parts()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $order_number = I('post.order_number');
        $parts_count = I('post.parts_count');
        $parts_type = I('post.parts_type');
        $parts_name = I('post.parts_name');
        $id = I('post.id');
        $parts_picture = I('post.parts_picture');
        $all_picture = I('post.all_picture');
        $this->checktoken($user_id, $token);
        $this->checkparam(array($user_id, $token, $parts_count, $order_number, $parts_name, $id));
        if ($parts_type == 1) {
            $map_pro['order_number'] = $order_number;
            $service = $this->order_pro_model->where($map_pro)->field('pro_xinhao,pro_property,pro_product,order_type')->find();
            $map_parts['parts_name'] = $parts_name;
            $map_parts['parts_pinlei'] = $service['pro_xinhao'];
            $map_parts['pro_property'] = $service['pro_property'];
            $map_parts['parts_product'] = $service['pro_product'];
            $parts = $this->pro_parts_price_model->where($map_parts)->field('parts_price')->find();
            $parts_price = $parts['parts_price'];
        }
        if ($parts_type == 2) {
            $parts_price = I('post.parts_price');
        }

        //修改价格
        $where_save['order_number'] = $order_number;
        $order_number = $this->pro_parts_model->where(array('id' => $id))->field('order_number,parts_price,parts_count')->find();

        $map_order_price['order_number'] = $order_number['order_number'];
        $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();


        $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();
        $order_save['parts_price'] = $order_price['parts_price'] - $order_number['parts_price'] * $order_number['parts_count'] + $parts_price * $parts_count;

        if($baoxiu_type['baoxiu_type'] == 1 && $parts_type == 1 ){

            $order_save['parts_price'] = $order_price['parts_price'] ;

        }else{

            $order_save['parts_price'] = $order_price['parts_price'] - $order_number['parts_price'] * $order_number['parts_count'] + $parts_price * $parts_count;

        }

        $order_save['repair_price'] = $order_price['service_price'] + $order_save['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'];


        $this->order_model->where($map_order_price)->save($order_save);

        //修改数据

            $this->edit_model($id);
            $save['order_number'] = $order_number;
            $save['parts_price'] = $parts_price;
            $save['parts_count'] = $parts_count;
            $save['parts_name'] = $parts_name;
            $save['type'] = $parts_type;
            $where['id'] = $id;
            $result = $this->pro_parts_model->where($where)->save($save);
        $this->order_model->where($where_save)->setField('status', '12');
        $this->order_user_model->where($where_save)->setField('status', '12');

        exit($this->returnApiSuccess());

    }

    public function edit_model($id)
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();

        if($info['parts_picture']['savename']){
            $save['parts_picture'] = '/data/upload/' . $info['parts_picture']["savepath"] . $info['parts_picture']["savename"];
            $save['parts_picture'] =  $this->geturl($save['parts_picture']);
        }
        if($info['all_picture']['savename']){
            $save['all_picture'] = '/data/upload/' . $info['all_picture']["savepath"] . $info['all_picture']["savename"];
            $save['all_picture'] = $save['parts_picture'];
        }

        $map['id'] = $id;
        $success = $this->pro_parts_model
            ->where($map)
            ->save($save);
        if (!$success) {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '上传文件失败'));
        }
    }


    /**
     * 工单跟踪
     */
    public function order_track()
    {
        $order_number = I('get.order_number');
        $map['order_number'] = $order_number;
        $taking_time = $this->order_repair_model->where($map)->field('taking_time')->find();

        $lssue = D('order_track')->where('order_number=' . $order_number)->order('id asc')->field('create_time')->find();

        $data['order_number'] = $order_number;
        $data['taking_time'] = date('Y-m-d H:i:s', $taking_time['taking_time']);
        $data['lssue_time'] = date('Y-m-d H:i:s', $lssue['create_time']);
        $re = D('order_track')->where('order_number=' . $order_number)->field('create_time,content')->order('create_time desc')->select();

        for ($i = 0; $i < count($re); $i++) {
            $data['track'][$i]['create_time'] = date('Y-m-d H:i:s', $re[$i]['create_time']);
            $data['track'][$i]['content'] = $re[$i]['content'];
        }
        exit($this->returnApiSuccess($data));
    }

    /**
     * 上传成为服务中心的资料
     */

    public function submit_service_data()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $add['company'] = I('post.company');
        $add['create_time'] = time();
        $add['faren_name'] = I('post.legal');
        $add['repair_id'] = I('post.user_id');
        $add['email'] = I('post.email');
        $this->checktoken($user_id, $token);
        $this->checkparam(array(I('post.user_id'), I('post.token'), I('post.company'), I('post.legal'), I('post.user_id'), I('post.email')));
        if (D('apply_service')->create($add)) {
            $result = D('apply_service')->add($add);
			
            $this->upload_service($user_id);
            if ($result) {

                $result = $this->user_repairer_model->where(array('user_id' => $user_id))->setField('app_ser', 4);
                exit($this->returnApiSuccess());
            }
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, D('apply_service')->getError()));
        }
    }

    private function upload_service($user_id)
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();
		
		
        if($info['business_license']['savename']){
            $data['business_license'] = '/data/upload/'.$info['business_license']["savepath"].$info['business_license']["savename"];
            $data['business_license'] = $this->geturl($data['business_license']);
        }
        if($info['tax_regis']['savename']){
            $data['tax_regis'] = '/data/upload/'.$info['tax_regis']["savepath"].$info['tax_regis']["savename"];
            $data['tax_regis'] = $this->geturl($data['tax_regis']);
        }
        if($info['special_regis']['savename']){
            $data['special_regis'] = '/data/upload/'.$info['special_regis']["savepath"].$info['special_regis']["savename"];
            $data['special_regis'] = $this->geturl($data['special_regis']);
        }
        D('apply_service')->where(array('user_id'=>$user_id))->save($data);

    }

    /**
     * 申请成为服务中心
     */
    public function apply_service()
    {
        $user_id = I('post.user_id');
        $map['user_id'] = $user_id;
        $token = I('post.token');
        $this->checktoken($user_id, $token);
        $this->checkparam(array($user_id, $token));

        $is_exis = $this->user_repairer_model->where(array('user_id'=>$user_id))->field('phone,qq')->find();
        $rec_address = $this->user_rec_address_model->where(array('user_id'=>$user_id))->field('rec_address')->find();
        if(empty($is_exis['phone']) || empty($is_exis['qq']) || empty($rec_address['rec_address'])){
            exit($this->returnApiError(BaseController::FATAL_ERROR, '个人信息不完整，请补全个人信息(QQ,电话，收货地址)'));
        }


        $save['app_ser'] = 1;
        $save['app_time'] = time();
        $result = $this->user_repairer_model->where($map)->save($save);
        if ($result) {
            exit($this->returnApiSuccess());
        }
    }

    /**
     * 申请服务中心状态
     */
    public function apply_service_list()
    {
        $user_id = I('post.user_id');
        $map['user_id'] = $user_id;
        $result = $this->user_repairer_model->where($map)->field('app_ser')->find();
        $data['apply_service_status'] = empty($result['app_ser']) ? '0' : $result['app_ser'];
        exit($this->returnApiSuccess($data));
    }


    /**
     * 弹出层
     */
    public function eject()
    {
        $user_id = I('post.user_id');
        $status = 9;
        $map['status'] = 9;
        $map['repair_person_id'] = $user_id;
        $map['app_danchu'] = 1;
        $order = $this->order_model->where($map)->field('order_number,repair_service_id')->find();
        $order_user = $this->order_user_model->where(array('order_number' => $order['order_number']))->field('user_phone')->find();
        $service = $this->user_service_model->where(array('user_id' => $order['repair_service_id']))->field('phone')->find();
        $data['order_number'] = $order['order_number'];
        $data['user_phone'] = $order_user['user_phone'];
        $data['service_phone'] = $service['phone'];
        if (empty($order['order_number'])) {
            $data = '';
        }
        exit($this->returnApiSuccess($data));

    }

    public function eject_cancel()
    {
        $map['order_number'] = I('post.order_number');
        $result = $this->order_model->where($map)->setField('app_danchu', 2);
        if ($result) {
            exit($this->returnApiSuccess());
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, '取消失败'));
        }
    }

    private function shot_mess_send($order_number)
    {
        $order_user = D('order_user')->where(array('order_number' => $order_number))->field('user_name,user_address,user_city,user_phone')->find();
        $order_pro = D('order_pro')->where(array('order_number' => $order_number))->field('order_type,pro_name,pro_product,pro_property,remarks')->find();
        if ($order_pro['order_type'] == 1) $order_pro['order_type'] = "安装";
        if ($order_pro['order_type'] == 2) $order_pro['order_type'] = "维修";
        if ($order_pro['order_type'] == 3) $order_pro['order_type'] = "送修";
        if ($order_pro['order_type'] == 4) $order_pro['order_type'] = "清洗";


        $data = array('0' => $order_pro['order_type'] . ":" . $order_user['user_name'] . " " . $order_user['user_phone'] . "  " . $order_user['user_city'],
            '1' => $order_pro['pro_name'] . ":" . $order_pro['pro_product'] . "," . $order_pro['pro_property'],
            '2' => "故障描述：" . $order_pro['remarks']
        );
        return $data;

    }




    /**
     * 确认支付
     */
    public function examine_price($order_number)
    {
        $model = D('order');
        $model->startTrans();
        $roll_va = true;
        $is_status = D('order')->where(array('order_number'=>$order_number))->field('status')->find();


        $map['order_number'] = $order_number;
        $id = D('order')->where($map)->field('user_id,repair_person_id,repair_service_id,service_price,parts_price,far_price,repair_price,logistics_price')->find();
        $order_pro = D('order_pro')->where($map)->field('baoxiu_type,order_type')->find();
        $wallet_buyers = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->find();

        $save_buyers['balance']   = $wallet_buyers['balance']   - $id['repair_price'];
        $save_buyers['use_money'] = $wallet_buyers['use_money'] + $id['repair_price'];
        $save_buyers['frozen_balance']   = $wallet_buyers['frozen_balance'] - $id['repair_price'];



        if($order_pro['baoxiu_type'] == 1 ){
            $save_change_wallet = D('wallet_buyers')->where(array('user_id'=>$id['user_id']))->save($save_buyers);

            if(empty($save_change_wallet)){
                $roll_va = false;
            }
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
            $add_income['order_number'] = I('order_number');
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
            $add_service['order_number']   = I('order_number');
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
            $add_income['order_number'] = I('order_number');
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
            $add_service['order_number']   = I('order_number');
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


            $add_income['order_number'] = I('order_number');
            $add_income['create_time']  = time();
            $add_income['service']      = 0;
            $add_income['pintai']       = $pintai;
            $add_income['repair']       = $repair_all;
            $add_income['far_price']    = $id['far_price'];
            $income = D('income')->add($add_income);

            if(empty($income)){
                $roll_va = false;
            }



            //添加到wallet_service_consumm表

            $add_service['create_time']    = time();
            $add_service['order_number']   = I('order_number');
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


        $data['content'] = "客户已完成付款";
        $data['create_time']    =time();
        $data['person']  ="现场付款";
        $data['order_number'] = I('order_number');
        $result = D('order_track')->add($data);
        if(empty($result)){
            $roll_va = false;
        }

        if($roll_va == true){
            $model->commit();
            exit($this->returnApiSuccess());
        } else {
            $model->rollback();
            exit($this->returnApiError(BaseController::FATAL_ERROR, '结算失败'));
        }
    }







}
