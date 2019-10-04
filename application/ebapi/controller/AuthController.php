<?php
namespace app\ebapi\controller;


class AuthController extends Basic
{
    protected $uid = 0;

    protected $userInfo = [];

    protected function _initialize()
    {
        parent::_initialize();
        //验证TOken并获取user信息
        $this->userInfo=$this->checkTokenGetUserInfo();
        $this->uid=isset($this->userInfo['uid']) ? $this->userInfo['uid'] : 0;
    }
    /**
     * 统一返回格式
     * @param array $data
     * @param string $code
     * @param string $msg
     * @return array
     */
    public static function asJson($data = [],$code = '000000',$msg = '')
    {
        return json_encode(['data' => $data,'code' => (string)$code,'msg' => $msg]);
    }

}