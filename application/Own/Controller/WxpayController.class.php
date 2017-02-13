<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 11:55
 */
namespace Own\Controller;
use Repair\Controller\BaseController;
use Think\Controller;
class WxpayController extends BaseController
{


    public function __construct()
    {
        parent::__construct();

    }

    public function pay(){

        $user_id = I('post.user_id');
        $token   = I('post.token');
        $order_number = I('post.order_number');
        $price   = I('post.price');

        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id,$token,$order_number,$price));

        $this->index($order_number,$price);

//        exit($this->returnApiSuccess(I('')));

    }






    public function index($order_number,$price)
    {

        $order_number = $order_number.rand(1000,9999);
        vendor('WxPayPubHelper.WxPayPubHelper');
        $unifiedOrder = new \UnifiedOrder_pub();
        $unifiedOrder->setParameter("body", "驰达家维商品购买");
        $unifiedOrder->setParameter("out_trade_no", $order_number);
        $price = $price * 100;


        $unifiedOrder->setParameter("total_fee", $price);
        $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::NOTIFY_URL);
        $unifiedOrder->setParameter("trade_type", "APP");
        $unifiedOrder->setParameter("device_info", "WEB");

        $appparam = $unifiedOrder->getResultAppApi();

        if($appparam){
            $data['Appparam'] = $appparam;

        }else{
            $data['error'] = $unifiedOrder->result;
        }


        exit($this->returnApiSuccess($data));
    }
}
