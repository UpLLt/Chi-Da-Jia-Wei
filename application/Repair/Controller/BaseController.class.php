<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 10:01
 */
namespace Repair\Controller;
use Think\Controller;
class BaseController extends Controller {
    const SYSTEM_BUSY = 100; //系统繁忙，请求超时
    const REQUEST_SUCCESS = 200; //请求成功
    const FATAL_ERROR = 210; //存在逻辑错误(mark:描述问题)
    const TOKEN_ERROR = 220; //token无效
    const MISS_PARAM = 300; //缺少参数（mark:描述问题）
    const REQUEST_NO_POWER = 403; //权限不足
    const INVALID_INTERFACE = 404; //无效接口
    const SERVER_INTERNAL_ERROR = 404; //服务器内部错误

    /**
     * 返回token
     * @return string
     */
    public function createtoken()
    {
        return md5(time() . "ccpbuild" . rand(10, 99));
    }
    /**
     * 拼接url
     * @param $param
     * @return string
     */
    public function geturl($param, $root = true)
    {
        $url       = '';
        $http_host = "http://" . $_SERVER['HTTP_HOST'];
        if ($root) $http_host .= __ROOT__;
        if (is_string($param))
            return $http_host . $param;
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                if ($k != count($param - 1))
                    $url .= $v . '/';
                else
                    $url .= $v;
            }
            return $http_host . $url;
        }
    }

    /**
     * 检查token
     * @param $user_id
     * @param $token
     * @return bool
     */
    public function checktoken($user_id, $token)
    {
        $result = D('token')
            ->field('id,token,token_end_time')
            ->find($user_id);
        if (!$result) return false;
        $m_token = $result['token'];
        if ($m_token != $token) return false;
        if (time() > $result['token_end_time']) return false;
        return true;
    }
    /**
     * 返回成功的信息
     * @param array $data
     * @param string $token
     * @return string
     */
    public function returnApiSuccess($data = array(), $token = "")
    {
        $result['code'] = self::REQUEST_SUCCESS;
        if ($token)
            $result['token'] = $token;
        if (count($data) > 0)
            $result['data'] = $data;
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 返回错误信息
     * @param $code
     * @param string $errormsg
     * @param bool $urlencode
     * @return string
     */
    public function returnApiError($code, $errormsg = "", $urlencode = false)
    {
        $result['code'] = $code;
        if (!empty($errormsg)) {
            $result['mark'] = $urlencode ? urlencode($errormsg) : $errormsg;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    /**
     * 检查参数
     * @param $param
     * @param int $backname
     */
    public function checkparam($param, $backname = self::MISS_PARAM)
    {
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                if (empty($v)) exit($this->returnApiError($backname));
            }
        }
    }

}


