<?php
namespace Own\Controller;
use Think\Controller;
class RegisterController extends BaseController{
    protected $user_model;
    protected $pro_price_model;
    protected $user_repairer_model;
    protected $shot_message_model;
    protected $wallet_buyers_model;
    protected $user_personal_moodel;

    public function __construct()
    {
        parent::__construct();
        $this->user_repairer_model = D('user_repairer');
        $this->user_model = D('user');
        $this->pro_price_model = D('pro_price');
        $this->shot_message_model = D('shot_message');
        $this->wallet_buyers_model = D('wallet_buyers');
        $this->user_personal_moodel= D('user_personal');
    }

    /**
     * 注册
     */
    public function register(){
        $vcode    = I('post.vcode');
        $username = I('post.username');
        $password = I('post.password');
        $this->checkparam(array($vcode,$username,$password));
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



        if(strlen(I('post.password'))<6 || strlen(I('post.password'))>36 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'密码位数不正确'));
        }


        $this->checkparam(array($username,$password,$vcode));
        $user['create_time'] = time();
        $user['username'] = $username;
        $user['password'] = md5($password);

        $user['user_type']= 1;
        $user['status']   = 1;
        $user['examine_status'] = 1;
        $user_res = $this->user_model->add($user);

        if($user_res){
            $wallet_bu['user_id'] = $user_res;
            $this->wallet_buyers_model->add($wallet_bu);
            $this->user_personal_moodel->add($wallet_bu);

        }

        if($user_res){
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,'添加失败'));
        }
    }




    /**
     * 发送短信
     */
    public function send_info(){
        $mobile      = I('post.mobile');
        $this->checkparam(array($mobile));
        $rand_number = rand_six();
        if(!preg_match('/^1[12345789]\d{9}$/',$mobile)){
            exit($this->returnApiError(BaseController::MISS_PARAM,'电话号码不正确'));
        }

        $content = "您好，您的短信验证码是".$rand_number.".如非本人操作，请无需理会,【驰达家维】";
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
     * 找回密码
     */
    public function lose_password(){

        $username = I('post.username');
        $password = md5(I('post.password'));
        $vcode    = I('post.vcode');



        if( strlen(I('post.password')) < 6 || strlen(I('post.password')) > 36 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'密码位数不正确'));
        }


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
     * 修改密码
     */
    public function modify_password(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $old_password = I('post.old_password');
        $password=  I('post.password');

        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id,$token,$old_password,$password));

        if(strlen($password)<6 || strlen($password)>36 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'新密码位数不正确'));
        }


        if( $old_password == $password ) exit($this->returnApiError(BaseController::FATAL_ERROR,'两次密码不能一致'));

       if(empty($password) || empty($old_password)  ) exit($this->returnApiError(BaseController::FATAL_ERROR,'值不能为空'));
        $map['id'] = $user_id;
        $map['password'] = md5($old_password);

        $user = $this->user_model->where($map)->find();
        if(!$user) exit($this->returnApiError(BaseController::FATAL_ERROR,'原密码不正确'));


        $this->user_model->where(array('id'=>$user_id))->setField('password',md5($password));
        exit($this->returnApiSuccess());

    }






}