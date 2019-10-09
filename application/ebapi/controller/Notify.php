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
        $data =  Pay::notify();

        if($data){
            if($data['return_code'] == 'SUCCESS'){ //支付成功
                //处理业务逻辑
                if (stripos($data['out_trade_no'], 'match-') == false){
                    $this->matchPay($data['out_trade_no']);
                }else{
                    $this->orderPay($data['out_trade_no']);
                }
                //阻止微信接口反复回调接口
                $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                echo $str;
            }
        }


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

    /**
     * 报名支付成功后
     */
    public function orderPay($orderSn)
    {
        $match_order =   Db::name("store_order")->where(["unique"=>$orderSn])->update(["paid"=>1,"status"=>0,"pay_time"=>time(),"pay_type"=>"winxin"]);

        if($match_order){
            return true;
        }else{
            return false;
        }
    }

}


