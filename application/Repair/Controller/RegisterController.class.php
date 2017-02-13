<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 13:45
 */
namespace Repair\Controller;
use Think\Controller;
class RegisterController extends BaseController{
    protected $user_model;
    protected $pro_price_model;
    protected $user_repairer_model;
    protected $shot_message_model;
    public function __construct()
    {
        parent::__construct();
        $this->user_repairer_model = D('user_repairer');
        $this->user_model = D('user');
        $this->pro_price_model = D('pro_price');
        $this->shot_message_model = D('shot_message');
    }

    /**
     * 注册
     */
    public function register(){
        $vcode    = I('vcode');
        $username = I('username');
        $map_code['username'] =   $username;
        $z_code     = $this->shot_message_model
            ->where($map_code)
            ->order('create_time desc')
            ->find();

        $code = $z_code['code'];
        if($vcode !== $code){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'验证码错误'));
        }
        if( time() > $z_code['end_time'] ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'短信验证码失效'));
        }
        $map_user['username'] = $username;

        $is_user = $this->user_model->where($map_user)->find();
        if($is_user) exit($this->returnApiError(BaseController::FATAL_ERROR,'用户名已存在'));


        $alladd = true;
        $this->user_model->startTrans();

        $password = I('password');
        $rel_name = I('rel_name');
        $city     = I('shop_add');
        $detail_add= I('detail_add');
        $id_card  = I('id_card');
        $skill    = I('skill');
        $this->checkparam(array($username,$password,$rel_name,$city,$detail_add,$id_card,$skill));
        $user['create_time'] = time();
        $user['username'] = $username;
        $user['password'] = md5($password);
        $user['user_type']= 4;
        $user['status']   = 1;
        $user['examine_status'] = 1;
        $user_res = $this->user_model->add($user);

        $map['username']  =  $username;
        $user_id  = $this->user_model->where($map)->field('id')->find();
        $repair['user_id']= $user_id['id'];
        $repair['real_name'] = $rel_name;
        $repair['city']   = $city;
        $repair['address']= $detail_add;
        $repair['id_card']= $id_card;
        $repair['city']   = $city;
        $repair['skill']  = $skill;
        $repair_res = $this->user_repairer_model->add($repair);
        $this->upload_model($username);

        if($user_res && $repair_res){

            $this->user_model->commit();
            exit($this->returnApiSuccess());

        }else{

            $this->user_model->rollback();
            exit($this->returnApiError(BaseController::FATAL_ERROR,'添加失败'));

        }
    }

    /**
     * 验证码验证
     */
    public function verify_vcode(){
        $vcode    = I('post.vcode');
        $username = I('post.username');
        $map_code['username'] =   $username;
        $z_code     = $this->shot_message_model
            ->where($map_code)
            ->order('create_time desc')
            ->find();

        $code = $z_code['code'];
        if($vcode !== $code){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'验证码错误'));
        }
        if( time() > $z_code['end_time'] ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'短信验证码失效'));
        }

        exit($this->returnApiSuccess());
    }


    public function upload_model($username)
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();

        $save['id_card_picture_1']   = '/data/upload/'.$info['id_card_picture_1']["savepath"].$info['id_card_picture_1']["savename"];
        $save['id_card_picture_1']   = $this->geturl( $save['id_card_picture_1'] );

        $save['id_card_picture_2']   = '/data/upload/'.$info['id_card_picture_2']["savepath"].$info['id_card_picture_2']["savename"];
        $save['id_card_picture_2']   = $this->geturl( $save['id_card_picture_2'] );

        $map['username'] = $username;
        $user_id = $this->user_model->field('id')->where($map)->find();
            $where['user_id'] = $user_id['id'];
             $success = $this->user_repairer_model
                        ->where($where)
                        ->save($save);
        if(!$success) {
            exit($this->returnApiError(BaseController::FATAL_ERROR,'上传文件失败'));
        }
    }

    /**
     * 找回密码
     */
    public function lose_password(){

        $username = I('post.username');
        $password = md5(I('post.password'));
        $vcode    = I('post.vcode');
        $this->checkparam(array($username,$password,$vcode));
        $map_code['username'] =   $username;
        $z_code     = $this->shot_message_model
                    ->where($map_code)
                    ->order('create_time desc')
                    ->find();
        if( time() > $z_code['end_time'] ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'短信验证码失效'));
        }
        $code = $z_code['code'];

        if($vcode !== $code){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'验证码错误'));
        }
        $map['username'] = $username;
        $save['password']= $password;
        $result = $this->user_model->where($map)->save($save);

            exit($this->returnApiSuccess());


        }


    /**
     * 找回体现密码
     */
    public function lose_taking_password(){

        $username = I('post.username');
        $password = md5(I('post.taking_password'));
        $vcode    = I('post.vcode');
        $this->checkparam(array($username,$password,$vcode));
        $map_code['username'] =   $username;
        $z_code     = $this->shot_message_model
            ->where($map_code)
            ->order('create_time desc')
            ->find();
        if( time() > $z_code['end_time'] ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'短信验证码失效'));
        }
        $code = $z_code['code'];

        if($vcode !== $code){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'验证码错误'));
        }
        $map['username'] = $username;
        $save['taking_password']= $password;
        $this->user_model->where($map)->save($save);

        exit($this->returnApiSuccess());


    }

    /**
     * 发送短信
     */
    public function send_info(){
        $mobile      = I('post.mobile');

        $rand_number = rand_six();
        if(!preg_match('/^1[34578]\d{9}$/',$mobile)){
            exit($this->returnApiError(BaseController::MISS_PARAM,'电话号码不正确'));
        }

        $content = "您好，您的验证码是".$rand_number.".如非本人操作，请无需理会,【驰达家维】";
        vendor ("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms  = new \Cxsms($options);
        $result = $Cxsms->send($mobile,$content);
        if($result && $result['returnsms']['returnstatus']=='Success'){
            $save_shot['username'] = $mobile;
            $save_shot['create_time']     =  time();
            $save_shot['code']            =  $rand_number;
            $save_shot['end_time']        =  strtotime("+15 minute");
            $this->shot_message_model->add($save_shot);
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,''));
        }



    }


    /**
     * 注册我的技能
     */
    public function skill(){
        $pro_pinlei = $this->pro_price_model->field('pro_pinlei')->select();
        $pro_pinlei = array_unique_fb($pro_pinlei);
        sort($pro_pinlei);

        for($i=0;$i<count($pro_pinlei);$i++){
            $pro_pinlei[$i]['name'] =  $pro_pinlei[$i][0];
            unset( $pro_pinlei[$i][0]);
            $map['pro_pinlei'] = $pro_pinlei[$i]['name'];
            $product = $this->pro_price_model->where($map)->field('product')->select();
            $product = array_unique_fb($product);
            sort($product);
           for($j=0;$j<count($product);$j++){
               $product[$j] =  $product[$j][0];
           }
            $pro_pinlei[$i]['product'] = $product;

        }

            exit($this->returnApiSuccess($pro_pinlei));
    }


}