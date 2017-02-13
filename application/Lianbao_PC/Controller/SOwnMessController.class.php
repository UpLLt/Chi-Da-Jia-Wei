<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 11:03
 */
namespace Lianbao_PC\Controller;

use Think\Controller;

class SOwnMessController extends ServiceBaseController
{
    protected $id;
    protected $user_model;
    protected $user_service_model;
    protected $user_rec_address;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    private function initialize()
    {
        $this->id = session('user_id');
        $this->user_model = D('user');
        $this->user_service_model = D('UserService');
        $this->user_rec_address = D('RecAddress');
    }

    public function index()
    {
        $wh_user['id'] = $this->id;
        $wh_service['user_id'] = $this->id;
        $user = $this->user_model
            ->where($wh_user)
            ->field('username')
            ->find();
        $user_ser = $this->user_service_model
            ->where($wh_service)
            ->find();
        $user_ser['username'] = $user['username'];
        $user_ser['id_card'] = id_card($user_ser['id_card']);
        $this->assign('user', $user_ser);
        $this->display();
    }

    /**
     * 修改手机号码
     */
    public function modify_phone()
    {
        $map['user_id'] = $this->id;
        $save['phone'] = I('post.phone');
        if (strlen(I('post.phone')) == 11) {
            $service = $this->user_service_model
                ->where($map)
                ->save($save);
            if ($service) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        } else {
            $this->error("电话号码不正确");
        }
    }

    /**
     * 修改邮箱
     */
    public function modify_email()
    {
        $map['user_id'] = $this->id;
        $save['email'] = I('post.email');
        $email = filter_var(I('post.email'), FILTER_VALIDATE_EMAIL);
        if ($email) {
            $service = $this->user_service_model
                ->where($map)
                ->save($save);
            if ($service) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        } else {
            $this->error("邮箱格式不正确");
        }
    }

    /**
     * 修改地址;
     */
    public function modify_address()
    {
        $map['user_id'] = $this->id;
        $save['com_address'] = I('post.address');
        if (strlen(I('post.address')) >= 8) {
            $service = $this->user_service_model
                ->where($map)
                ->save($save);
            if ($service) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        } else {
            $this->error("请填写详细公司地址");
        }
    }

    /**
     * 修改QQ;
     */
    public function modify_qq()
    {
        $map['user_id'] = $this->id;
        $save['qq'] = I('post.qq');
        if (strlen(I('post.qq')) >= 6 && strlen(I('post.qq')) <= 13) {
            $service = $this->user_service_model
                ->where($map)
                ->save($save);
            if ($service) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        } else {
            $this->error("QQ号码格式不正确");
        }
    }

    /**
     * 修改营业执照
     */
    public function modify_business_license()
    {
        $field = 'business_license';
        $this->upload_model($field);
    }

    /**
     * 修改tax登记证
     */
    public function modify_tax_regis()
    {
        $field = 'tax_regis';
        $this->upload_model($field);
    }

    /**
     * 修改特殊资质证
     */
    public function modify_special_regis()
    {
        $field = 'special_regis';
        $this->upload_model($field);
    }

    public function address()
    {
        $this->default_address();
        $this->_address();
        $this->display('address');
    }

    private function default_address()
    {
        $map['user_id'] = $this->id;
        $map['status'] = 1;
        $default_address = $this->user_rec_address
            ->where($map)
            ->find();
        $this->assign('default_address', $default_address);

    }

    private function _address()
    {
        $map['user_id'] = $this->id;
        $map['status'] = 0;
        $default_address = $this->user_rec_address
            ->where($map)
            ->order("id DESC")
            ->order('5')
            ->select();
        $str_address = " ";
        $js_address = " ";
        $abc = " ";
        for ($i = 0; $i < count($default_address); $i++) {

            $str_address .= "<li>" .
                "<h1>" . $default_address[$i]['rec_name'] . "<span>" . $default_address[$i]['rec_phone'] . "</span> <b>待默认地址</b></h1>" .
                "<p>" . $default_address[$i]['rec_address'] . "<span>" .
                "<a href=\"#layer_edit_" . $i . "\" id=\"layer_edits_" . $i . "\" >编辑</a>" .
                "<a href=" . U('Lianbao_PC/SOwnMess/del_address', array('id' => $default_address[$i]['id'])) . " id=\"delete_" . $i . "\" >删除</a>" .
                "</span></p>" .
                "</li>";
            $abc .= $i . ",";
            $default_address[$i]['number'] = $i;
        }

        $this->assign('_address', $default_address);
        $this->assign('str_address', $str_address);
    }

