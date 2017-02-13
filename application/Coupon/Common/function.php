<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10
 * Time: 13:51
 */
function generateCode( $nums , $exist_array= '' ,$code_length = 12 ,$prefix = '') {

    $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz";
    $promotion_codes = array();//这个数组用来接收生成的优惠码

    for($j = 0 ; $j < $nums; $j++) {

        $code = '';

        for ($i = 0; $i < $code_length; $i++) {

            $code .= $characters[mt_rand(0, strlen($characters)-1)];

        }


        if( !in_array($code,$promotion_codes) ) {

            if( is_array($exist_array) ) {

                if( !in_array($code,$exist_array) ) {//排除已经使用的优惠码

                    $promotion_codes[$j] = $prefix.$code; //将生成的新优惠码赋值给promotion_codes数组
                    $coupon = $prefix.$code;
                } else {

                    $j--;

                }

            } else {

                $promotion_codes[$j] = $prefix.$code;//将优惠码赋值给数组
                $coupon = $prefix.$code;;
            }

        } else {
            $j--;
        }
    }

    return $coupon;
}
