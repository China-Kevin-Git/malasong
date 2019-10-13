<?php
namespace app\ebapi\controller;

use app\ebapi\model\store\StoreProductRelation;
use app\ebapi\model\store\StoreProductReply;
use app\ebapi\model\store\StoreSeckill;
use app\core\util\GroupDataService;
use service\JsonService;
use service\UtilService;
use think\Db;


/**
 * 小程序秒杀api接口
 * Class SeckillApi
 * @package app\ebapi\controller
 *
 */
class MacthSeckil extends AuthController
{
    /**
     * 秒杀列表页
     * @return \think\response\Json
     */
    public function seckill_index(){
        $data["banner"]=[
            "http://chinb.org/system/images/20191012003902.png"
        ];
        $data["seckill"] = Db::name("match_seckill")->field("id,image,product_id,title,price,stop_time")->where("stop_time",">",time())->select();
        foreach ($data["seckill"] as $k=>$v){
            $data["seckill"][$k]["stop_time"] = date("Y-m-d",$v["stop_time"]);
        }
        return JsonService::successful($data);
    }


    /**
     * 秒杀详情页
     * @param Request $request
     * @return \think\response\Json
     */
    public function seckill_detail(){
        $data = UtilService::postMore(['id']);
        $seckill = Db::name("match_seckill")->field("id,product_id,image,title,price,stop_time")->where(["id"=>$data["id"]])->find();
        $macth = Db::name("match")->where(["id"=>$seckill["product_id"]])->find();
        $seckill["address"] = $macth["province"].$macth["city"].$macth["area"];
        $seckill["match_starat"] = date("Y-m-d",$macth["match_starat"]);
        $seckill["enroll_time"] = $macth["enroll_time"];
        $seckill["content"] = $macth["content"];

        return JsonService::successful($seckill);
    }

    /**
     * 生成秒杀订单
     */
    public function seckill_order()
    {
        $data = input("post.");
        $str = "match-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $seckill = Db::name("match_seckill")->field("id,product_id,image,title,price,stop_time")->where(["id"=>$data["id"]])->find();

        $add=[
            "uid"=>$this->uid,
            "match_id"=>$seckill["product_id"],
            "order_price"=>$seckill["price"],
            "match_order_sn"=>$str,
            "add_time"=>time(),
            "match_name"=>$seckill["title"],
            "type"=>1,
        ];
        Db::name("match_order")->insert($add);
    }

}