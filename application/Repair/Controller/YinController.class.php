<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/5
 * Time: 18:25
 */
namespace Repair\Controller;
use Think\Controller;
class YinController extends BaseController{
    public function index(){
        $map['id'] = I('id');
        $inform_user = D('inform_user')->where($map)->field('content,title')->find();

        $inform_user['content'] =  html_entity_decode($inform_user['content']);
        $this->assign('inform_user',$inform_user);
        $this->display('inform_detail');
    }
    public function Toread(){
        $map['id']   = I('id');
        $inform_user = D('policy_app')->where($map)->field('content,title')->find();

        $inform_user['content'] =  html_entity_decode($inform_user['content']);
        $this->assign('inform_user',$inform_user);
        $this->display('inform_detail');
    }
    public function product(){
        $map_pin['id'] = I('id');
        $user_id       = I('user_id');
        $pinlei =  D('pro_pinlei')->where($map_pin)->field('pro_pinlei')->find();

        $map['pro_pinlei'] = $pinlei['pro_pinlei'];
        $product = D('pro_price')->where($map)->field('product,pro_pinlei')->select();
        $product = array_unique_fb($product);
        for($i=0;$i<count($product);$i++){
            $product[$i]['product'] = str_replace('/','|',$product[$i]['0']);

            $product[$i]['product'] = urlencode( $product[$i]['product']);
            $product[$i]['leibie'] = urlencode($product[$i]['1']);
        }

        $this->assign('user_id',$user_id);
        $this->assign('product',$product);
        $this->assign('pro_pinlei',$product['pro_pinlei']);
        $this->display('pro_product');

    }
    public function leibie(){
        $user_id = I('user_id');
        $map['product']   =  str_replace('|','/',I('product'));

        $map['pro_pinlei'] = I('pro_pinlei');
        $yangshi = D('pro_price_detail')->where($map)->group('pro_property')->field('pro_property')->select();

        $property= D('pro_price')->where($map)->field('property_name_one,property_name_two,property_name_three,property_name_four')->find();
        $property[0] = $property['property_name_one'];
        $property[1] = $property['property_name_two'];
        $property[2] = $property['property_name_three'];
        $property[3] = $property['property_name_four'];
        $property= array_filter($property,create_function('$v','return !empty($v);'));
        for($i=0;$i<count($yangshi);$i++){
            $yangshi[$i]['name']=  explode(",",$yangshi[$i]['pro_property']);
            $yangshi[$i]['zz']  =  $property;
            for($j=0;$j<count($yangshi[$i]['name']);$j++){
                $yangshi[$i]['name'][$j] = $yangshi[$i]['zz'][$j].' : '.$yangshi[$i]['name'][$j];
            }
            $yangshi[$i]['pro_property_param'] = str_replace('/','|',$yangshi[$i]['pro_property']);

            $yangshi[$i]['pro_property_param'] = urlencode($yangshi[$i]['pro_property_param']);
        }
            $pro_pinlei_param = str_replace('/','|',I('pro_pinlei'));
            $pro_pinlei_param = urlencode($pro_pinlei_param);
            $product_param = str_replace('/','|',I('product'));
            $product_param = urlencode($product_param);


        $this->assign('user_id',$user_id);
        $this->assign('pro_pinlei',I('pro_pinlei'));
        $this->assign('pro_pinlei_param',$pro_pinlei_param);
        $this->assign('product_param',$product_param);
        $this->assign('product',str_replace('|','/',I('product')));
        $this->assign('yangshi',$yangshi);
        $this->display('leibie');
    }

    public function repair_type(){
        $user_id = I('user_id');
        $product    = str_replace('|','/',I('product'));
        $pro_pinlei =  str_replace('|','/',I('pro_pinlei'));
        $property   = I('property');

        $property   =  str_replace('|','/',$property);
        $map['product']    =   $product;
        $map['pro_pinlei']  =   $pro_pinlei;
        $map['pro_property']    =   $property;
        $yangshi = D('pro_price_detail')->where($map)->field('id,order_type,service_project')->select();
        $this->assign('user_id',$user_id);
        $this->assign('pro_pinlei',$pro_pinlei);
        $this->assign('product',$product);
        $this->assign('yangshi',$yangshi);
        $this->display('repair_type');
    }
    public function price(){
        $id = I('id');
        $user_id = $user_id = I('user_id');
        $map['id'] = $id;
        $user_repairer = D('user_repairer')->where(array('user_id'=>$user_id))->field('type,proportion')->find();
        $wallet_admin  = D('wallet_admin')->field('no_ser_proportion,proportion')->find();


        $yangshi = D('pro_price_detail')->where($map)->field('service_content,pro_price,service_project')->find();

        //直属成员
        if($user_repairer['type'] == 1){
            $yangshi['pro_price'] = sprintf("%.2f", $yangshi['pro_price']*(1-$wallet_admin['proportion']));
        }
        //附属成员
        if($user_repairer['type'] == 2 ){
            $yangshi['pro_price'] = round($yangshi['pro_price']*(1-$wallet_admin['proportion'])*( 1 - $user_repairer['proportion']));
        }
        //申请中成员 或者 无服务中心成员
        if($user_repairer['type'] == 3 || $user_repairer['type'] == 4 || empty($user_repairer['type'])){
            $yangshi['pro_price'] = round($yangshi['pro_price']*(1-$wallet_admin['no_ser_proportion']));
        }

        $this->assign('yangshi',$yangshi);
        $this->display('price');
    }

    public function connect(){
        $map['type']   = 6;
        $map['status'] = 2;
        $connect = D('policy')->where($map)->field('content')->order('create_time')->find();
        $connect['content'] =  html_entity_decode($connect['content']);
        $this->assign('connect',$connect);
        $this->display('connect');
    }
    public function Agreement(){
        $map['type']   = 7;
        $map['status'] = 2;
        $connect = D('policy')->where($map)->field('content')->order('create_time')->find();
        $connect['content'] =  html_entity_decode($connect['content']);
        $this->assign('connect',$connect);
        $this->display('connect');
    }

}