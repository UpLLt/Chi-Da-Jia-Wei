<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 15:06
 */
namespace Own\Controller;
use Repair\Controller\BaseController;
use Think\Controller;
class WalletController extends BaseController{
    protected $wallet_buyers_model;
    protected $user_personal_model;
    protected $user_model;
    protected $coupon_model;
    public function __construct()
    {
        parent::__construct();
        $this->wallet_buyers_model = D('wallet_buyers');
        $this->user_personal_model = D('user_personal');
        $this->user_model           = D('user');
        $this->coupon_model         = D('coupon');
    }

    /**
     * 我的余额
     */
    public function balance(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $this->checkparam(array($user_id,$token));
        $this->checktoken($user_id,$token);

        $balance = $this->wallet_buyers_model->where(array('user_id'=>$user_id))->field('balance')->find();
        $data['balance'] = empty($balance['balance']) ? 0 : $balance['balance'];
        exit($this->returnApiSuccess($data));
    }

    /**
     * 我的钱包
     */
    public function wallet(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $this->checkparam(array($user_id,$token));
        $this->checktoken($user_id,$token);

//        $balance = $this->wallet_buyers_model->where(array('user_id'=>$user_id))->field('balance')->find();
        $username= $this->user_model->where(array('id'=>$user_id))->field('username')->find();
        $head_picture = $this->user_personal_model->where(array('user_id'=>$user_id))->field('header_picture')->find();
//        $cou     = $this->coupon_model->where(array('uid'=>$user_id,'status'=>1))->count();
//        $data['coupon_count']= $cou;
//        $data['balance']     = empty($balance['balance']) ? '' : $balance['balance'];
        $data['username']    = $username['username'];
        $data['head_picture']= empty($this->geturl($head_picture['header_picture'])) ? '' : $this->geturl($head_picture['header_picture']);
        exit($this->returnApiSuccess($data));
    }

    /**
     * 上传头像
     */
    public function edit_head_picture(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $head_picture = I('post.head_picture');
        $this->checkparam(array($user_id,$token));
        $this->checktoken($user_id,$token);

        $this->upload_model($user_id);
    }


    public function upload_model($user_id)
    {

        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info   =   $upload->upload();

        if(empty($info['header_picture']["savepath"])){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'上传图片不能为空'));
        }else{
            $save['header_picture']   = '/data/upload/'.$info['header_picture']["savepath"].$info['header_picture']["savename"];
            $where['user_id'] = $user_id;

            $success = $this->user_personal_model
                ->where($where)
                ->save($save);

            if(!$success) {
                exit($this->returnApiError(BaseController::FATAL_ERROR,'上传文件失败'));
            }else{
                exit($this->returnApiSuccess());
            }
        }


    }

    /**
     * 版本号
     */
    public function judge_verson(){

        $verson_number  = I('get.verson_number');
        $number = D('verson_number')->where(array('app_type'=>1))->order('create_time desc')->field('verson_number,verson_content,url')->find();

        if($number['verson_number'] == $verson_number ) {
            $data['status'] = 1;
            $data['content']= '';
            $data['url']    = $number['url'];
        }else if($number['verson_number'] < $verson_number){
            $data['status'] = 3;
            $data['content']= "当前版本号不存在";
            $data['url']    = $number['url'];
        }else{
            $data['status'] = 2;
            $data['content']= $number['verson_content'];
            $data['url']    = $number['url'];
        }
        exit($this->returnApiSuccess($data));
    }

    /**
     * 意见反馈
     */
    public function complaint()
    {
        $token = I('post.token');
        $user_id = I('post.user_id');
        $content = I('post.content');
        $name    = I('post.name');
        $this->checkparam(array($token, $user_id,$content,$name));
        $this->checktoken($user_id,$token);
        $data = I('post.');
        $data['type'] = '9';
        $data['repair_person_id'] = $user_id;
        $data['create_time']      = time();



        if (D('complaint')->create($data)) {
            $result = D('complaint')->add($data);
            if ($result) {
                exit($this->returnApiSuccess());
            } else {
                exit($this->returnApiError(BaseController::FATAL_ERROR, '添加失败'));
            }
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, D('complaint')->getError()));
        }
    }


}