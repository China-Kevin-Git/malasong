<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/26
 */

namespace app\core\behavior;

use app\ebapi\model\store\StoreOrder as StoreOrderRoutineModel;
use app\ebapi\model\store\StoreOrder as StoreOrderWapModel; //待完善
use app\ebapi\model\user\UserRecharge;
use service\HookService;
use app\core\util\MiniProgramService;
use app\core\util\WechatService;
use think\Db;

//待完善
class PaymentBehavior
{

    /**
     * 下单成功之后
     * @param $order
     * @param $prepay_id
     */
    public static function wechatPaymentPrepare($order, $prepay_id)
    {

    }

    /**
     * 支付成功后
     * @param $notify
     * @return bool|mixed
     */
    public static function wechatPaySuccess($notify)
    {
        if(isset($notify->attach) && $notify->attach){
            return HookService::listen('wechat_pay_success_'.strtolower($notify->attach),$notify->out_trade_no,$notify,true,self::class);
        }
        return false;
    }

    /**
     * 商品订单支付成功后  微信公众号
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessProduct($orderId, $notify)
    {
        try{
            if(StoreOrderWapModel::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return StoreOrderWapModel::paySuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }


    /**
     * 商品订单支付成功后  小程序
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessProductr($orderId, $notify)
    {
        try{

            //处理业务逻辑
            if (stripos($orderId, 'match-') !== false){
                $match_order =   Db::name("match_order")->where(["match_order_sn"=>$orderId])->update(["is_pay"=>1,"status"=>1,"pay_time"=>time()]);
                $order = Db::name("match_order")->where(["match_order_sn"=>$orderId])->find();
                if($order["type"]==3){
                    $str = "pink-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                    $combination = Db::name("match_combination")->field("id,product_id,people,price,stop_time")->where(["product_id"=>$order["match_id"]])->find();
                    $array = [
                        "uid"=>$order["uid"],
                        "order_id"=>$str,
                        "total_price"=>$combination["price"],
                        "price"=>$combination["price"],
                        "people"=>$combination["people"],
                        "cid"=>$combination["id"],
                        "pid"=>$combination["product_id"],
                        "add_time"=>time(),
                        "stop_time"=>$combination["stop_time"],
                        "k_id"=>$order["k_id"],
                        "is_pay"=>1,
                        "pay_time"=>time(),
                    ];
                    Db::name("match_pink")->insert($array);
                    if(!empty($order["k_id"])){
                        $match_pink = Db::name("match_pink")->where(["k_id"=>$order["k_id"]])->find();
                        $count = Db::name("match_pink")->where(["k_id"=>$order["k_id"]])->count();
                        if($match_pink["people"] == $count+1){
                            Db::name("match_pink")->where(["k_id"=>$order["k_id"]])->update(["status"=>2]);
                        }

                    }

                }
                if($order["type"]==2){
                    Db::name("match_bargain_user")->where('bargain_id',$order["k_id"])->where('uid',$order["uid"])->update(["status"=>3]);
                }

                $run_arr = Db::name("run_arr")->where(["uid"=>$order["uid"]])->value("run_id");
                if(!empty($run_arr)){
                    $run = Db::name("run")->where("id",$run_arr)->find();
                    $price = round($order["order_price"]*$run["scale"]/100,2);

                    Db::name("user")->where("uid",$run['uid'])->setInc("now_money",$price);
                    Db::name("user_bill")->insert([
                        "uid"=>$run['uid'],
                        "link_id"=>$order['uid'],
                        "pm"=>1,
                        "title"=>"跑团人员购买赠送",
                        "category"=>"now_money",
                        "type"=>"extract",
                        "number"=>$price,
                        "add_time"=>time(),
                        "status"=>1,
                    ]);
                }
                if($match_order){
                    //阻止微信接口反复回调接口
                    $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    echo $str;
                    return true;
                }
            }

            if(StoreOrderRoutineModel::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return StoreOrderRoutineModel::paySuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }





    /**
     * 用户充值成功后
     * @param $orderId
     * @param $notify
     * @return bool
     */
    public static function wechatPaySuccessUserRecharge($orderId, $notify)
    {
        try{
            if(UserRecharge::be(['order_id'=>$orderId,'paid'=>1])) return true;
            return UserRecharge::rechargeSuccess($orderId);
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 使用余额支付订单时
     * @param $userInfo
     * @param $orderInfo
     */
    public static function yuePayProduct($userInfo, $orderInfo)
    {


    }


    /**
     * 微信支付订单退款
     * @param $orderNo
     * @param array $opt
     */
    public static function wechatPayOrderRefund($orderNo, array $opt)
    {
        WechatService::payOrderRefund($orderNo,$opt);
    }

    /**
     * 小程序支付订单退款
     * @param $orderNo
     * @param array $opt
     */
    public static function routinePayOrderRefund($orderNo, array $opt)
    {
        MiniProgramService::payOrderRefund($orderNo,$opt);
    }

    /**
     * 微信支付充值退款
     * @param $orderNo
     * @param array $opt
     */

    public static function userRechargeRefund($orderNo, array $opt)
    {
        WechatService::payOrderRefund($orderNo,$opt);
    }
}