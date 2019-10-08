<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/8
 * Time: 11:50
 */

namespace app\ebapi\controller;


use think\Db;

class Home extends AuthController
{
    /**
     * 商城首页
     */
    public function homeIndex()
    {
        $system = Db::name("system_group_data")->where(['gid'=>34])->column("value");
        $data = [] ;
        foreach ($system as $k=>$v){
            $v = json_decode($v,true);
            $data["banner"][$k] = $v["pic"]["value"];
        }
        $system = Db::name("system_group_data")->where(['gid'=>35])->column("value");
        foreach ($system as $k=>$v){
            $v = json_decode($v,true);
            $data["icon"][$k]['name'] = $v["name"]["value"];
            $data["icon"][$k]['icon'] = $v["icon"]["value"];

        }
        $data["combination"] = Db::name("store_combination")->field("id,image,product_id,price,title")->where(['is_del'=>0])->order("sort")->select();
        $data["seckill"] = Db::name("store_seckill")->field("id,image,product_id,price,title,ot_price")->where("stop_time",'>',time())->where(['is_del'=>0])->order("sort")->select();
        $data["bargain"] = Db::name("store_bargain")->field("id,image,product_id, '0元购' as `price_name`")->where("stop_time",'>',time())->where(['is_del'=>0])->order("sort")->select();

        return self::asJson($data);
    }
}