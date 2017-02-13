<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 14:01
 */

function array_unique_fb($array2D)
{
    foreach ($array2D as $v)
    {
        $v = join("^",$v);
        $temp[] = $v;
    }
    $temp = array_unique($temp);
    foreach ($temp as $k => $v)
    {
        $temp[$k] = explode("^",$v);
    }

    return $temp;
}
function rand_six(){
    $number = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    return $number;
}

/**
 * @return 工单完结码
 */
function rand_twelve(){
    $number = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    return $number;
}
/**
 * 计算创建时间到现在时间的时间差
 * @return $time数组
 */
function time_difference($time){
    $zero1  = time();
    $zero2  = $time;
    $c =  $zero1-$zero2;
    $days = intval($c/86400);
    $remain = $c%86400;
    $hours = intval($remain/3600);
    $remain = $remain%3600;
    $mins = intval($remain/60);
    $secs = $remain%60;
    $time_diff['day']   = $days;
    $time_diff['hours'] = $hours;
    $time_diff['mins']  = $mins;
    $time_diff['secs']  = $secs;
    return $time_diff;
}