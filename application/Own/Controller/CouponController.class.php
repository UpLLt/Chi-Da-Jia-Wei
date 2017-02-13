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
class CouponController extends BaseController{

    protected $coupon_model;
    protected $ticket_model;

    public function __construct()
    {
        parent::__construct();
        $this->coupon_model =  D('coupon');
        $this->ticket_model =  D('ticket');
    }

    public function coupon(){
        $user_id = I('post.user_id');
        $token   = I('post.token');

        $this->checkparam(array($user_id,$token));
        $this->checktoken($user_id,$token);

        $data = $this->coupon_model
             ->join('as a left join lb_ticket as b on a.tid = b.id')
             ->where(array('a.uid'=>$user_id,'a.status'=>1))
             ->order('create_time desc')
             ->field('b.price,b.rules,a.id')
             ->select();

        exit($this->returnApiSuccess($data));
    }



}