<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class PCInformController extends AdminbaseController
{
    protected $link_model;
    protected  $targets=array("_blank"=>"新标签页打开","_self"=>"本窗口打开");

    function _initialize() {
        parent::_initialize();
        $this->link_model = D("Common/Links");
    }

    function index_list(){
        $links=$this->link_model->order(array("listorder"=>"asc"))->select();
        $this->assign("links",$links);
        $this->display('index_list');
    }

    function index_add(){
        $this->assign("targets",$this->targets);
        $this->display();
    }

    /**
     * 增加
     */
    function index_add_post(){
        if(IS_POST){
            if ($this->link_model->create()) {
                if ($this->link_model->add()!==false) {
                    $this->success("添加成功！", U("PCInform/index_list"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->link_model->getError());
            }

        }
    }

//删除
    function index_delete(){
        if(isset($_POST['ids'])){

        }else{
            $id = intval(I("get.id"));
            if ($this->link_model->delete($id)!==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

    }
  //修改首页图片
    function Index_edit(){
        $id=I("get.id");
        $link=$this->link_model->where("link_id=$id")->find();
        $link['type']==1?$a=" selected='selected' ":$a=' ';
        $link['type']==2?$b=" selected='selected' ":$b=' ';
        $link['type']==3?$c=" selected='selected' ":$c=' ';
        $link['type']==4?$d=" selected='selected' ":$d=' ';
        $link['type']==5?$e=" selected='selected' ":$e=' ';
        $link['type']==6?$f=" selected='selected' ":$f=' ';
        $link['type']==7?$g=" selected='selected' ":$g=' ';
        $link['type']==8?$h=" selected='selected' ":$h=' ';
        $link['type']==9?$i=" selected='selected' ":$i=' ';
        $str=	"<option". $a ."value='1'>轮播图—A</option>".
            "<option". $h ."value='8'>轮播图—B</option>".
            "<option". $i ."value='9'>轮播图—C</option>".
            "<option". $b ."value='2'>发单图片</option>".
            "<option". $c ."value='3'>接单图片</option>".
            "<option". $d ."value='4'>官方平台</option>".
            "<option". $e ."value='5'>公司地址</option>".
            "<option". $f ."value='6'>资讯热线</option>".
            "<option". $g ."value='7'>公司账号</option>";

        $this->assign('str',$str);
        $this->assign('link',$link);
        $this->assign("targets",$this->targets);
        $this->display('index_edit');
    }

    //首页图片修改
    function edit_post(){
        if (IS_POST) {
            if ($this->link_model->create()) {
                if ($this->link_model->save()!==false) {
                    $this->success("保存成功！",U("PCInform/index_list"));
                } else {
                    $this->error("保存失败！");
                }
            } else {
                $this->error($this->link_model->getError());
            }
        }
    }


    /**
     * 发单必读
     */
    public function must_read(){
        $platform = D('policy')->where(array('type'=>8))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "关于驰达家维";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "8";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>8))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('mustread');
    }

    /**
     * 修改关于平台
     */
    public function edit_must_read(){
        $result = D('policy')->where(array('id'=>I('id')))->setField('content',I('content'));
        if($result) {
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

}