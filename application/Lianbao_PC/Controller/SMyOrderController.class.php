<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/17
 * Time: 17:08
 */
namespace Lianbao_PC\Controller;
use Think\Controller;
class SMyOrderController extends ServiceBaseController {
    protected $user_service_model;
    protected $id;
    protected $order_user_model;
    protected $order_pro_model;
    protected $order_repair_model;
    protected $order_model;
    protected $user_model;
    protected $user_shop_model;
    protected $order_add_service_model;
    protected $pro_parts_model;
    protected $pro_parts_price_model;
    protected $pro_price_model;
    protected $user_repairer_model;
    protected $order_logistics_model;
    protected $shot_message_model;
    protected $pro_price_detail_model;

    public function __construct(){
        parent::__construct();
        $this->initialize();
    }
    /*
    *初始化数据
    */
    private function initialize() {
        $this->user_model               =  D('user');
        $this->pro_price_detail_model   =  D('pro_price_detail');
        $this->user_repairer_model      =  D('user_repairer');
        $this->pro_price_model          =  D('pro_price');
        $this->pro_parts_price_model    =  D('pro_parts_price');
        $this->pro_parts_model          =  D('ProPrats');
        $this->order_add_service_model  =  D('order_add_service');
        $this->user_shop_model          =  D('user_shop');
        $this->order_pro_model          =  D('order_pro');
        $this->user_service_model       =  D('user_service');
        $this->order_user_model         =  D('order_user');
        $this->order_repair_model       =  D('order_repair');
        $this->order_model              =  D('order');
        $this->id                       =  session('user_id');
        $this->order_logistics_model    =  D('orderLogistics');
        $this->shot_message_model       =  D('shot_message');
    }
    /*
    *待指派
    *$status状态码为10;
    */
    public function assig(){
        $status='10';

        $this->order_list($status);
        $this->display('assign');
    }

    /**
     * 待指派详情页
     */
    public function assig_detail(){
        $this->gaipai_shifu();
        $order_number = I('get.order_number');
        $data = $this->detail($order_number);
        $this->assign('detail',$data);
        $this->display('assign_detail');
    }
    /*
    *待预约
    *$status状态码为9;
    */
    public function wait_send(){
        $this->order_list($status='9');
        $this->display('wait_send');

    }

    /*
    *待预约详情页;
    */
    public function wait_send_detail(){
        $time_str  = $this->time_str();
        $this->assign('time_str',$time_str);
        $order_number = I('get.order_number');
        $map['order_number'] =    $order_number;
        $real_name = D('user_repairer')->where($map)->field('real_name')->find();
        $this->assign('real_name',$real_name);
        $data = $this->detail($order_number);

        $this->gaipai_shifu();
        $this->assign('detail',$data);
        $this->display('wait_send_detail');
    }
    /*
    *预约时间
    *
    */
    public function time_str(){
        $time   = date("Y-m-d");
        $time_1 = date("Y-m-d",strtotime("+1 day"));
        $time_2 = date("Y-m-d",strtotime("+2 day"));
        $time_3 = date("Y-m-d",strtotime("+3 day"));
        $time_str ="<option selected>请选择</option>".
            "<option value=\"1\">".$time ."</option>".
            "<option value=\"2\">".$time_1 ."</option>".
            "<option value=\"3\">".$time_2 ."</option>".
            "<option value=\"4\">".$time_3 ."</option>".
            "<option value=\"5\">超过4天</option>";
        return $time_str;
    }

    /*
    *二段时间返回
    *待预约 ;
    */
    public function two_change_time(){
        $time = I('post.time');
        if($time==1){
            $time = date('His',time());
            if($time>0 && $time<120000 ){
               $data = "12:00-17:00 , 17:00-22:00";
            }
            if($time>120000 && $time<180000 ){

               $data = "17:00-22:00";
            }
        }
        if($time==2 || $time==3 || $time==4 ){
               $data = "07:00-12:00 , 12:00-17:00 , 17:00-22:00";
        }
        if($time==5){
            $data = "客户不能确定什么时候上门,预约时间不能满足,其他";
        }
        json_encode($data);
        $this->ajaxReturn($data);
    }

    /**
     * 添加预约
     */
    public function add_yuyue(){
        $order_number = I('post.order_number');
        $panduan_status = $this->order_model->where(array('order_number'=>$order_number))->field('status')->find();
        if($panduan_status['status'] == 8 || $panduan_status['status'] == 9 || $panduan_status['status'] == 10 ){


        $yuyue_time = "";
        $day_time = I('post.day_time');
        $time     = I('post.time');
        $time_o   = date("Y-m-d");
        $time_1 = date("Y-m-d",strtotime("+1 day"));
        $time_2 = date("Y-m-d",strtotime("+2 day"));
        $time_3 = date("Y-m-d",strtotime("+3 day"));
        if($day_time==1){
            $yuyue_time =$time_o;
        }
        if($day_time==2){
            $yuyue_time =$time_1;
        }
        if($day_time==3){
            $yuyue_time =$time_2;
        }
        if($day_time==4){
            $yuyue_time =$time_3;
        }
        if($day_time==5){
            $yuyue_time ="超过四天";
        }
        $map['order_number'] = $order_number;
        $status = $this->order_model->where($map)->field('status,repair_person_id')->find();
        $yuyue['yuyue_time'] = $yuyue_time.":".$time;
        $yuyue['yuyue']      = time();
        if($status['status']==8){
            $yuyue['gaiyue_reason'] = I('post.gaiyue');
            $track['content']      = "维修商改约上门时间为：". $yuyue['yuyue_time']. "改约原因为".I('post.gaiyue');
        }else{
            $track['content']      = "维修商预约上门时间为：". $yuyue['yuyue_time'];
        }

        $res_yuyue = $this->order_repair_model->where($map)->save($yuyue);
        if($status['status']!==8){
            $save['status']=8;
             $this->order_model->where($map)->save($save);
             $this->order_user_model->where($map)->save($save);

        }

        $id = session('user_id');
        $repairer = D('user_repairer')->where('user_id='.$status['repair_person_id'])->field('real_name')->find();
        $user_shop = D('user_service')->where("user_id=".$id)->field('company,rel_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time']  = time();

        $track['person']       = $user_shop['company'];
        $tra = D('order_track')->add($track);
        if($res_yuyue){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }

        }else{
            $this->ajaxReturn(3);
        }
    }

