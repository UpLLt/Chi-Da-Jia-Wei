<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 13:39
 */
namespace Lianbao_PC\Controller;
use Think\Controller;
class SChargeController extends ServiceBaseController{
    protected $pro_price_detail_model;
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }
    public function initialize(){
        $this->pro_price_detail_model = D('pro_price_detail');
    }

    public function index(){
        $this->first();
        $this->pinlei();
        $this->display('index');
    }
    private function first(){
        $proportion = D('wallet_admin')->field('proportion')->find();
        $result = $this->pro_price_detail_model->field('pro_pinlei,product')->limit(1)->find();
        $map['pinlei'] = $result['pro_pinlei'];
        $map['product']= $result['product'];

        $strshi="	<div class=\"show_main\">
				<div class=\"charge_title\"><h1>".$result['product']."</h1></div>
				<div class=\"charge_style\">";

        $strmo ="	</div>
			        </div>";

        $list   =  $this->pro_price_detail_model->group('pro_property')->field('pro_property')->where($map)->select();
        $strzh = '';
        $str4 ='';

        for($i=0;$i<count($list);$i++){
            $map['pro_property'] = $list[$i][0];
            $result = $this->pro_price_detail_model->where(array('pro_property'=>$list[$i]['pro_property']))->select();


            $str2 ='<div class="charge_class"><div class="charge_more"><h1>'.$list[$i]['pro_property'].'</h1></div>';


            $str3 = '<div class="charge_cent">
							<table class="cent_table">';
            $str4 = '';
            for($j=0;$j<count($result);$j++){
                $str4 .=				'<tr>
									<td width="160">'.$result[$j]['service_project'].'</td>
									<td width="161">'.$result[$j]['order_type'].'</td>
									<td width="230">默认价格'.round($result[$j]['pro_price']*(1-$proportion['proportion'])).'元</td>
									<td width="250">'.$result[$j]['service_content'].'</td>
							        </tr>';



            }
            $str5 =	'</table>
						</div>
					</div>';

            $strzh .= $str2.$str3.$str4.$str5;
        }

        $str =$strshi.$strzh.$strmo;
        $this->assign('str', $str);
    }
    private function pinlei(){
        $pinlei = $this->pro_price_detail_model->field('pro_pinlei')->select();
        $data = array_unique_fb($pinlei);
        $data = array_values($data);
        for($i=0;$i<count($data);$i++){
            $map['pro_pinlei'] = $data[$i][0];
            $data[$i]['product'] = $this->pro_price_detail_model->where($map)->field('product')->select();
            $data[$i]['product'] = array_unique_fb($data[$i]['product']);
                for($j=0;$j<count($data[$i]['product']);$j++){
                    $data[$i]['product'][$j]['pinlei'] =  $data[$i][0];
                }
        }

        $this->assign('list',$data);
    }
    public function change(){
        $proportion = D('wallet_admin')->field('proportion')->find();
        $map['pinlei'] = I('post.pinlei');
        $map['product']= I('post.product');

        $strshi="	<div class=\"show_main\">
				<div class=\"charge_title\"><h1>".I('post.product')."</h1></div>
				<div class=\"charge_style\">";

        $strmo ="	</div>
			        </div>";

        $list   =  $this->pro_price_detail_model->group('pro_property')->field('pro_property')->where($map)->select();

        $strzh = '';
        $str4 ='';
        for($i=0;$i<count($list);$i++){

        $result = $this->pro_price_detail_model->where(array('pro_property'=>$list[$i]['pro_property']))->select();


        $str2 ='<div class="charge_class"><div class="charge_more"><h1>'.$list[$i]['pro_property'].'</h1></div>';
									

		$str3 = '<div class="charge_cent">
							<table class="cent_table">';
        for($j=0;$j<count($result);$j++){
            $str4 .=				'<tr>
									<td width="160">'.$result[$j]['service_project'].'</td>
									<td width="161">'.$result[$j]['order_type'].'</td>
									<td width="230">默认价格'.round($result[$j]['pro_price']*(1-$proportion['proportion'])).'元</td>
									<td width="250">'.$result[$j]['service_content'].'</td>
							        </tr>';



        }
		$str5 =	'</table>
						</div>
					</div>';

        $strzh .= $str2.$str3.$str4.$str5;
        }

        $str =$strshi.$strzh.$strmo;

        $this->ajaxReturn($str);exit;

    }

}