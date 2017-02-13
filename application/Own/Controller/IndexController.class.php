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
class IndexController extends BaseController{
    protected $link_model;
    protected $pro_pinlei_model;

    public function __construct()
    {
        parent::__construct();
        $this->link_model = D('links');
        $this->pro_pinlei_model = D('pro_pinlei');
    }


    public function banner(){

        $link = $this->link_model
                    ->where(array('type'=>10))
                    ->limit(3)
                    ->order("link_id desc")
                    ->field('link_image')
                    ->select();


        foreach($link as $value){
            $a[] =   $this->geturl($value['link_image']);
        }
        $owm  = D('wallet_admin')->field('own')->find();

        $data['data'] = $a;
        $data['price']= $owm['own'];
       exit($this->returnApiSuccess($data));
    }

    /**
     * 家电维修
     */
    public function appliance_repair(){
        $list = $this->pro_pinlei_model
                     ->order('create_time desc')
                     ->field('image,pro_pinlei')
                     ->select();

        foreach($list as $v){
            $v['image'] = $this->geturl($v['image']);
            $data[]     = $v;
        }

        exit($this->returnApiSuccess($data));
    }
}