    /*
    *待收件;
    *$status状态码为11;
    */
    public function recipient(){
        $this->order_list($status=array(array('eq',11),array('eq',12), 'or'));
        $this->display('recipient');
    }
    /**
     * 待收件详情页
     */
    public function recipient_detail(){
        $add_service_str='';
        $parts_str    = '';
        $parts_str_2  = '';
        $str_3        = '';
        $pro_service_str = '';
        $tankuang_service ='';
        $parts_str_2_z2   ='';
        $order_number = I('get.order_number');
        $where['order_number'] = $order_number;
        $this->gaipai_shifu();
        $add_service  = $this->add_service_list($order_number);



        $parts_s      = $this->parts($order_number);
        $parts_z1     = $this->parts_1($order_number);
        $parts_z2     = $this->parts_2($order_number);
        $parts        = $this->parts($order_number);
        $_parts       = $this->select_parts($order_number);
        $pro_service  = $this->pro_service($order_number);
        $select_parts = '';
        for($z=0;$z<count($_parts);$z++){
            $select_parts .= '<option value="'.$_parts[$z]['parts_name'].','.$_parts[$z]['parts_price'].'">'.$_parts[$z]['parts_name'].'</option>';
        }
        for($i=0;$i<count($pro_service);$i++){
                for($j=0;$j<count($pro_service[$i]['service_content']);$j++) {
                    $pro_service_str .= '<option value="' . $pro_service[$i]['service_content'][$j] .'">'.$pro_service[$i]['service_content'][$j].'</option>';
                    $add_service_str .= '<li><input type="checkbox" name="add_service[]" value="' . $pro_service[$i]['service_content'][$j] .','.$pro_service[$i]['pro_price'] .'" />' . $pro_service[$i]['service_content'][$j] . '</li>';
                }
         }
        $data = $this->detail($order_number);
        $order_pro = D('order_pro')->where(array('order_number'=>$order_number))->field('order_type')->find();
        for($i=0;$i<count($parts_s);$i++){

            if( $parts_s[$i]['type'] == 1 && $order_pro['order_type'] == 1 ) $parts_s[$i]['parts_price'] = 0;

            $parts_str  .=          "<tr height='100'>".
                "<td>跟换".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_price']."</td>".
                "<td>".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_count']."</td>".
                "<td><a href=\"javascript:;\" id=\"pjimg_".$i."\"><img src=\"".$parts[$i]['parts_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "<td><a href=\"javascript:;\" id=\"zjimg_".$i."\"><img src=\"".$parts[$i]['all_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "</tr>";
        }

        for($j=0;$j<count($add_service);$j++){
            $str_3    .=  "<p>".$add_service[$j]."</p>";
            $tankuang_service .=   '<li>'.$add_service[$j].'</li>';
        }

        for($i=0;$i<count($parts_z1);$i++){
            $parts_str_2  .=         "<div class=\"vender_show\"><a href=\"javascript:;\" id=\"vender_img_".$i."\">".
                                    "<img src=\"".$parts_z1[$i]['parts_picture']."\" /></a>".
                                    "<span>".$parts_z1[$i]['parts_name']."</span>".
                                    "<span>数量：".$parts_z1[$i]['parts_count']."</span>".
                                    "<b><a href=\"javascript:;\" id=\"vender_edit_".$i."\">编辑</a>".
                                    "<a href=\"".U('Lianbao_PC/SMyOrder/del_parts',array('id'=>$parts_z1[$i]['id']))."\" id=\"vender_dale\">删除</a></b></div>";
        }
        for($i=0;$i<count($parts_z2);$i++){
            $parts_str_2_z2  .=         "<div class=\"vender_show\"><a href=\"javascript:;\" id=\"#vender_img_z2_".$i."\">".
                "<img src=\"".$parts_z2[$i]['parts_picture']."\" /></a>".
                "<span>".$parts_z2[$i]['parts_name']."</span>".
                "<span>数量：".$parts_z2[$i]['parts_count']."</span>".
                "<b><a href=\"javascript:;\" id=\"vender_edit_z2_".$i."\">编辑</a>".
                "<a href=\"".U('Lianbao_PC/SMyOrder/del_parts',array('id'=>$parts_z2[$i]['id']))."\" id=\"vender_dale\">删除</a></b></div>";
        }
        $str_1 =        "<div class=\"vender\"><h1><span>".
                       "<a href=\"javascript:;\" id=\"vender_show\">添加配件</a></span></h1>";
        $str_2 =       "<h1><span><a href=\"javascript:;\" id=\"vender_list\">添加或修改服务项</a></span></h1>".
                       "<div class=\"vender_list\">";
        $str_4 =       " </div></div>";

        $str_1_z2 =   "<div class=\"vender\"><h1><span><a href=\"javascript:;\" id=\"vender_show_oneself\">添加配件</a></span></h1>";
        $str_2_z2 =   "<h1><span><a href=\"javascript:;\" id=\"vender_list_oneself\">添加或修改服务项</a></span></h1></div>";



        $str_add_service =  $str_1.$parts_str_2.$str_2.$str_3.$str_4;
        $str_add_service_z2 =  $str_1_z2.$parts_str_2_z2.$str_2_z2;


        $order_pro    = $this->order_pro_model->where($where)->field('pro_xinhao,pro_product')->find();
        $judge_status = $this->order_model->where(array('order_number'=>I('order_number')))->field('status')->find();
        if($judge_status['status']==11) $judge_status['status'] = "待收件";
        if($judge_status['status']==12) $judge_status['status'] = "待寄件";

        $this->assign('judge_status',$judge_status);
        $this->assign('add_service_str',$add_service_str);
        $this->assign('pro_service_str',$pro_service_str);
        $this->assign('order_pro',$order_pro);
        $this->assign('parts_z1',$parts_z1);
        $this->assign('parts_z2',$parts_z2);
        $this->assign('tankuang_service',$tankuang_service);
        $this->assign('select_parts',$select_parts);
        $this->assign('str_add_service',$str_add_service);
        $this->assign('str_add_service_z2',$str_add_service_z2);
        $this->assign('num_parts',$parts);
        $this->assign('parts',$parts_str);
        $this->assign('detail',$data);
        $this->assign('add_service',$add_service);
        $this->display('recipient_detail');
    }

    /*
    *待寄件;
    *$status状态码为12;
    */
    public function send(){
        $this->order_list($status=array(array('eq',11),array('eq',12), 'or'));
        $this->display('send');
    }
    /**
     * 待寄件详情页
     */
    public function send_detail(){

        $order_number = I('get.order_number');

        $map['order_number'] = $order_number;
        $is_wuliu = $this->order_logistics_model->where($map)->find();
        if(empty($is_wuliu['id'])){

            $whliu['status']      = "1";
            $whliu['status_name'] = "待返件";

        }else{
            $whliu['status']      = "0";
            $whliu['status_name'] = "已返件";
        }


        $this->assign('wuliu',$whliu);
        $parts        = $this->parts($order_number);
        $data = $this->detail($order_number);
        $parts_str='';
        $add_service  = $this->add_service_list($order_number);

        $order_pro = D('order_pro')->where(array('order_number'=>$order_number))->field('order_type')->find();

        for($i=0;$i<count($parts);$i++){

            if( $parts[$i]['type'] == 1 && $order_pro['order_type'] == 1 ) $parts[$i]['parts_price'] = 0;
            $parts_str  .=          "<tr height='100'>".
                "<td>跟换".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_price']."</td>".
                "<td>".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_count']."</td>".
                "<td><a href=\"javascript:;\" id=\"pjimg_".$i."\"><img src=\"".$parts[$i]['parts_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "<td><a href=\"javascript:;\" id=\"zjimg_".$i."\"><img src=\"".$parts[$i]['all_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "</tr>";
        }



        $this->assign('service',$add_service);
        $this->assign('parts',$parts_str);
        $this->assign('num_parts',$parts);
        $this->assign('detail',$data);
        $this->display('send_detail');
    }

    /**
     * 增加物流
     */
    public function add_logistics(){
        $user_id = $this->id;
        $data    =  I('post.');
        $content = "服务中心已返回配件，快递公司是".$data['logistics_com']."，快递单号为".$data['logistics_danhao']."，快递价格为".$data['logistics_money']."，支付方式为".$data['pay_fangshi'];
        $add_le['create_time'] = time();
        $add_le['order_number']= $data['order_number'];
        $add_le['content']     = $content;
        $user_shop = $this->user_service_model->where(array('user_id'=>$user_id))->field('company')->find();
        $add_le['person']      = $user_shop['company'];
        $result = D('leaving_message')->add($add_le);
        if($this->order_logistics_model->create()){
            $result = $this->order_logistics_model->add();
            if($result){
                $this->success('修改成功',U('Lianbao_PC/SMyOrder/recipient'));
            }else{
                $this->error('修改失败');
            }
        }else{
            exit($this->order_logistics_model->getError());
        }

    }


    /*
   *待服务;
   *$status状态码为8;
   */
    public function service(){
        $status =  array(array('eq',8),array('eq',16), 'or');
        $this->order_list($status);
        $this->display('service');
    }
    /**
     *待服务详情页
     */

    public function judge_service(){
        $order_number = I('get.order_number');
        $map['order_number'] = $order_number;
        $result = $this->order_model->where($map)->field('status,source')->find();
        $this->assign('source',$result['source']);
        if($result['status']==8)  $this->service_detail($order_number);
        if($result['status']==16) $this->serviceing_detail($order_number);
    }


    public function service_detail($order_number){
        $time_str  = $this->time_str();
        $this->assign('time_str',$time_str);
        $data = $this->detail($order_number);
        $this->assign('detail',$data);
        $this->gaipai_shifu();
        $this->display('service_detail');
    }

    public function serviceing_detail($order_number){

        $add_service_str='';
        $parts_str    = '';
        $parts_str_2  = '';
        $str_3        = '';
        $pro_service_str = '';
        $tankuang_service ='';
        $parts_str_2_z2   ='';
        $order_number = I('get.order_number');
        $where['order_number'] = $order_number;
        $this->gaipai_shifu();
        $add_service  = $this->add_service_list($order_number);
        $parts_s      = $this->parts($order_number);
        $parts_z1     = $this->parts_1($order_number);
        $parts_z2     = $this->parts_2($order_number);
        $parts        = $this->parts($order_number);
        $_parts       = $this->select_parts($order_number);
        $pro_service  = $this->pro_service($order_number);
        $select_parts = '';

        for($z=0;$z<count($_parts);$z++){
            $select_parts .= '<option value="'.$_parts[$z]['parts_name'].','.$_parts[$z]['parts_price'].'">'.$_parts[$z]['parts_name'].'</option>';
        }


        for($i=0;$i<count($pro_service);$i++){
            for($j=0;$j<count($pro_service[$i]['service_content']);$j++) {
                $pro_service_str .= '<option value="' . $pro_service[$i]['service_content'][$j] .'">'.$pro_service[$i]['service_content'][$j].'</option>';
                $add_service_str .= '<li><input type="checkbox" name="add_service[]" value="' . $pro_service[$i]['service_content'][$j] .','.$pro_service[$i]['pro_price'] .'" />' . $pro_service[$i]['service_content'][$j] . '</li>';
            }
        }


        $data = $this->detail($order_number);



        $order_pro = D('order_pro')->where(array('order_number'=>$order_number))->field('order_type')->find();


        for($i=0;$i<count($parts_s);$i++){

            if( $parts_s[$i]['type'] == 1  && $order_pro[$i]['order_type'] == 1) $parts_s[$i]['parts_price'] = 0;


            $parts_str  .=          "<tr height='100'>".
                "<td>跟换".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_price']."</td>".
                "<td>".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_count']."</td>".
                "<td><a href=\"javascript:;\" id=\"pjimg_".$i."\"><img src=\"".$parts[$i]['parts_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "<td><a href=\"javascript:;\" id=\"zjimg_".$i."\"><img src=\"".$parts[$i]['all_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "</tr>";
        }
        for($j=0;$j<count($add_service);$j++){
            $str_3    .=  "<p>".$add_service[$j]."</p>";
            $tankuang_service .=   '<li><input type="radio" />'.$add_service[$j].'</li>';
        }
        for($i=0;$i<count($parts_z1);$i++){
            $parts_str_2  .=         "<div class=\"vender_show\"><a href=\"javascript:;\" id=\"vender_img_".$i."\">".
                "<img src=\"".$parts_z1[$i]['parts_picture']."\" /></a>".
                "<span>".$parts_z1[$i]['parts_name']."</span>".
                "<span>数量：".$parts_z1[$i]['parts_count']."</span>".
                "<b><a href=\"javascript:;\" id=\"vender_edit_".$i."\">编辑</a>".
                "<a href=\"".U('Lianbao_PC/SMyOrder/del_parts',array('id'=>$parts_z1[$i]['id']))."\" id=\"vender_dale\">删除</a></b></div>";
        }
        for($i=0;$i<count($parts_z2);$i++){
            $parts_str_2_z2  .=         "<div class=\"vender_show\"><a href=\"javascript:;\" id=\"#vender_img_z2_".$i."\">".
                "<img src=\"".$parts_z2[$i]['parts_picture']."\" /></a>".
                "<span>".$parts_z2[$i]['parts_name']."</span>".
                "<span>数量：".$parts_z2[$i]['parts_count']."</span>".
                "<b><a href=\"javascript:;\" id=\"vender_edit_z2_".$i."\">编辑</a>".
                "<a href=\"".U('Lianbao_PC/SMyOrder/del_parts',array('id'=>$parts_z2[$i]['id']))."\" id=\"vender_dale\">删除</a></b></div>";
        }
        $str_1 =        "<div class=\"vender\"><h1><span>".
            "<a href=\"javascript:;\" id=\"vender_show\">添加配件</a></span></h1>";
        $str_2 =       "<h1><span><a href=\"javascript:;\" id=\"vender_list\">添加或修改服务项</a></span></h1>".
            "<div class=\"vender_list\">";
        $str_4 =       " </div></div>";

        $str_1_z2 =   "<div class=\"vender\"><h1><span><a href=\"javascript:;\" id=\"vender_show_oneself\">添加配件</a></span></h1>";
        $str_2_z2 =   "<h1><span><a href=\"javascript:;\" id=\"vender_list_oneself\">添加或修改服务项</a></span></h1></div>";



        $str_add_service =  $str_1.$parts_str_2.$str_2.$str_3.$str_4;
        $str_add_service_z2 =  $str_1_z2.$parts_str_2_z2.$str_2_z2;

        $order_pro =  $this->order_pro_model->where($where)->field('pro_xinhao,order_type,pro_product')->find();

        $this->assign('add_service_str',$add_service_str);
        $this->assign('pro_service_str',$pro_service_str);
        $this->assign('order_pro',$order_pro);
        $this->assign('parts_z1',$parts_z1);
        $this->assign('parts_z2',$parts_z2);
        $this->assign('tankuang_service',$tankuang_service);
        $this->assign('select_parts',$select_parts);
        $this->assign('str_add_service',$str_add_service);
        $this->assign('str_add_service_z2',$str_add_service_z2);
        $this->assign('num_parts',$parts);
        $this->assign('parts',$parts_str);
        $this->assign('detail',$data);
        $this->assign('add_service',$add_service);
        $this->display('serviceing_detail');
    }

    /*
    *待收款;
    *$status状态码为13;
    */
    public function gathering(){
        $status =  array(array('eq',13),array('eq',20), 'or');
        $this->order_list($status);
        $this->display('gathering');
    }

    /**
     *待收款详情页
     */

    public function gathering_detail(){
        $order_number = I('get.order_number');
        $parts        = $this->parts($order_number);
        $data = $this->detail($order_number);
        $parts_str='';
        $add_service  = $this->add_service_list($order_number);

        $order_pro = D('order_pro')->where(array('order_number'=>$order_number))->field('order_type')->find();

        for($i=0;$i<count($parts);$i++){

            if( $parts[$i]['type'] == 1 && $order_pro['order_type'] == 1) $parts[$i]['parts_price'] = 0;

            $parts_str  .=          "<tr height='100'>".
                "<td>跟换".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_price']."</td>".
                "<td>".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_count']."</td>".
                "<td><a href=\"javascript:;\" id=\"pjimg_".$i."\"><img src=\"".$parts[$i]['parts_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "<td><a href=\"javascript:;\" id=\"zjimg_".$i."\"><img src=\"".$parts[$i]['all_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "</tr>";
        }
        $status = D('order')->where(array('order_number'=>$order_number))->field('status')->find();
        if($status['status'] == 13 )  $status['status']  = "待收款";
        if($status['status'] == 20 )  $status['status']  = "商家待收件";
        $this->assign('status',$status);
        $this->assign('service',$add_service);
        $this->assign('parts',$parts_str);
        $this->assign('num_parts',$parts);
        $this->assign('detail',$data);
        $this->display('gathering_detail');
    }
    /*
    *已取消;
    *$status状态码为14;
    */
    public function cancel(){
        $this->order_list($status='14');
        $this->display('cancel');
    }
    /**
     * 已取消详情页
     */

    public function cancel_detail(){
        $order_number = I('get.order_number');
        $data = $this->detail($order_number);
        $this->assign('detail',$data);
        $this->display('cancel_detail');
    }


    public function complaint(){
        $user_id = session('user_id');
        $map['repair_service_id'] = $user_id;
        $number = D('complaint')->where($map)->field('order_number')->select();
        for($j=0;$j<count($number);$j++){
            $map_number['order_number'] = $number[$j]['order_number'];
            $z = $this->order_model->where($map_number)->field('repair_person_id,order_type,order_number')->order('create_time desc')->find();
            $pro  = $this->order_pro_model->where(array('order_number'=>$z['order_number']))->find();
            $user = $this->order_user_model->where(array('order_number'=>$z['order_number']))->find();
            if(!empty($z)){
                $order_number[$j] = $z;
                $order_pro[$j]    = $pro;
                $order_user[$j]   = $user;
            }
        }
        sort($order_number);
        sort($order_pro);
        sort($order_user);
        for($i=0;$i<count($order_number);$i++){
            $data[$i]['pro_name'] = $order_pro[$i]['pro_name'];
            $data[$i]['pro_price'] = $order_pro[$i]['pro_price'];
            $data[$i]['order_number'] = $order_pro[$i]['order_number'];
            $data[$i]['user_name'] = $order_user[$i]['user_name'];
            $data[$i]['user_phone'] = $order_user[$i]['user_phone'];
            $data[$i]['user_city'] = $order_user[$i]['user_city'];
            $zhixingren = $this->user_service_model->where(array('user_id'=>session('user_id')))->field('rel_name')->find();
            $data[$i]['zhixingren'] = $zhixingren['rel_name'];
            if($order_user[$i]['status']== 8)  $data[$i]['status'] = "待服务";
            if($order_user[$i]['status']== 16)  $data[$i]['status']= "正在服务中";
            if($order_user[$i]['status']== 9)  $data[$i]['status'] = "待预约";
            if($order_user[$i]['status']== 10)  $data[$i]['status'] = "待指派";
            if($order_user[$i]['status']== 11)  $data[$i]['status'] = "待收件";
            if($order_user[$i]['status']== 12)  $data[$i]['status'] = "待寄件";
            if($order_user[$i]['status']== 13)  $data[$i]['status'] = "待收款";
        }
           $this->assign('list',$data);
           $this->display('complaint');
    }





    public function complaint_detail(){
        $order_number = I('get.order_number');
        $parts        = $this->parts($order_number);
        $data = $this->detail($order_number);
        $parts_str='';
        $add_service  = $this->add_service_list($order_number);

        $order_pro = D('order_pro')->where(array('order_number'=>$order_number))->field('order_type')->find();
        for($i=0;$i<count($parts);$i++){

            if( $parts[$i]['type'] == 1 && $order_pro[$i]['order_type'] == 1 ) $parts[$i]['parts_price'] = 0;
            $parts_str  .=          "<tr height='100'>".
                "<td>跟换".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_price']."</td>".
                "<td>".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_count']."</td>".
                "<td><a href=\"javascript:;\" id=\"pjimg_".$i."\"><img src=\"".$parts[$i]['parts_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "<td><a href=\"javascript:;\" id=\"zjimg_".$i."\"><img src=\"".$parts[$i]['all_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "</tr>";
        }
        $this->assign('service',$add_service);
        $this->assign('parts',$parts_str);
        $this->assign('num_parts',$parts);
        $this->assign('detail',$data);
        $this->display('complaint_detail');

    }


    /*
    *已完结;
    *$status状态码为7;
    */
    public function end(){
        $this->order_list($status='7');
        $this->display('end');
    }
    public function end_detail(){
        $order_number = I('order_number');
        $this->income($order_number);
        $parts        = $this->parts($order_number);
        $data = $this->detail($order_number);
        $parts_str='';
        $add_service  = $this->add_service_list($order_number);
        $order_pro = D('order_pro')->where(array('order_number'=>$order_number))->field('order_type')->find();

        for($i=0;$i<count($parts);$i++){
            if( $parts[$i]['type'] == 1 && $order_pro['order_type'] == 1 ) $parts[$i]['parts_price'] = 0;
            $parts_str  .=          "<tr height='100'>".
                "<td>跟换".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_price']."</td>".
                "<td>".$parts[$i]['parts_name']."</td>".
                "<td>".$parts[$i]['parts_count']."</td>".
                "<td><a href=\"javascript:;\" id=\"pjimg_".$i."\"><img src=\"".$parts[$i]['parts_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "<td><a href=\"javascript:;\" id=\"zjimg_".$i."\"><img src=\"".$parts[$i]['all_picture']."\"> <i class=\"fa fa-search-plus\" ></i></a></td>".
                "</tr>";
        }
        $order_pic = $this->order_model->where(array('order_number'=>I('order_number')))->field('end_picture_1,end_picture_2,wanjiema,buying_picture')->find();
        $this->assign('order_pic',$order_pic);
        $this->assign('service',$add_service);
        $this->assign('parts',$parts_str);
        $this->assign('num_parts',$parts);
        $this->assign('detail',$data);
        $this->display('end_detail');
    }
    /**
     * 工单完结
     */
    public function end_order(){


        $end_number   = I('post.end_number');
        $order_number = I('post.order_number');
        $map['order_number'] = $order_number;
        $where['username'] = $order_number;

        $code = $this->shot_message_model->where($where)->field('code,end_time')->find();
        $_mess_number = $code['code'];
        $end_time     = time();
        $baoxiu_type  =  $this->order_pro_model->where(array('order_number'=>$order_number))->field('baoxiu_type')->find();

        $re_service_price = $this->order_model->where($map)->field('service_price')->find();
        if(empty($re_service_price['service_price'])){

            $this->error('完结失败，请添加服务项，服务项目不能为空');

        }

        $this->upload_end($order_number);

        if($end_number == $_mess_number){
                if(time()<$code['end_time']){
                $re_parts_status = $this->order_model->where($map)->field('re_parts_status')->find();
                    if($re_parts_status['re_parts_status'] == 1 ) {

                        $res_end = $this->order_repair_model->where($map)->setField('confirm_time', $end_time);
                        $res_order = $this->order_model->where($map)->setField('status', 20);
                        $res_user = $this->order_user_model->where($map)->setField('status', 20);

                    }else{
                        $res_end = $this->order_repair_model->where($map)->setField('end_time', $end_time);
                        $res_order = $this->order_model->where($map)->setField('status', 13);
                        $res_user = $this->order_user_model->where($map)->setField('status', 13);
                    }

                if($res_end && $res_order && $res_user ){

                    $id = session('user_id');
                    $status = $this->order_model->where($map)->field('repair_person_id')->find();
                    $repairer = D('user_repairer')->where(array('user_id'=>$status['repair_person_id']))->field('real_name')->find();
                    $user_shop = D('user_service')->where(array('user_id'=>$id))->field('company,rel_name')->find();
                    $track['order_number'] = $order_number;
                    $track['create_time']  = time();
                    $track['content']      = "维修商完成服务";
                    $track['person']       = $user_shop['rel_name'];
                    $tra = D('order_track')->add($track);

                    if($baoxiu_type['baoxiu_type'] == 2 ){
                        $this->examine_price($order_number);
                    }else{
                        $this->success('完结成功',U('Lianbao_PC/SMyOrder/assig'));
                    }

                }else{
                    $this->error('订单完结失败');
                }
            }else{
                    $this->error('验证码失效');
                }
        }else{
            $this->error('请输入正确的订单完结验证码');
        }
    }

    /**
     * @param $field
     * @return string
     * 上传图片
     */
    private function upload_end($order_number)
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();
        if($info['end_picture_1']['savename']){
            $data['end_picture_1'] = 'data/upload/'.$info['end_picture_1']["savepath"].$info['end_picture_1']["savename"];
        }
        if($info['end_picture_2']['savename']){
            $data['end_picture_2'] = 'data/upload/'.$info['end_picture_2']["savepath"].$info['end_picture_2']["savename"];
        }
        if($info['wanjiema']['savename']){
            $data['wanjiema'] = 'data/upload/'.$info['wanjiema']["savepath"].$info['wanjiema']["savename"];
        }
        if($info['buying_picture']['savename']){
            $data['buying_picture'] = 'data/upload/'.$info['buying_picture']["savepath"].$info['buying_picture']["savename"];
        }
        $this->order_model->where(array('order_number'=>$order_number))->save($data);

    }


    /**
     * 改派师傅显示
     */
    public function gaipai_shifu(){
        $str     = '';
        $user_id = $this->id;

        $map['parent_id'] = $user_id;
        $map['type']      = array(array('eq',1),array('eq',2),'or');
        $sql = $this->user_repairer_model->where($map)->select();


        for($i=0;$i<count($sql);$i++){
            if($sql[$i]['type']==1) $sql[$i]['type']='直属成员';
            if($sql[$i]['type']==2) $sql[$i]['type']='附属成员';
            $where['repair_person_id'] = $sql[$i]['user_id'];
            $username = D('user')->where(array('id'=>$sql[$i]['user_id']))->field('username')->find();
            $sql[$i]['phone'] = $username['username'];
            $where['status'] = array(array('eq',8),array('eq',9),array('eq',10),array('eq',11),array('eq',12),array('eq',16),'or');
            $order = $this->order_model->where($where)->count();

            $str .= '<li><img src="/public/Service/images/worker.png"><b>'.$sql[$i]['real_name'].'</b><span>'.$sql[$i]['phone'].'</span><wq>'.$sql[$i]['city'].'</wq><span>'.$sql[$i]['type'].'</span><span>进行中订单(<i>'.$order.'</i>)</span><span class="black"> <input type="hidden" id="val_'.$i.'" value="'.$sql[$i]['user_id'].'"><a href="javascript:;" name="'.$sql[$i]['user_id'].'" id="sdad_'.$i.'">指派师傅</a></span></li>';
        }
        $this->assign('chengyuan',$sql);
        $this->assign('chengyuan_str',$str);

    }
    /**
     * 改派师傅
     */
    public function gaipai(){

        $id = session('user_id');
        $user_id = I('post.user_id');
        $order_number = I('post.order_number');
        $map['order_number'] = $order_number;
        $repairer = D('user_repairer')->where('user_id='.$user_id)->field('real_name')->find();
        $status  = $this->order_user_model->where($map)->field('status')->find();
        $save['assign_end_time'] = strtotime("+1 day");
        $save['assign_time'] = time();

        $username = $this->user_model->where(array('id'=>$user_id))->field('username')->find();
        $content = "【驰达家维】：您有新订单，请登录师傅端查看;";
        if($status['status']!==9 && $status['status']!==8){
           
            $save['status'] = '9';
            $res =$this->order_user_model->where($map)->save($save);
            $user_shop = D('user_service')->where("user_id=".$id)->field('company,rel_name')->find();
            $track['order_number'] = $order_number;
            $track['create_time']  = time();
            $track['content']      = "派单给".$repairer['real_name'];
            $track['person']       = $repairer['real_name'];
            $tra = D('order_track')->add($track);
            $result = $this->order_repair_model->where($map)->save($save);
            $save['repair_person_id'] = $user_id;
            $result = $this->order_model->where($map)->save($save);
            
        }else{


            $result = $this->order_repair_model->where($map)->save($save);
            $save['repair_person_id'] = $user_id;
            $result = $this->order_model->where($map)->save($save);
            $user_shop = D('user_service')->where("user_id=".$id)->field('company,rel_name')->find();
            $track['order_number'] = $order_number;
            $track['create_time']  = time();
            $track['content']      = "派单给".$repairer['real_name'];
            $track['person']       = $repairer['real_name'];
            $tra = D('order_track')->add($track);
            $result = $this->order_repair_model->where($map)->save($save);
            $save['repair_person_id'] = $user_id;
            $result = $this->order_model->where($map)->save($save);


        }

        if($result){
            $this->send_info_fiel($username['username'],$content);
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }


    /**
     *删除图片\
     */
    public function del_picture(){
        $map['id'] = I('post.order_number');
        if(I('post.parts')==1) $save['parts_picture'] = "";
        if(I('post.parts')==2) $save['all_picture']   = "";
        $result = $this->pro_parts_model->where($map)->save($save);
        if($result){
            $this->ajaxReturn(1);
        }
    }


    /**
     *增加配件有图片
     */
    public function add_parts(){


        $data  = I('post.');

            $array = explode(',',$data['parts_name']);
            $data['parts_name'] = $array[0];
            if($data['type'] ==1 ){
                $data['parts_price'] = $array[1];
            }

            $upload = new \Think\Upload();
            $upload->maxSize   =     3145728 ;
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
            $upload->rootPath  =     './data/upload/parts/';
            $upload->savePath  =     '';
            $info   =   $upload->upload();
            $parts_picture = 'data/upload/parts/'.$info['parts_picture']["savepath"].$info['parts_picture']["savename"];
            $all_picture   = 'data/upload/parts/'.$info['all_picture']["savepath"].$info['all_picture']["savename"];
            if(empty($info['parts_picture']))$parts_picture='';
            if(empty($info['all_picture'])) $all_picture='';
            $data['parts_picture'] = $parts_picture;
            $data['all_picture']   = $all_picture;
            $data['status']        = 2;
            if (!$this->pro_parts_model->create($data)){
                 $this->error($this->pro_parts_model->getError());
            }else{

                $map_order_price['order_number'] = I('post.order_number');
                $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();


                $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();
                $order_save['parts_price']   = $order_price['parts_price']   + $data['parts_price']*$data['parts_count'];


                //保修类型为 保内 配件类型厂家寄件
                if($baoxiu_type['baoxiu_type'] == 1  && $data['type'] ==1 ){

                    $order_save['parts_price']   = $order_price['parts_price'];

                }else{
                    $order_save['parts_price']   = $order_price['parts_price']   +  $data['parts_price']*$data['parts_count'];
                }

                $order_save['repair_price']  = $order_price['service_price'] + $order_save['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'] ;
                $this->order_model->where($map_order_price)->save($order_save);


                $result = $this->pro_parts_model->add();
                $this->order_model->where(array('order_number'=>$data['order_number']))->setField('status',12);
                $this->order_user_model->where(array('order_number'=>$data['order_number']))->setField('status',12);

                if($result){
                    $this->success('添加配件成功',U('Lianbao_PC/SMyOrder/recipient'));
                }else{
                    $this->error('添加配件失败');
                }
            }
    }

    /**
     * 删除配件
     */
    public function del_parts(){
        $map['id']    = I('id');
        $data         = $this->pro_parts_model->where($map)->field('type,parts_price,order_number,parts_count')->find();

        $map_order_price['order_number'] = $data['order_number'];
        $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();

        $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();
        $order_save['parts_price']   = $order_price['parts_price'] - $data['parts_price']*$data['parts_count'];

        if($baoxiu_type['baoxiu_type'] ==  1  && $data['type'] == 1 ){

           $order_save['parts_price']   = $order_price['parts_price'] ;

       }else{
           $order_save['parts_price']   = $order_price['parts_price'] - $data['parts_price']*$data['parts_count'];
       }


        $order_save['repair_price']  = $order_price['service_price'] + $order_save['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'];

        $this->order_model->where($map_order_price)->save($order_save);
        $result    = $this->pro_parts_model->where($map)->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    /**
     *修改配件
     */
    public function save_parts(){
        if(!empty(I('post.parts_count'))){
            $map['id'] = I('post.parts_id');
            $data =I('post.');
            $a    = explode(',',$data['parts_name']);
            $data['parts_name'] =  $a['0'];

            if($data['type'] ==1 ){
                $data['parts_price'] = $a['1'];
            }


            $order_number = $this->pro_parts_model->where($map)->field('order_number,parts_price,parts_count')->find();
            $map_order_price['order_number'] = $order_number['order_number'];
            $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();

            $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();

           if($baoxiu_type['baoxiu_type'] ==1  && $data['type'] == 1 ) {

               $order_save['parts_price']   = $order_price['parts_price'];

           }else{

               $order_save['parts_price']   = $order_price['parts_price'] - $order_number['parts_price']*$order_number['parts_count'] + $data['parts_price']*$data['parts_count'];

           }

            $order_save['repair_price']  = $order_price['service_price'] + $order_save['parts_price'] + $order_price['far_price'] + $order_price['logistics_price'];

            $this->order_model->where($map_order_price)->save($order_save);
            $result = $this->pro_parts_model->where($map)->save($data);

            if($result){

                $this->success('修改成功',U('Lianbo_PC/SMyOrder/recipient'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $this->error('修改失败，配件数量不能为空');
        }
    }


    /**
     *增加服务
     */
    public function add_service(){
        $b='';
        $price='';
        $ser  = '';
        $order_number = I('post.order_number');
        $map['order_number'] = $order_number;
        $service = I('post.add_service');
        for($i=0;$i<count($service);$i++){
            $array = explode(',',$service[$i]);
                $ser     .= $array[0].",";
                $price   += $array[1];
        }
        $ser = rtrim($ser,',');

        $map_order_price['order_number'] = I('post.order_number');
        $baoxiu_type = $this->order_pro_model->where($map_order_price)->field('baoxiu_type')->find();
        $order_price = $this->order_model->where($map_order_price)->field('service_price,parts_price,far_price,logistics_price')->find();
        $order_save['service_price'] = $price;
        if($baoxiu_type['baoxiu_type'] ==1 ){
            $order_save['repair_price']  = $price + $order_price['logistics_price'] + $order_price['far_price'];
        }

        if($baoxiu_type['baoxiu_type']==2 ){

            $order_save['repair_price']  = $price + $order_price['parts_price'] + $order_price['logistics_price'] + $order_price['far_price'];

        }

        $this->order_model->where($map)->save($order_save);
        $update['create_time'] = time();
        $update['add_service'] = $ser;
        $id = session('user_id');
        $user_shop = D('user_service')->where("user_id=".$id)->field('company,rel_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time']  = time();
        $track['content']      = "服务中心修改服务项目为".$ser;
        $track['person']       = $user_shop['company'];
        $tra = D('order_track')->add($track);

        $add_service = $this->order_add_service_model->where($map)->field('add_service')->find();
        if ($add_service['add_service']) {
            $result = $this->order_add_service_model->where($map)->save($update);
        } else {
            $update['order_number'] = $order_number;
            $result = $this->order_add_service_model->add($update);
        }
        if($result){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }
    /*
    *全部;
    */
    public function whole(){
        $map_user['user_id']   = $this->id;
        $map['user_id']        = $this->id;
        $shop_location  = $this->user_service_model
            ->where($map_user)
            ->field('shop_location')
            ->find();
        $loca['repair_service_id']  = $this->id;
        $loca['user_city'] =  $shop_location['shop_location'];
        $loca['publish_status'] = 2;
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
            $pro_name = $this->order_pro_model->where($where)->field('pro_name')->find();
            $taking_order[$i]['pro_name'] =  $pro_name['pro_name'];
            if( $taking_order[$i]['status']==10) {
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/assign_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "待指派";
            }
            if( $taking_order[$i]['status']==9){
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/order_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "待预约";
            }
            if( $taking_order[$i]['status']==11){

                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/recipient_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "待收件";
            }
            if( $taking_order[$i]['status']==12){
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/send_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "待寄件";
            }
            if( $taking_order[$i]['status']==8){
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/service_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "待服务";
            }
            if( $taking_order[$i]['status']==13){
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/gathering_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "待收款";
            }
            if( $taking_order[$i]['status']==7){
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/end_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "已完结";
            }
            if( $taking_order[$i]['status']==16){
                $taking_order[$i]['str'] =U('Lianbao_PC/SMyOrder/service_detail',array('order_number'=>$taking_order[$i]['order_number']));
                $taking_order[$i]['leixin'] = "正在服务中";
            }

        }

        $this->assign('page',$show);
        $this->assign('list',$taking_order);
        $this->display('whole');
    }

    /**
     * 取消订单
     */
    public function cancel_order(){
        $map['order_number']                = I('post.order_number');
        $save['status']                     = '2';
        $reason['cancel_order_reason']      =   I('post.order_reason');
        $reason['cancel_order_time']        = time();
        $order_number = I('post.order_number');
        $repair  = $this->order_repair_model->where($map)
            ->save($reason);
        $order_user = $this->order_user_model
            ->where($map)
            ->save($save);
        $order      = $this->order_model
            ->where($map)
            ->save($save);

        $id = session('user_id');
        $status = $this->order_model->where($map)->field('repair_person_id')->find();
        $repairer = D('user_repairer')->where('user_id='.$status['repair_person_id'])->field('real_name')->find();
        $user_shop = D('user_service')->where("user_id=".$id)->field('company,rel_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time']  = time();
        $track['content']      = "服务中心取消订单";
        $track['person']       = $user_shop['company'];

        $tra = D('order_track')->add($track);

        if( $repair && $order  && $order_user){
            $this->success('取消成功',U('Lianbao_PC/SMyOrder/wait_send'));
        }else{
            $this->error('取消失败');
        }

    }


    /*
    *调用模版
    *$status状态码;
    */
    private function order_list($status){
        $proportion = D('wallet_admin')->field('proportion')->find();
        $map_user['user_id']   = $this->id;
        $map['user_id']        = $this->id;
        $loca['a.repair_service_id'] = $this->id;
        $loca['a.status']         = $status;

        $count        = $this->order_model
            ->join('as a left join lb_order_user as b on a.order_number = b.order_number')
            ->where($loca)
            ->count();
        $Page       = new \Think\Page($count,10);
        $show       = $Page->show();
        $taking_order = $this->order_model
            ->join('as a left join lb_order_user as b on a.order_number = b.order_number')
            ->order('a.create_time DESC')
            ->where($loca)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        for($i=0;$i<count($taking_order);$i++){
            $where['order_number']  = $taking_order[$i]['order_number'];
            $real_name = $this->order_model->where($where)->field('repair_person_id,service_price,parts_price,logistics_price,far_price')->find();
            $wher_map['user_id'] = $real_name['repair_person_id'];
            $repair_name = $this->user_repairer_model->where($wher_map)->field('real_name')->find();
            $pro_name = $this->order_pro_model->where($where)->field('pro_name')->find();
            $taking_order[$i]['repair_price'] = sprintf("%.2f", $real_name['service_price']*(1-$proportion['proportion']) + $real_name['parts_price'] + $real_name['logistics_price'] + $real_name['far_price']);
            $taking_order[$i]['pro_name'] =  $pro_name['pro_name'];
            $taking_order[$i]['zhixinren']=  $repair_name['real_name'];
            if($taking_order[$i]['status']==8) $taking_order[$i]['status']='待服务';
            if($taking_order[$i]['status']==16) $taking_order[$i]['status']='服务进行中';
            if($taking_order[$i]['status']==13) $taking_order[$i]['status']='待收款';
            if($taking_order[$i]['status']==20) $taking_order[$i]['status']='商家待收件';
        }

        $this->assign('page',$show);
        $this->assign('list',$taking_order);

    }

    /**
     * @param $order_number 订单号
     * @return mixeds 调用时间函数处理剩余时间
     * 查看详情页基本模版
     */
    private function detail ($order_number){
        $proportion = D('wallet_admin')->field('proportion')->find();
        $map['order_number']= $order_number;
        $detail = $this->order_user_model->where($map)->find();

        $taking_time = $this->order_repair_model->where(array('order_number'=>$order_number))->field('taking_time')->find();

        $time   = time_difference($taking_time['taking_time']);


        $detail['day']   = $time['day'];
        $detail['hours'] = $time['hours'];
        $detail['mins']  = $time['mins'];
        $order_pro =   $this->order_pro_model
            ->where($map)
            ->field('pro_xinhao,pro_property,order_type,remarks')
            ->find();

        $detail['pro_xinhao']   = $order_pro['pro_xinhao'];
        $detail['pro_property'] = $order_pro['pro_property'];

        $detail['remarks']      = $order_pro['remarks'];
        if($order_pro['order_type']==1) $detail['baoxiu_type'] = "安装单";
        if($order_pro['order_type']==2) $detail['baoxiu_type'] = "维修单";
        if($order_pro['order_type']==3) $detail['baoxiu_type'] = "送修单";
        if($order_pro['order_type']==4) $detail['baoxiu_type'] = "清洗单";
        $map_shop['user_id'] = $detail['user_id'];
        $map_user['user_id'] = $this->id;


        $user_shop = D('links')
            ->where(array('type'=>6))
            ->field('link_description')
            ->find();

        $user_service =   $this->user_service_model->where($map_user)
            ->field('service_phone,skill_phone,company')
            ->find();

        $this->order_track($order_number);
        $order_price  = $this->order_model->where($map)->field('service_price,parts_price,logistics_price,far_price')->find();
        $order_price['repair_price']  = sprintf("%.2f", $order_price['service_price']*(1-$proportion['proportion']) + $order_price['parts_price'] + $order_price['logistics_price'] + $order_price['far_price']);
        $this->assign('order_price',$order_price);
        $this->assign('user_service',$user_service);
        $this->assign('user_shop',$user_shop);

        return($detail);
    }

    /**
     * @param $order_number
     * @return array  服务项
     */
    private function add_service_list($order_number){
        $order['order_number'] = $order_number;
        $a = $this->order_add_service_model->where($order)->find();
        $add_service = explode(',',$a['add_service']);
        return $add_service;
    }

    /**
     * 全部配件单
     * @param $order_number 订单号
     * @return mixed 返回当前订单的配件
     */
    private function parts($order_number){
        $order['order_number'] = $order_number;
        $pro_parts = $this->pro_parts_model->where($order)->select();
        return $pro_parts;
    }
    /**
     * 商家寄件配件单
     * @param $order_number 订单号
     * @return mixed 返回当前订单的配件
     */
    private function parts_1($order_number){
        $order['order_number'] = $order_number;
        $order['type']         = 1;
        $pro_parts = $this->pro_parts_model->where($order)->select();
        return $pro_parts;
    }
    /**
     * 自行购买配件单
     * @param $order_number 订单号
     * @return mixed 返回当前订单的配件
     */
    private function parts_2($order_number){
        $order['order_number'] = $order_number;
        $order['type']         = 2;
        $pro_parts = $this->pro_parts_model->where($order)->select();
        return $pro_parts;
    }


    /**
     * 获取当前订单存在的配件
     * @param $order_number
     * @return 返回配件名称;配件价格
     */
    private function select_parts($order_number){

        $map_pro['order_number'] = $order_number;
        $service = $this->order_pro_model->where($map_pro)->field('pro_xinhao,pro_property,pro_product,order_type')->find();
        $map_parts['parts_pinlei'] = $service['pro_xinhao'];
        $map_parts['pro_property'] = $service['pro_property'];
        $map_parts['parts_product'] = $service['pro_product'];
        $parts = $this->pro_parts_price_model->where($map_parts)->field('parts_name,parts_price')->select();
        return $parts;
    }

    /**
     * 当前单号的服务
     * @param $order_number
     * @return array
     */

    public function pro_service($order_number){
        $map_pro['order_number'] = $order_number;

        $service = $this->order_pro_model->where($map_pro)->field('baoxiu_type,pro_xinhao,pro_property,pro_product,order_type')->find();

        if ($service['order_type'] == 1) $map_price['order_type'] = '安装';
        if ($service['order_type'] == 2) $map_price['order_type'] = '维修';
        if ($service['order_type'] == 3) $map_price['order_type'] = '送修';
        if ($service['order_type'] == 4) $map_price['order_type'] = '清洗';
        $map_price['pro_pinlei']   = $service['pro_xinhao'];
        $map_price['pro_property'] = $service['pro_property'];
        $map_price['product']      = $service['pro_product'];

        if( $service['baoxiu_type'] == 1){
            $service_pro = $this->pro_price_detail_model->where($map_price)->field('pro_price,service_project,service_content')->select();
        }
        if( $service['baoxiu_type'] == 2 ){
            $service_pro = $this->pro_price_detail_model->where($map_price)->field('pro_price_wai,service_project,service_content')->select();
            for ($k = 0; $k < count($service_pro); $k++) {
                $service_pro[$k]['pro_price'] = $service_pro[$k]['pro_price_wai'];
            }
        }

        if ($service['order_type'] == 2) {
            for ($i = 0; $i < count($service_pro); $i++) {
                $service_pro[$i]['service_content'] = explode(',', $service_pro[$i]['service_content']);
            }
        }if ($service['order_type'] == 3) {
            for ($i = 0; $i < count($service_pro); $i++) {
                $service_pro[$i]['service_content'] = explode(',', $service_pro[$i]['service_content']);
            }
        }
        if($service['order_type'] == 1){
            for ($i = 0; $i < count($service_pro); $i++) {
                $service_pro[$i]['service_content'] = array('0'=>'上门安装');
            }
        }

        return $service_pro;
}


    /**
     *开始服务
     */
    public function start_order(){
        $map['order_number'] = I('post.order_number');
        $source = $this->order_model->where(array('order_number'=>I('post.order_number')))->field('source')->find();
        if($source['source'] == 1){
               $this->ajaxReturn(2);exit;
        }


        $order_number = I('post.order_number');
        $save['start_time']  = time();
        $where['status']      = '16';
        $add_time = $this->order_repair_model->where($map)->save($save);
        $order = $this->order_model->where($map)->save($where);
        $order_user = $this->order_user_model->where($map)->save($where);

        $id = session('user_id');
        $status = $this->order_model->where($map)->field('repair_person_id')->find();
        $repairer = D('user_repairer')->where('user_id='.$status['repair_person_id'])->field('real_name')->find();
        $user_shop = D('user_service')->where("user_id=".$id)->field('company,rel_name')->find();
        $track['order_number'] = $order_number;
        $track['create_time']  = time();
        $track['content']      = "工单开始服务";
        $track['person']       = $user_shop['rel_name'];
        $tra = D('order_track')->add($track);

        if($add_time && $order && $order_user)   {
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }

    /**
     * 工单完结码
     */
    public function end_code(){
        $order_number = I('order_number');
        $map['order_number'] = $order_number;
        $phone = $this->order_user_model->where($map)->field('user_phone')->find();
        $rand_number = rand_twelve();
        $content = "您好，您的工单完结码是".$rand_number.".如非本人操作，请无需理会,【驰达家维】";
        vendor ("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms  = new \Cxsms($options);
        $result = $Cxsms->send($phone['user_phone'],$content);
        if($result && $result['returnsms']['returnstatus']=='Success') {
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
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(2);
            }

        }
    }

    /**
     * 留言模块
     */
    public function leaving_message(){
        $map['order_number'] = I('order_number');
        $list =	D('leaving_message')->where($map)->order('create_time desc')->select();
        $this->assign('list',$list);
        $this->assign('order_number',I('order_number'));
        $this->display('words');
    }

    /**
     * 增加留言
     */
    public function add_message(){
        $content = I('content');
        $order_number = I('order_number');
        if(empty($content)){
            $this->error('留言内容不能为空');
        }
        $map['user_id'] = session('user_id');
        $user_shop = D('user_service')->where($map)->field('company')->find();
        $add['create_time'] = time();
        $add['order_number']= $order_number;
        $add['content']     = $content;
        $add['person']      = $user_shop['company'];
        $result = D('leaving_message')->add($add);
        if($result){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * @param $order_number
     * 工单跟踪
     */
    public function order_track($order_number){
        $map['order_number'] = $order_number;
        $track = D('order_track')->order('create_time desc')->where($map)->select();
        $this->assign('track',$track);
    }

    public function income($order_number)
    {
      $result = D('income')->where(array('order_number'=>$order_number))->find();
      $this->assign('income',$result);
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
            $this->success('付款成功',U('Lianbao_PC/SMyOrder/assig'));exit;
        } else {
            $model->rollback();
            $this->error('付款失败');
        }
    }

    /**
     * 发送短信
     * @param  [type] $mobile [description]
     * @param  [type] $cont   [description]
     * @return [type]         [description]
     */
    private function send_info_fiel($mobile,$content){
        vendor ("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms  = new \Cxsms($options);
        $result = $Cxsms->send($mobile,$content);
        if($result && $result['returnsms']['returnstatus']=='Success'){

        }else{
            $this->error('发送短信失败');
        }
    }
   


}