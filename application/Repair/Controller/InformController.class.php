<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/5
 * Time: 16:32
 */
namespace Repair\Controller;

use Think\Controller;

class InformController extends BaseController
{
    protected $inform_user_model;
    protected $policy_app_model;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    private function initialize()
    {
        $this->inform_user_model = D('inform_user');
        $this->policy_app_model  = D('policy_app');
    }

    /**
     *工单消息
     */
    public function order_inform()
    {
        $map['status'] = 2;
        $map['type']   = 3;
        $order_inform = $this->inform_user_model->field('id,title,create_time,describe')->where($map)->select();
        for($i=0;$i<count($order_inform);$i++){
            $order_inform[$i]['create_time'] = date("Y-m-d H:i:s",$order_inform[$i]['create_time']);
            $param = '/index.php?g=Repair&m=Yin&a=index&id='.$order_inform[$i]['id'];
            $param = $this->geturl($param);
            $order_inform[$i]['url'] = $param ;
        }
        exit($this->returnApiSuccess($order_inform));
    }
    /**
     *交易消息
     */
    public function jiaoyi_inform()
    {
        $map['status'] = 2;
        $map['type']   = 4;
        $order_inform = $this->inform_user_model->field('id,title,create_time,describe')->where($map)->select();
        for($i=0;$i<count($order_inform);$i++){
            $order_inform[$i]['create_time'] = date("Y-m-d H:i:s",$order_inform[$i]['create_time']);
            $param = '/index.php?g=Repair&m=Yin&a=index&id='.$order_inform[$i]['id'];
            $param = $this->geturl($param);
            $order_inform[$i]['url'] = $param ;
        }
        exit($this->returnApiSuccess($order_inform));
    }

    /**
     * 平台政策
     */
    public function pintai(){

        $map['status'] = 2;
        $map['type']   = 6;
        $order_inform = $this->inform_user_model->field('id,title,create_time,describe')->where($map)->select();
        for($i=0;$i<count($order_inform);$i++){
            $order_inform[$i]['create_time'] = date("Y-m-d H:i:s",$order_inform[$i]['create_time']);
            $param = '/index.php?g=Repair&m=Yin&a=index&id='.$order_inform[$i]['id'];
            $param = $this->geturl($param);
            $order_inform[$i]['url'] = $param ;
        }
        exit($this->returnApiSuccess($order_inform));
    }

    /**
     *活动消息
     */
    public function huodong_inform()
    {
        $map['status'] = 2;
        $map['type']   = 5;
        $order_inform = $this->inform_user_model->field('id,title,create_time,describe')->where($map)->select();
        for($i=0;$i<count($order_inform);$i++){
            $order_inform[$i]['create_time'] = date("Y-m-d H:i:s",$order_inform[$i]['create_time']);
            $param = '/index.php?g=Repair&m=Yin&a=index&id='.$order_inform[$i]['id'];
            $param = $this->geturl($param);
            $order_inform[$i]['url'] = $param ;
        }
        exit($this->returnApiSuccess($order_inform));
    }

    /**
     * 接单必读
     */
    public function taking_order(){
        $map['type_name'] = '接单必读';
        $map['status']    = '2';
        $one_type_name = $this->policy_app_model->where($map)->group('one_type_name')->field('one_type_name')->select();

        for($i=0;$i<count($one_type_name);$i++){
            $map_two['one_type_name'] = $one_type_name[$i]['one_type_name'];
            $data[$i]['title']  = $one_type_name[$i]['one_type_name'];
            $two_type_name  = $this->policy_app_model->where($map_two)->field('id,two_type_name')->select();
            for($j=0;$j<count($two_type_name);$j++){
                $param = '/index.php?g=Repair&m=Yin&a=Toread&id='.$two_type_name[$j]['id'];
                $param = $this->geturl($param);

                $data[$i]['two_type_name'][$j]['url'] = $param;
                $data[$i]['two_type_name'][$j]['name']= $two_type_name[$j]['two_type_name'];
            }

        }


        exit($this->returnApiSuccess($data));
    }

    /**
     * 版本号
     */
    public function judge_verson(){

        $verson_number  = I('get.verson_number');
        $number = D('verson_number')->where(array('app_type'=>4))->order('create_time desc')->field('verson_number,verson_content')->find();

        if($number['verson_number'] == $verson_number ) {
            $data['status'] = 1;
            $data['content']= '';
        }else if($number['verson_number'] < $verson_number){
            $data['status'] = 3;
            $data['content']= "当前版本号不存在";
        }else{
            $data['status'] = 2;
            $data['content']= $number['verson_content'];
        }
        exit($this->returnApiSuccess($data));
    }

}