<?php
namespace app\ebapi\controller;


use think\Db;

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
        "allmacth",
        "macthcatgory",
        "details",
        "detailscontent",
        "month",
        "search",
        "orderprice",
        "homeindex",
        "crfy",
        "get_combination_list",
        "get_article",
        "get_cid_article",
        "article_desc",
        "homeIndex",
        "home_index",

    ];

    protected function _initialize()
    {

        parent::_initialize();

        $prom_goods = Db::name('match_order')->where(["is_pay"=>0])->field('match_order_id,add_time')->limit(100)->select();
        foreach ($prom_goods as &$v){
            if($v['add_time']+1800<=time()){
                //取消
                Db::name('match_order')->where('match_order_id',$v['match_order_id'])->update(["is_pay"=>3]);
            }
        }


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