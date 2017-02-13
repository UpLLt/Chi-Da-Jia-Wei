<?php
namespace Coupon\Controller;
use Common\Controller\AdminbaseController;
use Coupon\Model\CouponModel;
use Coupon\Model\TicketModel;


class CouponController extends AdminbaseController{

      protected $coupon_model;
      protected $ticket_model;

      public function __construct()
      {
          parent::__construct();
          $this->coupon_model = new CouponModel();
          $this->ticket_model = new TicketModel();
      }

      public function index(){
          $coupon_number = generateCode(1);

      }

      public function add_list(){

          $user = D('user')->join('as a left join lb_user_personal as b on a.id=b.user_id')
                           ->where('a.id = '.I('uid'))
                           ->field('b.rel_name,a.id')
                           ->find();

          $list = $this->ticket_model->select();
          $this->assign('user',$user);
          $this->assign('list',$list);
          $this->display('add');
      }

      public function add(){


          $ticket = $this->ticket_model
                         ->where(array('id'=>I('tid')))
                         ->find();
          if($this->coupon_model->create()){
                $data = I('post.');
                $data['ctype'] = 1;
                $data['status'] = 1;
                $data['expiration_time'] = strtotime("+" . $ticket['vaildity'] . " days");
                $data['coupon_number']   = generateCode(1);
                $data['create_time']     = time();
                $result = $this->coupon_model->add($data);
                if($result){
                    $this->success('成功',U('User/Indexadmin/index_own'));
                }else{
                    $this->error('失败');
                }


          }else{
              $this->error($this->coupon_model->getError());
          }
      }




}