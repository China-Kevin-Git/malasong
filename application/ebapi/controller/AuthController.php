<?php
namespace app\ebapi\controller;


class AuthController extends Basic
{
    protected $uid = 0;

    protected $userInfo = [];

    protected $action = [
        "index",
        "matchCount",
        "queryMatch",
        "queryFollow",
        "article",
        "article",
        "allMacth",
        "macthCatgory",
        "details",
        "detailsContent",
        "month",
        "search",
        "orderPrice",

    ];

    protected function _initialize()
    {

        parent::_initialize();
        if(in_array(request()->action(),$this->action)==false){
            //验证TOken并获取user信息
            $this->userInfo=$this->checkTokenGetUserInfo();
            $this->uid=isset($this->userInfo['uid']) ? $this->userInfo['uid'] : 0;
        }

    }
}