    /**
     * 修改收货人信息
     */
    public function edit()
    {
        if (I('post.id') == 1) {
            $map['status'] = 1;
            $map['user_id'] = $this->id;
            $this->user_rec_address->where($map)->setField('status', '0');
        }
        if ($this->user_rec_address->create($_POST)) {
            $result = $this->user_rec_address->save();
            if ($result) {

                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        } else {
            exit($this->user_rec_address->getError());
        }
    }

    /**
     * 删除收货人信息
     */
    public function del_address()
    {
        $map['id'] = I('get.id');
        $result = $this->user_rec_address->where($map)->delete();
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 增加收货人信息
     */
    public function add_address_list()
    {
        $user_id = $this->id;
        $this->assign('user_id', $user_id);
        $this->display('add_address');
    }


    public function add_address()
    {
        if (I('post.id') == 1) {
            $map['status'] = 1;
            $map['user_id'] = $this->id;
            $this->user_rec_address->where($map)->setField('status', '0');
        }
        if ($this->user_rec_address->create()) {
            $result = $this->user_rec_address->add();
            if ($result) {
                $this->success('新增成功');
            } else {
                $this->error('新增失败');
            }
        } else {
            exit($this->user_rec_address->getError());
        }
    }

    /**
     * 业务信息
     */
    public function business()
    {
        $this->display('business');
    }

    /**
     *安全设置
     */
    public function setting()
    {
        $this->display('setting');
    }

    /**
     * 修改密码
     */
    public function modify_password(){
        $password      = I('post.password');
        $new_password  = I('post.new_password');
        $re_password   = I('post.re_password');
        if(empty($password) || empty($new_password) || empty($re_password)){
                $this->error('请补全表单');
        }
        if($new_password!==$re_password){
            $this->error('两次密码不一致');
        }
        $map['password'] = md5($password);
        $map['id']  = $this->id;
        $result = $this->user_model->where($map)->find();
        if(empty($result)){
            $this->error('原密码不正确');
        }
        $res = $this->user_model->where($map)->setField('password',md5($new_password));
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }

    }

    /**
     * 修改提现密码
     */
    public function modify_pay_password(){
        $password      = I('post.password');
        $new_password  = I('post.new_password');
        $re_password   = I('post.re_password');
        if(empty($password) || empty($new_password) || empty($re_password)){
            $this->error('请补全表单');
        }
        if($new_password != $re_password){
            $this->error('两次密码不一致');
        }
        $map['taking_password'] = md5($password);
        $map['id']  = $this->id;
        $result = $this->user_model->where($map)->field('id')->find();
        if(empty($result['id'])){
            $this->error('原密码不正确');
        }
            if(strlen($new_password) >= 6 ){
                $res = $this->user_model->where(array('id'=>$this->id))->setField('taking_password',md5( $re_password ));
            }else{
                $this->error('密码不能低于六位');
            }


        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }

    }

    /**
     * 找回密码，重置密码
     */
    public function lose_password(){
        $map['username'] = I('username');
        $end_time = D('shot_message')->where($map)->field('end_time,code')->find();
        $shot_message = I('vcode');
        if($shot_message != $end_time['code']){
            $this->error('验证码不正确');
        }else{
            if(time() > $end_time['end_time']){
                $this->error('验证码超时，请重新获取');
            }else{
                if(I('password') == I('re_password')){
                    if(strlen(I('password'))>=6){
                        $result	= D('user')->where($map)->setField('taking_password',md5(I('password')));
                        if($result){
                            $this->success('支付密码重置成功');
                        }else{
                            $this->error('失败，不能与原密码一致');
                        }
                    }else{
                        $this->error('密码位数必须大于六位，请重新输入');
                    }
                }else{
                    $this->error('两次密码不一致，请重新输入');
                }
            }

        }
    }



    /**
     * @param $field 数据库字段名称
     * 上传图片
     */
    private function upload_model($field)
    {
        $map['user_id'] = $this->id;
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = 'data/upload/';
        $upload->savePath = '';
        $info = $upload->upload();
        $save[$field] = './data/upload/' . $info[$field]["savepath"] . $info[$field]["savename"];
        $success = $this->user_service_model
            ->where($map)
            ->save($save);
        if ($success) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }

    }


}