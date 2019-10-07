<?php
namespace app\ebapi\controller;

use app\core\logic\Pay;
use think\Db;

/**
 * 支付回调
 * Class Notify
 * @package app\ebapi\controller
 */
//待完善
class Notify
{
    /**
     *   支付  异步回调
     */
    public function notify()
    {
        Pay::notify();

    }


    /**
     * 报名支付成功后
     */
    public function matchPay($orderSn)
    {
        $match_order =   Db::name("match_order")->where(["match_order_sn"=>$orderSn])->update(["is_pay"=>1,"status"=>1,"pay_time"=>time()]);

        if($match_order){
            return true;
        }else{
            return false;
        }
    }

}


