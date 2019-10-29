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
        $data["icon"] = Db::name("store_category")->field("id,cate_name,pic")->order("sort")->select();

        $data["combination"] = Db::name("store_combination")->field("image,product_id as id,price,title")->where(['is_del'=>0,"is_show"=>1])->where("stop_time",'>',time())->order("sort")->select();
        $data["seckill"] = Db::name("store_seckill")->field("image,stop_time,product_id as id,price,title,ot_price")->where(['is_del'=>0,"is_show"=>1])->where("stop_time",'>',time())->order("sort")->select();
        $data["bargain"] = Db::name("store_bargain")->field("image,product_id as id, '0元购' as `price_name`")->where(['is_del'=>0])->where("stop_time",'>',time())->order("sort")->select();

        return self::asJson($data);
    }
}