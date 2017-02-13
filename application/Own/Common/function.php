<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 10:15
 */

function rand_six(){
    $number = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    return $number;
}

function order_number(){
    $a = substr(date('Ymd',time()),-6);
    $number = D('order')->count();
    if(strlen($number) == 1 ){
        $order_number =  $a.'00000'.$number;
    }else if(strlen($number) == 2){
        $order_number =  $a.'0000'.$number;
    }else if(strlen($number) == 3){
        $order_number =  $a.'0000'.$number;
    }else if(strlen($number) == 4){
        $order_number =  $a.'00'.$number;
    }else if(strlen($number) == 5){
        $order_number =  $a.'0'.$number;
    }else {
        $order_number = $a.$number;
    }
    return $order_number;
}