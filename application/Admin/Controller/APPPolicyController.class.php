<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class APPPolicyController extends AdminbaseController{
    protected  $policy_model;
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }
    private function initialize()
    {
        $this->policy_model = D('policy_app');
    }
    public function index(){
        $data = $this->policy_model->order('create_time')->select();
        $list = $this->send($data);
        $this->assign('list',$list);
        $this->display('list');
    }
    public function detail(){
        $map['id'] = I('get.id');
        $list= $this->policy_model->where($map)->find();
        if( $list['status']==2)$list['status']='审核通过';
        if( $list['status']==1)$list['status']='未审核';
        $list['content'] =  html_entity_decode( $list['content']);
        $this->assign('list',$list);
        $this->display('detail');
    }
    public function edit(){
        $map['id'] = I('get.id');
        $list= $this->policy_model->where($map)->find();

        $list['status'] == 1 ? $f = " selected='selected' " : ' ';
        $list['status'] == 2 ? $g = " selected='selected' " : ' ';
        $status = "<option " . $f . "  value='1'>未审核</option>" .
                  "<option " . $g . "  value='2'>已审核</option>";
        $list['content'] = html_entity_decode($list['content']);
        $this->assign('status',$status);
        $this->assign('list',$list);
        $this->display('edit');

    }
    public function add(){
        $this->display('add');
    }
    public function add_policy(){
        $add = I('post.');
        $add['create_time'] = time();
        $result = $this->policy_model->add($add);
        if($result){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }

    }
    public function edit_policy(){
        if($this->policy_model->create()){
            $result = $this->policy_model->save();
            if($result){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }else{
            exit($this->policy_model->getError());
        }

    }
    public function del(){
        $map['id'] = I('get.id');
        $result = $this->policy_model->where($map)->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function send($list){
        for($i=0;$i<count($list);$i++){
            if( $list[$i]['type']==1) $list[$i]['type']='服务中心接单指南';
            if( $list[$i]['type']==2) $list[$i]['type']='生成子账号操作指南';
            if( $list[$i]['type']==3) $list[$i]['type']='服务质量管理规章';
            if( $list[$i]['type']==4) $list[$i]['type']='工单完结必读';
            if( $list[$i]['status']==2)$list[$i]['status']='审核通过';
            if( $list[$i]['status']==1)$list[$i]['status']='未审核';
        }
        return $list;
    }

}