<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 17:01
 */
namespace AdOwn\Controller;
use AdOwn\Model\LinksModel;
use Common\Controller\AdminbaseController;
class BannerController extends AdminbaseController{

      protected $links_model;
      public function __construct()
      {
          parent::__construct();
          $this->links_model = new LinksModel();
      }

    /**
     * 首页
     */
      public function index(){
            $list = $this->links_model
                         ->where('type = 10')
                         ->select();
            $this->assign('links',$list);
            $this->display();

      }

    /**
     * 增加显示
     */
      public function add_list(){
            $this->display('add');
      }

     /**
      * 增加图片
      */
      public function add(){
            if($this->links_model->create()){
                $result = $this->links_model->add();
                if($result){
                    $this->success('成功',U('Banner/index'));
                }else{
                    $this->error('失败');
                }
            }else{
                $this->error($this->links_model->getError());
            }
      }

    /**
     * 删除图片
     */

    public function delete(){

            $result = $this->links_model
                           ->where(array('link_id'=>I('link_id')))
                           ->delete();
            if($result){
                $this->success('成功');
            }else{
                $this->error('失败');
            }
    }


    /**
     * 修改首页
     */
    public function edit_list(){

            $list = $this->links_model
                         ->where(array('link_id'=>I('link_id')))
                         ->find();
            $this->assign('link',$list);
            $this->display('edit');
    }

    /**
     * 修改
     */
    public function edit(){

            if($this->links_model->create()){
                $result = $this->links_model->where(array('link_id'=>I('link_id')))->save();
                if($result){
                    $this->success('成功',U('Banner/index'));
                }else{
                    $this->error('失败');
                }

            }else{
                $this->error($this->links_model->getError());
            }
    }
}