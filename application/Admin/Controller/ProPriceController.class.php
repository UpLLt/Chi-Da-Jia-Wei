<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ProPriceController extends AdminbaseController
{
    public function index()
    {

        $leibie = D('pro_price')->field('pro_pinlei')->group('pro_pinlei')->select();
        $key    = I('pinlei');
        $_GET['pinlei'] = $key;

        for ($j = 0; $j < count($leibie); $j++) {
            if ($key == $leibie[$j]['pro_pinlei']) {
                $ab .= "<option" . " selected='selected' " . "value='" . $leibie[$j]['pro_pinlei'] . "'>" . $leibie[$j]['pro_pinlei'] . "</option>";
            } else {
                $ab .= "<option value='" . $leibie[$j]['pro_pinlei'] . "'>" . $leibie[$j]['pro_pinlei'] . "</option>";
            }
        }

        if (empty($key)) {
            $count = M('pro_price_detail')->count();
            $page = $this->page($count, 20);
            $list = M('pro_price_detail')->order('create_time DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        } else {
            $map['pro_pinlei'] = $key;

            $count = M('pro_price_detail')->where($map)->count();
            $page = $this->page($count, 20);
            $list = M('pro_price_detail')->where($map)->order('create_time DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        }


        $this->assign('pro_pinlei', $ab);
        $this->assign('page', $page->show('Admin'));
        $this->assign('list', $list);
        $this->display();

    }


    /*
     * 删除产品
     */
    public function del()
    {
        $id = $_GET['id'];
        $del = M('pro_price_detail')->where("id='{$id}'")->delete();
        D('pro_price_service')->where("pid='{$id}'")->delete();
        if ($del) {
            $this->success("删除成功", U("ProPrice/index"));
        } else {
            $this->error("删除失败");
        }


    }

    /*
     * 属性列表
     */
    public function property_list()
    {
        $leibie = D('pro_price')->field('pro_pinlei')->group('pro_pinlei')->select();
        $key = I('pinlei');
        $_GET['pinlei'] = $key;

        for ($j = 0; $j < count($leibie); $j++) {
            if ($key == $leibie[$j]['pro_pinlei']) {
                $ab .= "<option" . " selected='selected' " . "value='" . $leibie[$j]['pro_pinlei'] . "'>" . $leibie[$j]['pro_pinlei'] . "</option>";
            } else {
                $ab .= "<option value='" . $leibie[$j]['pro_pinlei'] . "'>" . $leibie[$j]['pro_pinlei'] . "</option>";
            }
        }
        if (empty($key)) {
            $count = D('pro_price')->count();
            $page = $this->page($count, 20);
            $list = M('pro_price')->order('id DESC')->limit($page->firstRow . ',' . $page->listRows)->select();
        } else {
            $_GET['pro_pinlei'] = $key;
            $map['pro_pinlei'] = $key;
            $count = D('pro_price')->where($map)->count();
            $page = $this->page($count, 20);
            $list = M('pro_price')->order('id DESC')->where($map)->limit($page->firstRow . ',' . $page->listRows)->select();
        }

        $this->assign('list', $list);
        $this->assign('page', $page->show('Admin'));
        $this->assign('leibie', $ab);
        $this->display('property_list');
    }


    /*
    * 删除分类属性
    */
    public function del_property()
    {
        $id = $_GET['id'];
        $del = M('pro_price')->where("id='{$id}'")->delete();
        if ($del) {
            $this->success("删除成功", U("ProPrice/property_list"));
        } else {
            $this->error("删除失败");
        }
    }

    public function pro_edit_list(){
        $id = I('id');
        $list = D('pro_price')->where(array('id'=>$id))->find();
        $this->assign('list',$list);
        $this->display('edi_proper');

    }



    /**
     * 增加
     */
    public function add()
    {
        $pinlei = M('pro_price')->field('pro_pinlei')->select();
        foreach ($pinlei as $v) {
            $v = implode(",", $v);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        rsort($temp);
        for ($i = 0; $i < count($temp); $i++) {
            $abc[$i]['pro_pinlei'] = $temp[$i];
        }
        $this->assign('pro_pinlei', $abc);
        $this->display();
    }


    /*
     * 增加属性
     */
    public function add_property()
    {
        $proprice = D('ProPinlei')->field('pro_pinlei')->select();
        $this->assign('pinlei', $proprice);
        $this->display();
    }

    /*
     * 增加分类属性
     */
    public function property()
    {
        $map['pro_pinlei'] = I('post.pro_pinlei');
        $map['product'] = I('post.product');
        $post = I('post.');
        $pro_price = D('pro_price')->where($map)->field('id')->find();
        if ($pro_price['id']) {
            $this->error("添加失败,该产品已存在");
        }
        $add = M('pro_price')->add($post);
        if ($add) {
            $this->success("添加成功",U('ProPrice/property_list'));
        } else {
            $this->error("添加失败");
        }
    }

    /*
   * 修改分类属性
   */
    public function edit_property()
    {
        $post = I('post.');

        $add = M('pro_price')->where(array('id'=>I('id')))->save($post);
        if ($add) {
            $this->success("修改成功",U('ProPrice/property_list'));
        } else {
            $this->error("修改失败");
        }
    }





    public function product()
    {
        $pinlei = I('post.pinlei');
        $product = M('pro_price')->field("product")->where("pro_pinlei='{$pinlei}'")->select();
        foreach ($product as $v) {
            $v = implode(",", $v);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        rsort($temp);
        for ($i = 0; $i < count($temp); $i++) {
            $abc[$i]['product'] = $temp[$i];
        }
        foreach ($abc as $e) {
            $test[] = $e['product'];
        }

        json_encode($test);

        $this->ajaxReturn_e($test);

    }

    public function show_property()
    {
        $pro_pinlei = I('post.pinlei');
        $product = I('post.product');
        $map['pro_pinlei'] = $pro_pinlei;
        $map['product'] = $product;
        $property = M('pro_price')->where($map)->find();
        json_encode($property);
        $this->ajaxReturn_e($property);

    }

    public function add_price()
    {
        $pro_pinlei = I('post.pro_pinlei');
        $product = I('post.product');
        $property_one = I('post.property_one');
        $property_two = I('post.property_two');
        $property_three = I('post.property_four');
        $property_four = I('post.property_three');
        $pro_price = I('post.pro_price');


        if (!empty($property_three) && !empty($property_one) && !empty($property_two) && !empty($property_four)) {
            $property = $property_one . "," . $property_two . "," . $property_three . "," . $property_four;
        }
        if (!empty($property_three) && !empty($property_one) && !empty($property_two) && empty($property_four)) {
            $property = $property_one . "," . $property_two . "," . $property_three;
        }
        if (!empty($property_two) && !empty($property_one) && empty($property_three) && empty($property_four)) {
            $property = $property_one . "," . $property_two;
        }
        if (!empty($property_one) && empty($property_two) && empty($property_three) && empty($property_four)) {
            $property = $property_one;
        }
        $add['pro_price_wai'] = I('post.pro_price_wai');
        $add['service_project'] = I('post.service_project');
        $add['service_content'] = I('post.service_content');
        $add['order_type'] = I('post.order_type');
        $add['pro_price'] = $pro_price;
        $add['pro_pinlei'] = $pro_pinlei;
        $add['product'] = $product;
        $add['create_time'] = time();
        $add['pro_property'] = $property;

        $res = M('pro_price_detail')->add($add);
        $id = D('pro_price_detail')->order('create_time desc')->limit('id')->find();
        $a = explode(',', I('post.service_content'));
        for ($i = 0; $i < count($a); $i++) {
            $map_ser['pid'] = $id['id'];
            $map_ser['price'] = $pro_price;
            $map_ser['price_wai'] = I('post.pro_price_wai');
            $map_ser['service'] = $a[$i];
            D('pro_price_service')->add($map_ser);
        }
        if ($res) {
            $this->ajaxReturn_e(1);
        } else {
            $this->ajaxReturn_e(2);
        }
    }


    public function edit_price_list()
    {
        $pinlei = M('pro_price')->field('pro_pinlei')->select();
        foreach ($pinlei as $v) {
            $v = implode(",", $v);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        rsort($temp);
        for ($i = 0; $i < count($temp); $i++) {
            $abc[$i]['pro_pinlei'] = $temp[$i];
        }
        $map['id'] = I('id');

        $list = D('pro_price_detail')->where($map)->find();
        //添加价格
        $pro_price = D('pro_price')->where(array('pro_pinlei' => $list['pro_pinlei'], 'product' => $list['product']))->field('property_name_one,property_name_two,property_name_three,property_name_four')->find();
        $shuxin = explode(',', $list['pro_property']);
        $name['one'] = empty($pro_price['property_name_one']) ? "无" : $pro_price['property_name_one'];
        $name['two'] = empty($pro_price['property_name_two']) ? "无" : $pro_price['property_name_two'];
        $name['thr'] = empty($pro_price['property_name_four']) ? "无" : $pro_price['property_name_four'];
        $name['fou'] = empty($pro_price['property_name_three']) ? "无" : $pro_price['property_name_three'];

        $name['pro_one'] = empty($shuxin['0']) ? "<option value=\"0\">无</option>" : "<option value='" . $shuxin['0'] . "'>" . $shuxin['0'] . "</option>";
        $name['pro_two'] = empty($shuxin['1']) ? "<option value=\"0\">无</option>" : "<option value='" . $shuxin['1'] . "'>" . $shuxin['1'] . "</option>";
        $name['pro_thr'] = empty($shuxin['2']) ? "<option value=\"0\">无</option>" : "<option value='" . $shuxin['2'] . "'>" . $shuxin['2'] . "</option>";
        $name['pro_fou'] = empty($shuxin['3']) ? "<option value=\"0\">无</option>" : "<option value='" . $shuxin['3'] . "'>" . $shuxin['3'] . "</option>";
        $order_type = "<option value='" . $list['service_project'] . "'>" . $list['service_project'] . "</option>";
        $this->assign('order_type', $order_type);
        $this->assign('name', $name);
        $this->assign('pro_pinlei', $abc);
        $this->assign('list', $list);
        $this->display('edit_price_list');
    }

    public function edit_price()
    {
        $pro_pinlei = I('post.pro_pinlei');
        $product = I('post.product');
        $property_one = I('post.property_one');
        $property_two = I('post.property_two');
        $property_three = I('post.property_four');
        $property_four = I('post.property_three');
        $pro_price = I('post.pro_price');


        if (!empty($property_three) && !empty($property_one) && !empty($property_two) && !empty($property_four)) {
            $property = $property_one . "," . $property_two . "," . $property_three . "," . $property_four;
        }
        if (!empty($property_three) && !empty($property_one) && !empty($property_two) && empty($property_four)) {
            $property = $property_one . "," . $property_two . "," . $property_three;
        }
        if (!empty($property_two) && !empty($property_one) && empty($property_three) && empty($property_four)) {
            $property = $property_one . "," . $property_two;
        }
        if (!empty($property_one) && empty($property_two) && empty($property_three) && empty($property_four)) {
            $property = $property_one;
        }
        $add['pro_price_wai'] = I('post.pro_price_wai');
        $add['service_project'] = I('post.service_project');
        $add['service_content'] = I('post.service_content');
        $add['order_type'] = I('post.order_type');
        $add['pro_price'] = $pro_price;
        $add['pro_pinlei'] = $pro_pinlei;
        $add['product'] = $product;
        $add['create_time'] = time();
        $add['pro_property'] = $property;
        $map_id['id'] = I('id');
        $res = M('pro_price_detail')->where($map_id)->save($add);
        $map['pid'] = I('id');
        D('pro_price_service')->where($map)->delete();
        $a = explode(',', I('post.service_content'));
        for ($i = 0; $i < count($a); $i++) {
            $map_ser['pid'] = I('id');
            $map_ser['price'] = $pro_price;
            $map_ser['price_wai'] = I('post.pro_price_wai');
            $map_ser['service'] = $a[$i];
            D('pro_price_service')->add($map_ser);
        }
        if ($res) {
            $this->ajaxReturn(3);
        }
    }

    public function parts_list()
    {
        $pinlei = M('pro_price')->field('pro_pinlei')->select();
        foreach ($pinlei as $v) {
            $v = implode(",", $v);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        rsort($temp);
        for ($i = 0; $i < count($temp); $i++) {
            $abc[$i]['pro_pinlei'] = $temp[$i];
        }
        $this->assign('pro_pinlei', $abc);
        $this->display('add_parts');

    }

    public function add_parts()
    {

        $add['parts_name'] = I('post.parts_name');
        $add['parts_price'] = I('post.parts_price');
        $add['parts_pinlei'] = I('post.pro_pinlei');
        $add['parts_product'] = I('post.product');
        $add['create_time'] = time();
        $property_one = I('post.property_one');
        $property_two = I('post.property_two');
        $property_three = I('post.property_four');
        $property_four = I('post.property_three');
        if (!empty($property_three) && !empty($property_one) && !empty($property_two) && !empty($property_four)) {
            $property = $property_one . "," . $property_two . "," . $property_three . "," . $property_four;
        }
        if (!empty($property_three) && !empty($property_one) && !empty($property_two) && empty($property_four)) {
            $property = $property_one . "," . $property_two . "," . $property_three;
        }
        if (!empty($property_two) && !empty($property_one) && empty($property_three) && empty($property_four)) {
            $property = $property_one . "," . $property_two;
        }
        if (!empty($property_one) && empty($property_two) && empty($property_three) && empty($property_four)) {
            $property = $property_one;
        }
        $add['pro_property'] = $property;
        if (I('id')) {
            $map['id'] = I('id');
            $result = D('pro_parts_price')->where($map)->save($add);
        } else {
            $result = D('pro_parts_price')->add($add);
        }

        if ($result) {
            $this->ajaxReturn(1);
        }
    }

    /**
     * 详情页
     */
    public function detail()
    {
        $id = I('get.id');
        $map['id'] = $id;
        $pro_price = M('pro_price_detail')->where($map)->find();
        $this->assign('pro_price', $pro_price);
        $this->display();

    }

    /**
     * 品类列表
     */
    public function add_pinlei_list()
    {
        $this->display('add_pinlei');
    }

    public function add_pinlei()
    {
        $pinlei = D('ProPinlei');
        if ($pinlei->create()) {
            if (!I('id')) {
                $result = $pinlei->add();
            } else {
                $map['id'] = I('id');
                $result = $pinlei->where($map)->save();
            }
            if ($result) {
                $this->success('添加成功');
            } else {
                $this->success('添加失败');
            }
        } else {
            exit($pinlei->getError());
        }
    }

    public function pinlei_list()
    {
        $pinlei = D('ProPinlei')->order('create_time')->select();
        $this->assign('pinlei', $pinlei);
        $this->display('pinlei_list');
    }

    public function pinlei_delete()
    {
        $map['id'] = I('id');
        $result = D('ProPinlei')->where($map)->delete();
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function pinlei_edit()
    {
        $map['id'] = I('id');
        $result = D('ProPinlei')->where($map)->find();
        $this->assign('list', $result);
        $this->display('pinlei_edit');
    }

    public function parts_index()
    {
        $count = D('pro_parts_price')->count();
        $page = $this->page($count, 20);
        $list = M('pro_parts_price')->order('create_time desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $show = $page->show('Admin');
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('parts_index');
    }

    public function del_parts()
    {
        $map['id'] = I('id');
        $result = D('pro_parts_price')->where($map)->delete();
        if ($result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function edit_parts_list()
    {
        $map['id'] = I('id');
        $pinlei = M('pro_price')->field('pro_pinlei')->select();
        foreach ($pinlei as $v) {
            $v = implode(",", $v);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        rsort($temp);
        for ($i = 0; $i < count($temp); $i++) {
            $abc[$i]['pro_pinlei'] = $temp[$i];
        }
        $this->assign('pro_pinlei', $abc);
        $result = D('pro_parts_price')->where($map)->find();
        $this->assign('list', $result);
        $this->display('edit_parts');
    }

}