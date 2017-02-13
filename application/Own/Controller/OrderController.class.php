<?php
namespace Own\Controller;
use Think\Controller;
class OrderController extends BaseController {

    protected $order_model;
    protected $order_pro_model;
    protected $order_user_model;
    protected $order_track_model;

    public function __construct()
    {
        parent::__construct();
        $this->order_model = D('order');
        $this->order_pro_model = D('order_pro');
        $this->order_user_model = D('order_user');
    }


    /**
     * 发布订单
     */
    public function order(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $this->checktoken($user_id,$token);

        $real_name    = I('post.real_name');
        $user_city    = I('post.user_city');
        $user_address = I('post.user_address');
        $pro_name     = I('post.service_type');
        $remarks      = I('post.remarks');
        $order_type   = I('post.order_type');
        $phone        = I('post.phone');

        $this->checkparam(array($user_id,$real_name,$user_city,$user_address,$pro_name,$remarks,$order_type));

        $order_num             = order_number();
        $order['service_price']= 0;
        $order['repair_price'] = 0;
        $order['user_id']	   = $user_id;
        $order['pro_price']    = 0;
        $order['order_number'] = $order_num;
        $order['create_time']  = time();
        $order['status']= 0;
        $order['order_type']   = $order_type;
        $order['source']       = 1;

        $user['pro_price']     = 0;
        $user['user_city']     = $user_city;
        $user['order_number']  = $order_num;
        $user['user_name'] 	   = $real_name;
        $user['user_address']  = $user_address;
        $user['create_time']   = time();
        $user['user_id']       = $user_id;
        $user['status']        = 0;
        $user['user_phone']    = $phone;

        $pro['order_number']  = $order_num;
        $pro['pro_name']      = $pro_name;
        $pro['pro_price']     = 0;
        $pro['pro_count']     = 1;
        $pro['remarks']       = $remarks;
        $pro['order_type']    = $order_type;
        $pro['pro_xinhao']    = $order_type;
        $pro['baoxiu_type']   = 2;
        $pro['pro_product']   = $order_type;
        $pro['pro_property']  = $order_type;

        $order_add_service['add_service']  = $remarks;
        $order_add_service['create_time']  = time();
        $order_add_service['order_number'] = $order_num;
        $res_3 = D('order_add_service')->add($order_add_service);

        $res = $this->order_model->add($order);
        $res_1 = $this->order_user_model->add($user);
        $res_2 = $this->order_pro_model->add($pro);

        $track['order_number'] = $order_num;
        $track['create_time']  = time();
        $track['content']      = "用户发布工单成功";
        $track['person']       = $real_name;
        $res_4 = D('order_track')->add($track);

        $data['order_number'] = $order_num;
        if($res && $res_1 && $res_2 && $res_3 && $res_4){

            exit($this->returnApiSuccess($data));
        }else{

            exit($this->returnApiError(BaseController::FATAL_ERROR,'失败'));

        }
    }

    /**
     * 详情
     */
    public  function detail(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $order_number = I('post.order_number');
        $this->checkparam(array($user_id,$token,$order_number));
        $this->checktoken($user_id,$token);


        $list = $this->order_pro_model
            ->join('as a left join lb_order_user as b on a.order_number=b.order_number left join lb_order as c on a.order_number=c.order_number')
            ->where('a.order_number='.$order_number)
            ->field('b.user_address,a.pro_name,a.remarks,a.order_number,c.status')
            ->find();
        if($list['status']  == 0 ) $list['type'] = '待支付';
        if($list['status']  == 8 ) $list['type'] = '待服务';
        if($list['status']  == 7 ) $list['type'] = '已完结';
        if($list['status']  == 2 ) $list['type'] = '已取消';
        if($list['status']  == 1 ) $list['type'] = '待接单';
        if($list['status']  == 9 ) $list['type'] = '待预约';
        if($list['status']  == 10 ) $list['type']= '待指派';

        $owm  = D('wallet_admin')->field('own')->find();
        $list['price'] =  $owm['own'];
        exit($this->returnApiSuccess($list));

   }

    /**
     * 用户订单列表
     */
    public function order_list(){

        $user_id = I('post.user_id');
        $token   = I('post.token');
        $this->checkparam(array($user_id,$token));
        $this->checktoken($user_id,$token);

        $status = I('post.status');

        $count = $this->order_model->where(array('user_id'=>$user_id,'status'=>0))->count();
        if(empty($status)){
          $where = 'a.user_id = '.$user_id;
        }else{

          $where =  'a.user_id = '.$user_id. '  and a.status =  '.$status;

        }

        $list = $this->order_model
                     ->join('as a left join lb_order_pro as b on a.order_number=b.order_number left join lb_order_user as c on a.order_number = c.order_number left join lb_order_repair as d on a.order_number = d.order_number')
                     ->field('a.status,a.create_time,c.user_address,b.pro_name,b.remarks,d.yuyue_time,a.order_number')
                     ->order('a.create_time desc')
                     ->where($where)
                     ->select();
        foreach($list as $v){
            if($v['status']  == 0 ) $a['type'] = '待支付';
            if($v['status']  == 8 ) $a['type'] = '待服务';
            if($v['status']  == 7 ) $a['type'] = '已完结';
            if($v['status']  == 2 ) $a['type'] = '已取消';
            if($v['status']  == 1 ) $a['type'] = '待接单';
            if($v['status']  == 9 ) $a['type'] = '待预约';
            if($v['status']  == 10 ) $a['type']= '待指派';



            $a['pro_name']     = $v['pro_name'];
            $a['status']       = $v['status'];
            $a['user_address'] = $v['user_address'];
            $a['remarks']      = $v['remarks'];
            $a['create_time']  = date('Y-m-d',$v['create_time']);
            $a['bespoke_time'] = empty($v['yuyue_time']) ? '': $v['yuyue_time'];
            $a['order_number'] = $v['order_number'];
            $c[] = $a;

        }
            $data['list'] = $c;
             $data['count'] = $count;

        exit($this->returnApiSuccess($data));
    }

    /**
     * 取消订单
     */
    public function cancel_order(){
        $user_id = I('post.user_id');
        $token  = I('post.token');
        $order_number = I('post.order_number');

        $user_name = $this->order_user_model->where(array('order_number'=>$order_number))->field('user_name')->find();
        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id,$token,$order_number));

        $res   = $this->order_model->where(array('order_number'=>$order_number))->setField('status',2);
        $res_1 = $this->order_user_model->where(array('order_number'=>$order_number))->setField('status',2);

        $track['order_number'] = $order_number;
        $track['create_time']  = time();
        $track['content']      = "用户发布工单成功";
        $track['person']       = $user_name['user_name'];
        $res_2 = D('order_track')->add($track);

        if( $res && $res_1 && $res_2){
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,'失败'));
        }
    }

    /**
     * 设置金额
     */
    public function set_money(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $order_number = I('post.order_number');
        $price   = I('post.price');
        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id,$token,$order_number));

        $status = D('order')->where(array('order_number'=>$order_number))->field('status')->find();

        if($status['status'] != 8  ) exit($this->returnApiError(BaseController::FATAL_ERROR,'订单状态不正确'));

        $result = D('order')->where(array('order_number'=>$order_number))
            ->save(array(
                'service_price' => $price,
                'repair_price'  => $price,
            ));

        if($result){
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,'修改失败'));
        }
    }
}