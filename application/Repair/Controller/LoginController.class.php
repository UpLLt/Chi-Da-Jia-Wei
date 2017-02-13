<?php
namespace Repair\Controller;
use Think\Controller;
class LoginController extends BaseController {
    protected $user_model;
    protected $token_model;
    public function __construct()
    {
        parent::__construct();
        $this->user_model  = D('user');
        $this->token_model = D('token');
    }

    public function login()
    {
        $username = I('post.username');
        $password = md5(I('post.password'));
        $this->checkparam(array($username,$password));

        $map['username'] = $username;
        $map['password'] = $password;
        $map['user_type']= 4;
        $map['status']   = 1;
        $map_use['username'] = $username;
        $r = $this->user_model
            ->where($map_use)
            ->find();
        $result = $this->user_model
             ->where($map)
             ->find();
        if(empty($r['id'])){ exit($this->returnApiError(BaseController::FATAL_ERROR,'账号不存在'));}
        if(empty($result['id'])){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'密码错误'));
        }
        if( $r['examine_status'] != 2 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'登陆失败，账号尚未通过审核，请耐心等待'));
        }
        $token = $this->createtoken();
        $where['user_id'] = $result['id'];
        $is_token= $this->token_model
            ->where($where)
            ->field('id')
            ->find();
        $insert['create_time']    =  time();
        $insert['token_end_time'] =  strtotime("+1 month");
        $insert['token']          =  $token;
        if($is_token){
            $res = $this->token_model->where($where)->save($insert);
        }else{
            $insert['user_id'] = $result['id'];
            $res = $this->token_model->add($insert);
        }
        if(!$res) exit($this->returnApiError(BaseController::FATAL_ERROR,'token添加失败'));
        $data['user_id'] = $result['id'];

        $is_exis =  D('wallet_repairer')->where(array('user_id'=>$result['id']))->field('id')->find();

        if(empty($is_exis['id'])){
            $add_re['user_id'] = $result['id'];
            D('wallet_repairer')->add($add_re);
        }


        exit($this->returnApiSuccess($data,$token));
    }
    /**
     * 取消登陆
     */
    public function cancel_login(){
        $user_id = I('post.user_id');
        $token   = I('token');
        $this->checktoken($user_id,$token);
        $save['token'] = '';
        $save['token_end_time'] = '';
        $save['create_time'] = '';
        $result = $this->token_model->where(array('user_id'=>$user_id))->save($save);
        if($result){
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,'已退出登陆'));
        }
    }

}