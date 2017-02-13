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
class WxController extends NofityBaseController
{

    public function __construct()
    {
        parent::__construct();
        header("Content-type:text/html;charset=utf-8");
        ini_set('date.timezone', 'Asia/Shanghai');
        vendor('WxPayPubHelper.WxPayPubHelper');
    }

    public function index()
    {
        //使用通用通知接口
        $notify = new \Notify_pub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        //回调错误
        if (!$xml) return false;

        $notify->saveData($xml);
        //签名状态
        $checkSign = true;
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
            $checkSign = false;
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();

        //通知微信，成功获取到相应的异步通知
        echo $returnXml;
        if (!$checkSign) exit;


        //微信返回参数
        $back_data = $notify->getData();
        $order_sn = $back_data['out_trade_no']; //订单号
        $order_sn = substr($order_sn,0,strlen($order_sn)-4);

//        $order_sn = 'SN3371371264'; //订单号
        $order_price = $back_data['total_fee'] / 100; //微信返回的是分，换算成元
//        $order_price = 100;

        $this->addOrderError($back_data, $order_sn);

        if (empty($order_sn))
            exit('order sn  is null');

        if (empty($order_price) || $order_price == 0 || $order_price < 0)
            exit('order price  is null');


        //排除无效订单,避免重复处理
        if (!$this->chechValidity($order_sn)) {
            $this->addOrderError($this->error_list, $order_sn);
            exit();
        }

        //改变订单状态
        $result = $this->update_order_status($order_sn, $order_price);
        if (!$result) exit();

    }
}
