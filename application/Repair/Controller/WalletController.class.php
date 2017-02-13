<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 11:09
 */
namespace Repair\Controller;

use ThinK\Controller;

class WalletController extends BaseController
{
    protected $user_model;
    protected $wallet_repairer_model;
    protected $links_model;
    protected $user_card_model;
    protected $wallet_service_consum_model;
    protected $complaint_model;
    protected $user_repairer_model;
    protected $user_service_model;
    protected $pro_price_model;
    protected $user_rec_address_model;
    protected $pro_pinlei_model;
    protected $order_model;

    public function __construct()
    {
        parent::__construct();
        $this->__initialize();
    }

    private function __initialize()
    {
        $this->order_model       = D('order');
        $this->pro_pinlei_model  = D('pro_pinlei');
        $this->wallet_repairer_model = D('wallet_repairer');
        $this->links_model = D('links');
        $this->user_model = D('user');
        $this->user_card_model = D('UserCard');
        $this->wallet_service_consum_model = D('wallet_service_consum');
        $this->complaint_model = D('complaint');
        $this->user_repairer_model = D('user_repairer');
        $this->user_service_model  = D('user_service');
        $this->pro_price_model     = D('pro_price');
        $this->user_rec_address_model = D('user_rec_address');
    }

    public function index()
    {
        $user_id = I('post.user_id');
        $this->checkparam(array($user_id));
        $link['link_status'] = 1;
        $link['type'] = 6;
        $service_phone = $this->links_model->where($link)->field('link_description')->find();
        $map['id'] = $user_id;
        $username = $this->user_model->where($map)->field('username')->find();

        $data['username'] = $username['username'];
        $data['service_phone'] = $service_phone['link_description'];
        $map_repair['user_id'] = I('user_id');
        $result = $this->user_repairer_model->where($map_repair)->field('parent_id,real_name')->find();
        $data['real_name']     = $result['real_name'];

        if($result['parent_id']){
            $data['status'] = 1;
        }else{
            $data['status'] = 2;}

        exit($this->returnApiSuccess($data));
    }

    /**
     * 我的钱包
     */
    public function wallet()
    {
        $user_id = I('post.user_id');
        $token  = I('post.token');
        $map['user_id'] = $user_id;
        $this->checkparam(array($user_id, $token));
        $this->checktoken($user_id, $token);
        $repair = $this->user_repairer_model->where($map)->field('type')->find();
        $repair['type'] = empty($repair['type']) ?  '' : $repair['type'];

        $is_exis = $this->wallet_repairer_model->where($map)->field('id')->find();
        if(empty($is_exis['id'])){
            $add_re['user_id'] = $user_id;
            $this->wallet_repairer_model->add($add_re);
        }
        $wallet = $this->wallet_repairer_model
            ->where($map)
            ->field('balance,tixian_ing,taking_money,tixian_all,wait_queren,bond_money,tixian_status')
            ->find();
        $wallet['balance']      = empty($wallet['balance']) ? '0' : $wallet['balance'];
        $wallet['tixian_ing']   = empty($wallet['tixian_ing']) ? '0' : $wallet['tixian_ing'];
        $wallet['taking_money'] = empty($wallet['taking_money']) ? '0' : $wallet['taking_money'];
        $wallet['tixian_all']   = empty($wallet['tixian_all']) ? '0' : $wallet['tixian_all'];
        $wallet['wait_queren']  = empty($wallet['wait_queren']) ? '0' : $wallet['wait_queren'];
        $wallet['bond_money']   = empty($wallet['bond_money']) ? '0' : $wallet['bond_money'];
        $wallet['tixian_status']= empty($wallet['tixian_status']) ? '0' : $wallet['tixian_status'];
        $result = $this->user_model->where(array('id'=>$user_id))->field('taking_password')->find();
       if($result['taking_password']){
           $wallet['is_taking_password'] = "1";
       }else{
           $wallet['is_taking_password'] = "2";
       }
        $wallet['repair_type'] = $repair['type'];
        exit($this->returnApiSuccess($wallet));
    }

