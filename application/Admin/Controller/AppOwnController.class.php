<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 17:01
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class AppOwnController extends AdminbaseController{

    /**
     * 常见问题
     */
    public function user_agreement(){
        $platform = D('policy')->where(array('type'=>9))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "常见问题";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "9";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>9))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('user_agreement');
    }



    /**
     * 修改常见问题
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
     * 联系我们
     */
    public function connect_us(){
        $platform = D('policy')->where(array('type'=>11))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "联系我们";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "11";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>11))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('connect_us');
    }


    /**
     * 修改联系我们
     */
    public function edit_connect_us(){
        $result = D('policy')->where(array('id'=>I('id')))->setField('content',I('content'));
        if($result) {
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }


    /**
     * 家电维修说明
     */
    public function repair_explain(){
        $platform = D('policy')->where(array('type'=>12))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "家电维修说明";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "12";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>12))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('repair_explain');
    }


    /**
     * 修改家电维修说明
     */
    public function edit_repair_explain(){
        $result = D('policy')->where(array('id'=>I('id')))->setField('content',I('content'));
        if($result) {
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }


    /**
     * 家电清洗说明
     */
    public function clean_explain(){
        $platform = D('policy')->where(array('type'=>13))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "家电清洗说明";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "13";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>13))->field('content,id')->find();
        }
        $this->assign('list',$platform);
        $this->display('clean_explain');
    }


    /**
     * 修改家电清洗说明
     */
    public function edit_clean_explain(){
        $result = D('policy')->where(array('id'=>I('id')))->setField('content',I('content'));
        if($result) {
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }


    /**
     * 关于驰达家维
     */
    public function about_platform(){
        $platform = D('policy')->where(array('type'=>10))->field('content,id')->find();
        $platform['content'] =  html_entity_decode($platform['content']);
        if( empty( $platform['id']) ){
            $add['title']       = "关于驰达家维";
            $add['status']      = "2";
            $add['create_time'] = time();
            $add['type']        = "10";
            D('policy')->add($add);
            $platform = D('policy')->where(array('type'=>10))->field('content,id')->find();
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
     * 版本号_列表_个人端
     */

    public function version_number(){
        $count = D('verson_number')->where(array('app_type'=>1))->count();
        $page = $this->page($count, 20);
        $list  = D('verson_number')->where(array('app_type'=>1))->order('create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $page = $page->show('Admin');
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display('list_verson');
    }

    /**
     * 修改版本号_显示_个人端
     */
    public function edit_verson_list(){
        $list =                   D('verson_number')->where(array('id'=>I('id')))->find();
        $this->assign('list',$list);
        $this->display('edit_verson');
    }

    /**
     * 修改版本号_个人端
     */
    public function edit_verson(){

        if(D('verson_number')->create()){
            $result = D('verson_number')->save();
            if( $result ){
                $this->success('修改成功',U('AppOwn/version_number'));
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
     * 增加版本号_页面_个人端
     */
    public function add_verson_list(){
        $this->display('add_verson');
    }

    /**
     * 增加版本号_个人端
     */
    public function add_verson(){
        $data                = I('post.');
        $data['create_time'] = time();
        $data['app_type']    = 1;
        if(D('verson_number')->create($data)){
            $result = D('verson_number')->add($data);
            if( $result ){
                $this->success('增加成功',U('AppOwn/version_number'));
            }else{
                $this->error('增加失败');
            }
        }else{
            exit(D('verson_number')->getError());
        }
    }



}