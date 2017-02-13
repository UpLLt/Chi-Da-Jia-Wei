<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 10:01
 */
namespace Own\Controller;
use Think\Controller;
class NofityBaseController extends BaseController {

    protected $order_model;
    protected $error_list;

    private $check_order_status = false;
    public function __construct()
    {
        parent::__construct();
        $this->order_model = D('order');
    }

    /**
     * 错误日志新增
     * @param $error_msg
     */
    public function addOrderError($error_msg, $order_sn)
    {
        if (is_array($error_msg)) {
            foreach ($error_msg as $k => $v) {
                $error[] = $k . '=' . $v;
            }
            $error_msg = $error;
        }
        D('order_error')->add(
            array(
                'order_number' => $order_sn,
                'content' => json_encode($error_msg),
                'create_time' => time()
            )
        );

        return true;
    }

    /**
     * 检查订单有效性
     * @param $order_sn 订单编号
     * @param $auto 手动
     * @return bool
     */
    public function chechValidity($order_sn, $auto = true)
    {
        $result = $this->order_model->where(array('order_number' => $order_sn))->find();
        //排除无效订单
        if ($result == null) {
            $this->error_list[] = '无效订单';
            return false;
        }

        if ($auto) {
            //避免重复处理
            if ($result['status'] == 1) {
                $this->error_list[] = '订单已处理';
                return false;
            }
        }



        $this->check_order_status = true;

        return true;
    }


    /**
     * 更改订单状态
     * @param $order_sn 订单编号
     * @param $order_price 支付金额
     * @return bool
     */
    public function update_order_status($order_sn,  $order_price)
    {
        //修改订单状态
        $order_status = $this->order_model
                ->where(array('order_number' => $order_sn))
                ->field('status,user_id')
                ->find();

        if( $order_status['status'] == 0 ){

            $result = $this->order_model
                ->where(array('order_number' => $order_sn))
                ->save(array(

                    'status' => 1,

                ));

            $result_1 = D('order_user')->where(array('order_number' => $order_sn))
                ->save(array(
                    'status' => 1,
                ));


            $add_service['create_time']    = time();
            $add_service['order_number']   = $order_sn;
            $add_service['order_price']    = $order_price;
            $add_service['type']           = "1";
            $add_service['user_id']        = $order_status['user_id'];
            $add_service['buyer_id']       = $order_status['user_id'];
            $add_service['liushuihao']     = $order_sn;

            D('wallet_service_consum')->add($add_service);


        }elseif( $order_status['status'] == 8 ){

            $result = $this->order_model
                ->where(array('order_number' => $order_sn))
                ->save(array(
                    'status' => 7,
                ));

            $result_1 = D('order_user')->where(array('order_number' => $order_sn))
                ->save(array(
                    'status' => 7,
                ));


            if(empty($order_price)){
                $order = D('order')->where(array('order_number' => $order_sn))
                            ->field('user_id,repair_person_id,repair_service_id')
                            ->find();

                $add_service['create_time']    = time();
                $add_service['order_number']   = $order_sn;
                $add_service['order_price']    = 0;
                $add_service['service_shouru'] = 0;
                $add_service['pintai_shouru']  = 0;
                $add_service['repairer_price'] = 0;
                $add_service['type']           = "1";
                $add_service['user_id']        = $order['user_id'];
                $add_service['repairer_id']    = $order['repair_person_id'];
                $add_service['service_id']     = $order['repair_service_id'];
                $add_service['buyer_id']       = $order['user_id'];
                $add_service['liushuihao']     = $order_sn;

                D('wallet_service_consum')->add($add_service);

                $content = "用户主动付款";
                $user    = '用户';

                $data['content'] = $content;
                $data['create_time']    =time();
                $data['person']   =  $user;
                $data['order_number'] = $order_sn;
                $result = D('order_track')->add($data);


            }else{
                $publicsh = A('Lianbao_PC/Publish');
                $content = "用户主动付款";
                $user    = '用户';
                $publicsh->auto_a_price($order_sn,$content,$user);
            }



        }

        if ($result === false || $result_1 == false) {
            $this->addOrderError('状态更新失败', $order_sn);
            return false;
        };

        return true;
    }





}