    /**
     * 添加银行卡
     */
    public function add_card()
    {
        $taking_password = md5(I('post.taking_password'));
        $card_name      = I('post.card_name');
        $card_number    = I('post.card_number');
        $card_bank      = I('post.card_bank');
        $phone          = I('post.phone');
        $token          = I('post.token');
        $user_id        = I('post.user_id');
        $map['user_id'] = $user_id;
        $is_id_card  = D('user_card')->where($map)->field('id')->find();

        $this->checkparam(array($card_name, $card_number, $token));
        $this->checktoken($user_id, $token);
        if( $taking_password ){

            D('user')->where(array('id'=>$user_id))->setField('taking_password',$taking_password);
        }


        if ($this->user_card_model->create()) {
            $result = $this->user_card_model->add();
            if ($result) {

                exit($this->returnApiSuccess());
            } else {
                exit($this->returnApiError(BaseController::FATAL_ERROR, '添加失败'));
            }
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, $this->user_card_model->getError()));
        }

    }

    /**
     * 删除银行卡
     */
    public function del_id_card(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $id      = I('post.card_id');
        $this->checkparam(array($user_id,$token,$id));
        $this->checktoken($user_id,$token);
        $result = $this->user_card_model->where(array('id'=>$id))->delete();
        if($result){
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,'删除失败'));
        }
    }


    /**
     * 修改体现密码
     */
    public function modify_taking_password(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $password= md5(I('post.taking_password'));
        $new_password = md5(I('post.new_taking_password'));
        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id,$token,$password,$new_password));
        if( $password == $new_password ) exit($this->returnApiError(BaseController::FATAL_ERROR,'两次密码不能一致'));
        if(empty($password) || empty($new_password)  ) exit($this->returnApiError(BaseController::FATAL_ERROR,'值不能为空'));
        $map['id'] = $user_id;
        $map['taking_password'] = $password;
        $user = $this->user_model->where($map)->find();
        if(!$user) exit($this->returnApiError(BaseController::FATAL_ERROR,'密码不正确'));
        $this->user_model->where($map)->setField('taking_password',$new_password);
        exit($this->returnApiSuccess());

    }


    /**
     * 银行卡
     */
    public function card()
    {
        $token = I('post.token');
        $user_id = I('post.user_id');
        $this->checkparam(array($token, $user_id));
        $this->checktoken($user_id, $token);
        $map['user_id'] = $user_id;
        $id_card = $this->user_card_model->where($map)
            ->order('create_time desc')
            ->field('id,card_name,card_number,card_bank,phone')
            ->select();

        exit($this->returnApiSuccess($id_card));
    }


    /**
     * 消费记录
     */
    public function bond()
    {
        $token = I('post.token');
        $user_id = I('post.user_id');
        $page    = I('post.page');
        $pagenum = I('post.pagenum');
        empty($pagenum) ? $pagenum= 10:$pagenum=I('post.pagenum');
        $this->checkparam(array($token, $user_id,$page));
        $this->checktoken($user_id, $token);
        $map['repairer_id'] = $user_id;

        $count = $this->wallet_service_consum_model->where($map)->count();
        $start = ($page-1)*$pagenum;
        $pagemax = ceil($count/$pagenum);

        $bond  = $this->wallet_service_consum_model->where($map)
            ->limit($start.','.$pagenum)
            ->order('create_time desc')
            ->field('liushuihao,create_time,repairer_price,type')
            ->select();
        $data['name']= $bond;

        for ($i = 0; $i < count($data['name']); $i++) {
            $data['name'][$i]['create_time'] = date("Y-m-d H:i:s", $data['name'][$i]['create_time']);
            if ($data['name'][$i]['type'] == 1) {
                $data['name'][$i]['type'] = '维修收入';
                $data['name'][$i]['status'] = '已放入钱包';
                $data['name'][$i]['repairer_price'] = "+".$data['name'][$i]['repairer_price'];
            }
            if ($data['name'][$i]['type'] == 2)
            {
                $data['name'][$i]['type'] = '提现';
                $data['name'][$i]['status'] = '已提现';
                $data['name'][$i]['repairer_price'] = "-".$data['name'][$i]['repairer_price'];
            }
            if ($data['name'][$i]['type'] == 3)
            {
                $data['name'][$i]['type'] = '提现失败';
                $data['name'][$i]['status'] = '已返回钱包';
                $data['name'][$i]['repairer_price'] = 0;
            }
        }
        $data['page']    = $page;
        $data['pagemax'] = $pagemax;
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
        $this->checkparam(array($token, $user_id));
        $this->checktoken($user_id, $token);
        $data = I('post.');
        $data['type'] = '9';
        $data['repair_person_id'] = $user_id;
        $data['create_time']      = time();

        $repair = D('user_repairer')->where(array('user_id'=>$user_id))->field('real_name')->find();
        $data['name'] = $repair['real_name'];
        if ($this->complaint_model->create($data)) {
            $result = $this->complaint_model->add($data);
            if ($result) {
                exit($this->returnApiSuccess());
            } else {
                exit($this->returnApiError(BaseController::FATAL_ERROR, '添加失败'));
            }
        } else {
            exit($this->returnApiError(BaseController::FATAL_ERROR, $this->user_card_model->getError()));
        }
    }

    /**
     * 申请成为企业成员
     */
    public function apply_list(){

        $map['user_id']  =  I('post.user_id');
        $is_exist = D('user')->where(array('id'=>I('post.user_id')))->field('examine_status')->find();
        if($is_exist['examine_status'] ==1 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,"该用户尚未通过审核，不能申请成为企业成员"));
        }
        $order_map['status'] = array(array('eq', 8), array('eq', 9), array('eq', 10), array('eq', 11),array('eq', 12),array('eq', 13),array('eq', 16),array('eq', 20),'or');
        $order_map['repair_person_id'] = I('pos0t.user_id');
        $order_count = $this->order_model->where($order_map)->count();
        if($order_count > 0 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,"申请失败，请完结工单后再申请"));
        }

        $this->checkparam(array(I('post.user_id')));
        $city = $this->user_repairer_model
            ->where($map)
            ->field('city')
            ->find();
        $loca['shop_location'] = array('like',array('%'.$city['city'].'%'));


        $result = $this->user_service_model->where($loca)->field('user_id,company')->select();
        for($i=0;$i<count($result);$i++){
            $map['id'] = $result[$i]['user_id'];
            $user = $this->user_model->where($map)->field('username')->find();
            $data[$i]['company_id'] = $result[$i]['user_id'];
            $data[$i]['company_account'] = $user['username'];
            $data[$i]['company'] = $result[$i]['company'];
        }
        if($result){
            exit($this->returnApiSuccess($data));
        }


    }

    /**
     * 申请附属成员
     */
    public function apply_member(){
        $token = I('post.token');
        $user_id = I('post.user_id');
        $type  = I('post.type');
        $par_company_id = I('post.company_id');

        $order_map['status'] = array(array('eq', 8), array('eq', 9), array('eq', 10), array('eq', 11),array('eq', 12),array('eq', 13),array('eq', 16),array('eq', 20),'or');
        $order_map['repair_person_id'] = $user_id;
        $order_count = $this->order_model->where($order_map)->count();
        if($order_count > 0 ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,"申请失败，请完结工单后再申请"));
        }

        $this->checkparam(array($token, $user_id,$type,$par_company_id));
        $this->checktoken($user_id, $token);
        $map['user_id'] = $user_id;
        $save['type']   =   I('post.type');
        $save['parent_id'] = $par_company_id;
        $result =  $this->user_repairer_model
            ->where($map)
            ->save($save);
        if($result){
            exit($this->returnApiSuccess());
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,'申请失败'));
        }
    }

    /**
     * 个人中心
     */
    public function own_center(){
        $map['user_id'] = I('post.user_id');
        $this->checkparam(array(I('post.user_id')));
        $data = $this->user_repairer_model->where($map)->field('parent_id,type,real_name,phone,address,skill,qq,phone')->find();
        $rec_address = $this->user_rec_address_model->where($map)->field('rec_address,rec_city')->find();
        $data['rec_address']  = $rec_address['rec_city'].$rec_address['rec_address'];
        $map_user['id'] = I('post.user_id');
        $username    = $this->user_model->where($map_user)->field('username')->find();
        $user_service = D('user_service')->where(array('user_id'=>$data['parent_id']))->field('company')->find();
        $data['banding_phone']    = $username['username'];
        $data['guishuqiye']       = empty($user_service['company']) ? '无' : $user_service['company'];
        if($data['type'] == 1 )  {
            $data['type'] = "直属成员";
        } else if( $data['type'] == 2 ){
            $data['type'] = "附属成员";
        }else{
            $data['type'] = "无";
        }
        if(!$data['parent_id'])   $data['parent_id']='';
        if(!$data['skill'])       $data['skill']='';
        if(!$data['rec_address']) $data['rec_address']='未填写';
        if(!$data['real_name'])   $data['real_name']='';
        if(!$data['phone'])       $data['phone']='未填写';
        if(!$data['qq'])          $data['qq']='未填写';
        if(!$data['address'])     $data['address']='';

        exit($this->returnApiSuccess($data));
    }

    /**
     * 收货地址
     */
    public function address(){
        $user_id     = I('post.user_id');
        $token       = I('post.token');
        $rec_city    = I('post.rec_city');
        $rec_address = I('post.rec_address');
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
                exit($this->returnApiError(BaseController::FATAL_ERROR,'添加失败'));
            }
        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,$this->user_rec_address_model->getError()));
        }

    }

    /**
     * 修改手机号
     */
    public function phone(){
        $user_id  = I('post.user_id');
        $token    = I('post.token');
        $phone    = I('post.phone');
        $this->checkparam(array($user_id,$token,$phone));
        $this->checktoken($user_id,$token);
        if($this->user_repairer_model->create()){
            $map['user_id'] = $user_id;
            $this->user_repairer_model->where($map)->save();
            exit($this->returnApiSuccess());

        }else{
            exit($this->returnApiError(BaseController::FATAL_ERROR,$this->user_repairer_model->getError()));
        }
    }

    /**
     * 修改QQ
     */
    public function qq(){
        $user_id  = I('post.user_id');
        $token    = I('post.token');
        $qq    = I('post.qq');
        $this->checkparam(array($user_id,$token,$qq));
        $this->checktoken($user_id,$token);
        $map['user_id']   = $user_id ;
        $save['qq']       = $qq;
        $this->user_repairer_model->where($map)->save($save);
        exit($this->returnApiSuccess());

    }

    /**
     * 价格查看
     */
    public function charge(){
        $user_id = I('post.user_id');

        $product = $this->pro_pinlei_model->field('pro_pinlei,id,pinlei_picture')->select();
        for($i=0;$i<count($product);$i++){
            $param =  '/index.php?g=Repair&m=Yin&a=product&id='.$product[$i]['id'].'&user_id='.$user_id;
            $url  = $this->geturl($param);
            $data[$i]['url'] = $url;
            $data[$i]['pro_pinlei']  = $product[$i]['pro_pinlei'];
            $data[$i]['pro_picture'] = $this->geturl($product[$i]['pinlei_picture']) ;
        }
        exit($this->returnApiSuccess($data));
    }

    /**
     * 修改密码
     */
    public function modify_password(){
        $user_id = I('post.user_id');
        $token   = I('post.token');
        $password= md5(I('post.password'));
        $new_password = md5(I('post.new_password'));
        $this->checktoken($user_id,$token);
        $this->checkparam(array($user_id,$token,$password,$new_password));
        if( $password == $new_password ) exit($this->returnApiError(BaseController::FATAL_ERROR,'两次密码不能一致'));
        if(empty($password) || empty($new_password)  ) exit($this->returnApiError(BaseController::FATAL_ERROR,'值不能为空'));
        $map['id'] = $user_id;
        $map['password'] = $password;
        $user = $this->user_model->where($map)->find();
        if(!$user) exit($this->returnApiError(BaseController::FATAL_ERROR,'密码不正确'));
        $this->user_model->where($map)->setField('password',$new_password);
            exit($this->returnApiSuccess());

    }

    /**
     *关于驰达家维
     */
    public function connect(){
        $param =  '/index.php?g=Repair&m=Yin&a=connect';
        $data  = $this->geturl($param);
        exit($this->returnApiSuccess($data));

    }

    /**
     * 协议
     */
    public function agreement(){
        $param =  '/index.php?g=Repair&m=Yin&a=agreement';
        $data  = $this->geturl($param);
        exit($this->returnApiSuccess($data));
    }

    /**
     * 提现
     */
    public function tixian(){
        $money    = I('post.tixian_money');
        $user_id  = I('post.user_id');
        $token    = I('post.token');
        $card_id  = I('card_id');
        $taking_password = I('post.taking_password');
        $this->checkparam(array($money,$user_id,$token,$card_id,$taking_password));

        $pass = D('user')->where(array('id'=>$user_id))->field('taking_password')->find();
        if(empty($pass['taking_password'])){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'失败，提现密码未设置'));
        }

        if(md5($taking_password) != $pass['taking_password']){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'失败，提现密码不正确'));
        }

        $this->checktoken($user_id,$token);
        $tixian = $this->wallet_repairer_model->where(array('user_id'=>$user_id))->field('balance,tixian_ing,tixian_all')->find();

        $tixian_ing = $tixian['tixian_ing'] + $money;
        if($tixian['balance']  < 0  || $tixian['balance'] < $money ){
            exit($this->returnApiError(BaseController::FATAL_ERROR,'失败，余额不足'));
        }
        if( !empty($tixian['tixian_jinxinshi']) && $tixian['tixian_card_id'] != $card_id ){

            $this->error('再次提现失败，请勿使用与正在提现中的不同银行卡');

        }
		
        $save['balance']         =  $tixian['balance'] - $money ;
        $save['tixian_status']        =  "1";
        $save['tixian_ing']      =  $tixian_ing;
        $save['tixian_card_id']  =  $card_id;

        $result = $this->wallet_repairer_model->where(array('user_id'=>$user_id))->save($save);


//        $sqlq = $this->wallet_repairer_model->getLastSql();
//
//        $assadasldjasd['val'] =  $sqlq;
//        D('test')->add($assadasldjasd);

       if($result){
           exit($this->returnApiSuccess());
       }else{
           exit($this->returnApiError(BaseController::FATAL_ERROR,'提现失败'));
       }

    }




}