<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class AppInformController extends AdminbaseController
{
    public function index()
    {

        $this->display();
    }

    public function edit()
    {
        $id = I('id');
        $map['id'] = $id;
        $inform_user = M('inform_user')->where($map)->order('create_time desc')->find();
        $inform_user['type'] == 3 ? $c = " selected='selected' " : ' ';
        $inform_user['type'] == 4 ? $f = " selected='selected' " : ' ';
        $inform_user['type'] == 5 ? $g = " selected='selected' " : ' ';
        $inform_user['type'] == 6 ? $h = " selected='selected' " : ' ';
        $type = "<option " . $c . " value='3'>工单消息</option>" .
                "<option " . $f . " value='4'>交易消息</option>" .
                "<option " . $g . " value='5'>活动消息</option>" .
                "<option " . $h . " value='6'>交易消息</option>" ;

        $inform_user['status'] == 1 ? $d = " selected='selected' " : ' ';
        $inform_user['status'] == 2 ? $e = " selected='selected' " : ' ';
        $status = "<option " . $d . "  value='1'>待发布</option>" .
                  "<option " . $e . "  value='2'>发布</option>";
        $this->assign('inform_user', $inform_user);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->display();

    }

    public function add_inform()
    {

        $add = I('post.');
        $id = I('post.inform_id');
        if (empty($id)) {
            $add['create_time'] = time();
            $add['content'] = htmlspecialchars_decode( $add['content']);
            $result = M('inform_user')->add($add);
            if ($result) {
                $this->success("添加成功",U('AppInform/info_list'));
            } else {
                $this->error("添加失败");
            }
        }else{
            $map['id']=$id;
            $result = M('inform_user')->where($map)->save($add);
            if ($result) {
                $this->success("修改成功");
            } else {
                $this->error("修改失败");
            }
        }
    }
    public function detail(){
        $id = I('get.id');
        $map['id'] = $id;
        $inform = M('inform_user')->where($map)->find();
        if($inform['type']==3)$inform['type']='工单消息';
        if($inform['type']==4)$inform['type']='交易消息';
        if($inform['type']==5)$inform['type']='活动消息';
        if($inform['type']==6)$inform['type']='平台政策';
        if($inform['status']==1)$inform['status']='待发表';
        if($inform['status']==2)$inform['status']='发表';

        $this->assign('inform',$inform);
        $this->display();

    }

    public function info_list()
    {
        $map['type'] = array(array('eq', 3), array('eq', 4), array('eq', 5), array('eq', 6) , 'or');
        $count = M('inform_user')->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('inform_user')->where($map)->order('create_time')->limit($page->firstRow . ',' . $page->listRows)->select();

        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['type'] == 3) {
                $list[$i]['type'] = '工单消息';
            };
            if ($list[$i]['type'] == 4) {
                $list[$i]['type'] = '交易消息';
            };
            if ($list[$i]['type'] == 5) {
                $list[$i]['type'] = '活动消息';
            };
            if ($list[$i]['type'] == 6) {
                $list[$i]['type'] = '平台政策';
            };
            if ($list[$i]['status'] == 1) {
                $list[$i]['status'] = '待发表';
            };
            if ($list[$i]['status'] == 2) {
                $list[$i]['status'] = '发表';
            };
        }
        $page = $page->show('Admin');
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display('list');

    }

    public function search()
    {


        if(empty( I('post.keyword') )){
            $map['type'] = array(array('eq', 3), array('eq', 4), array('eq', 5), array('eq', 6) , 'or');
            $list = M('inform_user')->order('create_time desc')->where($map)->select();
        }else{
            $map['title'] = I('post.keyword');
            $list = M('inform_user')->order('create_time desc')->where($map)->select();
        }
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['type'] == 3) {
                $list[$i]['type'] = '工单消息';
            };
            if ($list[$i]['type'] == 4) {
                $list[$i]['type'] = '交易消息';
            };
            if ($list[$i]['type'] == 5) {
                $list[$i]['type'] = '活动消息';
            };
            if ($list[$i]['type'] == 6) {
                $list[$i]['type'] = '平台政策';
            };
            if ($list[$i]['status'] == 1) {
                $list[$i]['status'] = '待发表';
            };
            if ($list[$i]['status'] == 2) {
                $list[$i]['status'] = '发表';
            };
        }

        $this->assign('list', $list);
        $this->display('list');
    }

    public function del()
    {
        $map['id'] = I('id');
        $result = M('inform_user')->where($map)->delete();
        if ($result) {
            $this->success("添加成功");
        } else {
            $this->error("添加失败");
        }
    }


    /**
     * 接单必读分类展示页面
     */

    public function index_portal(){
        $map['grade'] = array(array('eq', 1), array('eq', 2), 'or');
        $one  = D('policy_portal')->where($map)->select();
        for($i=0;$i<count($one);$i++){
             $parent = D('policy_portal')->where(array('id'=>$one[$i]['parent_id']))->field('name')->find();
             $one[$i]['parent_id'] = empty($parent['name']) ? "没有父级" : $parent['name'];
        }
        $this->assign('list',$one);
        $this->display('index_portal');
    }

    /**
     * 删除分类
     */
    public function del_portal(){
        $id = I('id');
        $grade = D('policy_portal')->where(array('parent_id'=>$id))->field('grade')->find();
        if($grade['grade']){
            $this->error('删除失败，该分类下面还存在子类');
        }else{
            $result = D('policy_portal')->where(array('id'=>$id))->delete();
            if($result){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
    /**
     * 修改分类
     */
    public function edit_portal_list(){
        $id = I('id');
        $policy_portal =  D('policy_portal')->where(array('id'=>$id))->find();

        $one  = D('policy_portal')->field('id,name')->where(array('grade'=>1))->select();

        $list = "";

        for($i=0;$i<count($one);$i++){
            if( $policy_portal['parent_id'] == $one[$i]['id'] ) {
                $check = "selected";
            }else{
                $check = "";
            }
            $list .= "<option selected='".$check."' value='".$one[$i]['id']."'>".$one[$i]['name']."</option>";
        }


        $this->assign('list',$list);
        $this->assign('data',$policy_portal);
        $this->display('edit_portal');
    }

    /**
     * 接单必读分类首页
     */
    public function add_portal_list(){
        $one  = D('policy_portal')->field('id,name')->where(array('grade'=>1))->select();
        $list = "";

        for($i=0;$i<count($one);$i++){
            $list .= "<option value='".$one[$i]['id']."'>".$one[$i]['name']."</option>";
        }
        $this->assign('list',$list);
        $this->display('add_portal');
    }



    /**
     * 接单必读分类添加
     */
    public function add_portal(){
        $parent  = I('parent');
        $data['time'] = time();
        $data['parent_id'] = $parent;
        $data['name'] = I('name');

        $data['description'] = I('description');
       if($parent == 0){
            $data['grade']  = 1;
       }else{
            $data['grade']  = 2;
       }

       if(I('id')){
           $result = D('policy_portal')->where(array('id'=>I('id')))->save($data);
       }else{
           $data['create_time'] = time();
           $result = D('policy_portal')->add($data);
       }

        if($result){
            $this->success('操作成功',U('AppInform/index_portal'));
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 接单必读
     */

    public function taking_read(){
        $count = D('policy_app')->count();

        $page = $this->page($count, 20);

        $list = D('policy_app')->order('create_time')->limit($page->firstRow . ',' . $page->listRows)->select();

        for($i=0;$i<count($list);$i++){
           if($list[$i]['status']== 1) $list[$i]['status'] = "待审核";
           if($list[$i]['status']== 2) $list[$i]['status'] = "已审核";
        }


        $page = $page->show('Admin');
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->display('taking_read');
    }

    /**
     * 添加接单必读
     */

    public function taking_add_list(){
        $one  = D('policy_portal')->field('id,name')->where(array('grade'=>1))->select();
        $list = "";
        $two_a  = "";
        for($i=0;$i<count($one);$i++){
            $list .= "<option value='".$one[$i]['name']."'>".$one[$i]['name']."</option>";
        }

        $two = D('policy_portal')->where(array('parent_id'=>$one['0']['id']))->select();

        for($j=0;$j<count($two);$j++){
            $two_a .= "<option  class='two_type_name' value='".$two[$j]['name']."'>".$two[$j]['name']."</option>";
        }

        $this->assign('two',$two_a);
        $this->assign('list',$list);
        $this->display('taking_add');
    }

    /**
     * 添加接单 改变二级opition
     */
    public function taking_option(){
        $name = I('name');
        $id = D('policy_portal')->where(array('name'=>$name))->field('id')->find();

        $option = D('policy_portal')->where(array('parent_id'=>$id['id']))->field('name')->select();
        foreach ($option as $v) {
            $v = implode(",", $v);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        rsort($temp);

        for ($i = 0; $i < count($temp); $i++) {
            $abc[$i]['name'] = $temp[$i];
        }
        foreach ($abc as $e) {
            $test[] = $e['name'];
        }
        json_encode($test);

        $this->ajaxReturn_e($test);

    }

    /**
     *添加接单必读
     */
    public function add_policy(){
        $add = I('post.');
        $add['type_name']   = "接单必读";
        $add['create_time'] = time();
        if(empty(I('two_type_name')) || empty(I('one_type_name'))){

            $this->error('添加失败,一级或二级标题不能为空');

        }

        if(empty($add['id'])){
            $add['create_time'] = time();
            $result = D('policy_app')->add($add);
        }else{
            $result = D('policy_app')->where(array('id'=>I('id')))->save($add);
        }


        if($result){

            $this->success('添加成功',U('AppInform/taking_read'));

        }else{

            $this->error('添加失败');

        }

    }

    /**
     * 接单必读删除
     */
    public function taking_del(){
        $id = I('id');
        $result = D('policy_app')->where(array('id'=>$id))->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


    /**
     *接单必读详情
     */

    public function taking_detail(){
        $map['id'] = I('id');
        $list      = D('policy_app')->where($map)->find();
        if( $list['status']==2)$list['status']='审核通过';
        if( $list['status']==1)$list['status']='未审核';
        $list['content'] =  html_entity_decode( $list['content']);
        $this->assign('list',$list);
        $this->display('taking_detail');
    }


    /**
     *接单必读修改
     */

    public function taking_edit_list(){
        $data = D('policy_app')->where(array('id'=>I('id')))->find();
        $tyep_id = D('policy_portal')->where(array('name'=>$data['one_type_name']))->field('id')->find();
        $one  = D('policy_portal')->field('id,name')->where(array('grade'=>1))->select();
        $list = "";
        $two_a  = "";
        for($i=0;$i<count($one);$i++){
            if($data['one_type_name'] == $one[$i]['name']){
                $check =  $check = "selected";
            }else{
                $check =  "";
            }
            $list .= "<option selected='".$check."' value='".$one[$i]['name']."'>".$one[$i]['name']."</option>";
        }

        $two = D('policy_portal')->where(array('parent_id'=>$tyep_id['id']))->select();

        for($j=0;$j<count($two);$j++){
            if($data['two_type_name'] == $two[$j]['name']){
                $chec = "selected";
            }else{
                $chec =  "";
            }

            $two_a .= "<option selected='".$chec."'  class='two_type_name' value='".$two[$j]['name']."'>".$two[$j]['name']."</option>";
        }
        if($data['status'] ==1 ){
            $status = "	<option selected='selected' value=\"1\">未审核</option><option value=\"2\">审核</option>";
        }else{
            $status = "	<option  value=\"1\">未审核</option><option selected='selected' value=\"2\">审核</option>";
        }

        $this->assign('status',$status);
        $this->assign('data',$data);
        $this->assign('two',$two_a);
        $this->assign('list',$list);
        $this->display('taking_edit');
    }

    /**
     * 关于驰达家维
     */
    public function about_platform(){
        $platform = D('policy')->where(array('type'=>6))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "关于驰达家维";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "6";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>6))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('about_platform');
    }

    /**
     * 修改驰达家维
     */
    public function edit_platform(){
       $result = D('policy')->where(array('id'=>I('id')))->setField('content',I('content'));
       if($result) {
           $this->success('修改成功');
       }else{
           $this->error('修改失败');
       }
    }


    /**
     * 用户协议
     */
    public function user_agreement(){
        $platform = D('policy')->where(array('type'=>7))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "用户协议";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "7";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>7))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('user_agreement');
    }


    /**
     * 修改用户协议
     */
    public function edit_user_agreement(){
        $result = D('policy')->where(array('id'=>I('id')))->setField('content',I('content'));
        if($result) {
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }


    /**
     * 版本号_列表_师傅端
     */

    public function version_number(){
        $count = D('verson_number')->where(array('app_type'=>4))->count();
        $page = $this->page($count, 20);
        $list  = D('verson_number')->where(array('app_type'=>4))->order('create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $page = $page->show('Admin');
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display('list_verson');
    }

    /**
     * 修改版本号_显示_师傅端
     */
    public function edit_verson_list(){
        $list = D('verson_number')->where(array('id'=>I('id')))->find();
        $this->assign('list',$list);
        $this->display('edit_verson');
    }

    /**
     * 修改版本号_师傅端
     */

    public function edit_verson(){
        if(D('verson_number')->create()){
           $result = D('verson_number')->save();
           if( $result ){
               $this->success('修改成功',U('AppInform/version_number'));
           }else{
               $this->error('修改失败');
           }
        }else{
            exit(D('verson_number')->getError());
        }
    }

    /**
     * 删除版本号_师傅端
     */
    public function del_verson(){
        $result = D('verson_number')->where(array('id'=>I('id')))->delete();
        if($result){
            $this->success('删除成功');
        }else {
            $this->error('删除失败');
        }
    }

    /**
     * 增加版本号_页面_师傅端
     */
    public function add_verson_list(){
        $this->display('add_verson');
    }

    /**
     * 增加版本号_师傅端
     */
    public function add_verson(){
        $data                = I('post.');
        $data['create_time'] = time();
        $data['app_type']    = 4;
        if(D('verson_number')->create($data)){
            $result = D('verson_number')->add($data);
            if( $result ){
                $this->success('增加成功',U('AppInform/version_number'));
            }else{
                $this->error('增加失败');
            }
        }else{
            exit(D('verson_number')->getError());
        }
    }

}