<?php
namespace app\ebapi\controller;


class AuthController extends Basic
{
    protected $uid = 0;

    protected $userInfo = [];

    protected $action = [
        "index",
        "matchcount",
        "querymatch",
        "queryfollow",
        "article",
        "allMacth",
        "macthcatgory",
        "details",
        "detailscontent",
        "month",
        "search",
        "orderprice",
        "homeindex",
        "crfy",
        "get_combination_list",
        "homeIndex",
        "get_article",
        "get_cid_article",
        "article_desc",

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

    /**
     * 统一返回格式
     * @param array $data
     * @param string $code
     * @param string $msg
     * @return array
     */
    public static function asJson($data = [],$code = 200,$msg = 'ok')
    {
        return json_encode(['data' => $data,'code' => $code,'msg' => $msg]);
    }
}