<?php
namespace Own\Controller;
use Think\Controller;
class SetUpController extends BaseController{
        protected $policy_model;
        protected $links_model;

    public function __construct()
    {

        parent::__construct();
        $this->policy_model = D('policy');
        $this->links_model  = D('links');

    }

    /**
     * 常见问题
     */

    public function agreement(){
       $list = $this->policy_model
                    ->where(array('type'=>9))
                    ->field('content')
                    ->find();
       $list['content'] = htmlspecialchars_decode($list['content']);
       $this->assign('list',$list);
       $this->display('about_us');
    }

    /**
     * 关于我们
     */

    public function about_us(){
        $list = $this->policy_model
            ->where(array('type'=>10))
            ->field('content')
            ->find();
        $list['content'] = htmlspecialchars_decode($list['content']);
        $this->assign('list',$list);
        $this->display('about_us');
    }

    /**
     * 联系我们
     */

    public function connect_us(){
        $list = $this->policy_model
            ->where(array('type'=>11))
            ->field('content')
            ->find();
        $list['content'] = htmlspecialchars_decode($list['content']);
        $this->assign('list',$list);
        $this->display('about_us');
    }


    /**
     * 家电维修说明
     */

    public function repair_explain(){
        $list = $this->policy_model
            ->where(array('type'=>12))
            ->field('content')
            ->find();
        $list['content'] = htmlspecialchars_decode($list['content']);
        $this->assign('list',$list);
        $this->display('about_us');
    }

    /**
     * 家电清洗说明
     */

    public function clean_explain(){
        $list = $this->policy_model
            ->where(array('type'=>13))
            ->field('content')
            ->find();
        $list['content'] = htmlspecialchars_decode($list['content']);
        $this->assign('list',$list);
        $this->display('about_us');
    }

    /**
     * 联系客服
     */

    public function connect(){
        $service_phone = $this->links_model
                              ->where(array('type'=>6))
                              ->field('link_description')
                              ->find();
        exit($this->returnApiSuccess($service_phone));
    }

}