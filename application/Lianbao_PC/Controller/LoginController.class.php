<?php
namespace Lianbao_PC\Controller;

use Think\Controller;

class loginController extends IndexBaseController
{
    public function index()
    {	$count = D('user')->count();	
        $count = $count + 1111;
		$this->assign('count',$count);
		$this->display('login');
    }
    public function login(){
        $name = $_POST['name'];
        $pwd = md5($_POST['pwd']);
        $vcode = $_POST['code'];
       ;

        $vode = check_verify($vcode);
        if ($vode) {
            $map['username'] = $name;
            $map['password'] = $pwd;


            $sql = M('user')->where($map)->find();
            if ($sql['user_type'] == 2) {
                    session('user_id', $sql['id']);
                    $this->ajaxReturn(1);
            } elseif($sql['user_type'] == 3) {
                session('user_id', $sql['id']);
                $this->ajaxReturn(4);
            }else{
				$this->ajaxReturn(2);
			}
        } else {
             $this->ajaxReturn(3);
        }
    }
	public function loginout(){
		 $user =  session('user_id',null);
		 if(empty($user))	 
		 $this->redirect('Login/index','',0);	
	}

    public function forget_password_list(){
        $this->display('getpwd');

    }


    /**
     * 发送短信
     */
    public function send_info(){
        $mobile      = I('post.mobile');

        $rand_number = rand_six();
        if(!preg_match('/^1[23456789]\d{9}$/',$mobile)){
            exit($this->ajaxReturn(2));
            //手机号码错误
        }

        $map['username'] = $mobile;
        $username = D('user')->where($map)->field('username')->find();
        if(empty($username['username'])){
            exit($this->ajaxReturn(3));
            //账户不存在
        }

        $next = D('shot_message')->where($map)->field('next_send,create_time')->find();
        if($next['create_time']<time() && time()<$next['next_send']){
            exit($this->ajaxReturn(4));
            //短时间重复发送
        }
        $content = "您好，您的找回密码验证码是".$rand_number.".如非本人操作，请无需理会,【驰达家维】";
        vendor ("Cxsms.Cxsms");
        $options = array(
            'userid'  =>'1167',
            'account' =>'18781176753',
            'password'=>'5280201',
        );
        $Cxsms  = new \Cxsms($options);
        $result = $Cxsms->send($mobile,$content);
        if($result && $result['returnsms']['returnstatus']=='Success'){
            $map_te['username'] = $mobile;
            $shot_message = D('shot_message')->where($map)->field('username')->find();

            $save_shot['username']        = $mobile;
            $save_shot['create_time']     =  time();
            $save_shot['code']            =  $rand_number;
            $save_shot['end_time']        =  strtotime("+15 minute");
            $save_shot['next_send']        =  strtotime("+1 minute");
            if($shot_message['username']){
                $result = D('shot_message')->where($map_te)->save($save_shot);
            }else{
                $result = D('shot_message')->add($save_shot);
            }
            if($result){
                exit($this->ajaxReturn(1));
            }else{
                exit($this->ajaxReturn(6));
            }
        }else{
            exit($this->ajaxReturn(5));
        }
    }

    public function check_mess(){
        $map['username'] = I('username');
        session('username',I('username'));
        $end_time = D('shot_message')->where($map)->field('end_time,code')->find();
        $shot_message = I('shot_message');
        if($shot_message != $end_time['code']){
            $this->ajaxReturn(1);
        }else{
			if(time()>$end_time['end_time']){
				 $this->ajaxReturn(3);
			}else{
				$this->ajaxReturn(2);
			}
			
            

        }
    }
    public function new_password(){
        $this->display('new_pass');
    }
    public function modify_password(){
       $username     = session('username');
       $password     = I('password');
       $new_password = I('re_password');
        if($password != $new_password) {
            $this->ajaxReturn(1);exit;
        }
        $is_exist = D('user')->where(array('username'=>$username))->field('id')->find();
        if(empty($is_exist['id'])){
            $this->ajaxReturn(2);exit;
        }
        $result = D('user')->where(array('username'=>$username))->setField('password',md5($password));
        if($result){
            $this->ajaxReturn(3);
        }else{
           $this->ajaxReturn(4);
        }

    }

}