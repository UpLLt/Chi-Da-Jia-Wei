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
class AddressController extends BaseController{
    protected $user_rec_address_model;

    public function __construct()
    {
        parent::__construct();
        $this->user_rec_address_model = D('user_rec_address');
    }

    /**
     * 收货地址详情页
     */
    public function address_list(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id));
        $data = $this->user_rec_address_model->where(array('user_id'=>$user_id))->field('id,rec_city,rec_address')->select();
        exit($this->returnApiSuccess($data));
    }



    /**
     * 增加收货地址
     */
    public function address(){
        $user_id     = I('post.user_id');
        $token       = I('post.token');
        $rec_city    = I('post.rec_city');
        $rec_address = I('post.rec_address');

        $this->checkparam(array($user_id,$token,$rec_city,$rec_address));
        $this->checktoken($token);
        if($this->user_rec_address_model->create()){

            $map['user_id'] = $user_id;
            $is_add = $this->user_rec_address_model->field('rec_address')->where($map)->find();

            if($is_add['rec_address']){
                $result = $this->user_rec_address_model->where($map)->save($this->user_rec_address_model->create());
            }else{

                $result = $this->user_rec_address_model->add();
            }
            if($result){
                exit($this->returnApiSuccess());
            }else{
                exit($this->returnApiError(BaseController::FATAL_ERROR,'添加收货地址失败'));
            }
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,$this->user_rec_address_model->getError()));
        }

    }


}