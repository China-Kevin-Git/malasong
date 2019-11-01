<?php

namespace app\common\sdk\wxpay;

/**
 * 接口访问类
 */
class WxPayApi
{
    /**
     * 统一下单
     *
     * @param WxPayUnifiedOrder $apiRequest
     * @param int               $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function unifiedOrder(WxPayUnifiedOrder $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if (!$apiRequest->IsOut_trade_noSet()) {
            throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
        } else if (!$apiRequest->IsBodySet()) {
            throw new WxPayException("缺少统一支付接口必填参数body！");
        } else if (!$apiRequest->IsTotal_feeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数total_fee！");
        } else if (!$apiRequest->IsTrade_typeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数trade_type！");
        }
        
        //关联参数
        if ($apiRequest->GetTrade_type() == "JSAPI" && !$apiRequest->IsOpenidSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if ($apiRequest->GetTrade_type() == "NATIVE" && !$apiRequest->IsProduct_idSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }
        
        //异步通知url未设置，则使用配置文件中的url
        if (!$apiRequest->IsNotify_urlSet()) {
            $apiRequest->SetNotify_url($apiRequest->config['notify_url']);//异步通知url
        }
        
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        //签名
        $apiRequest->SetSign();
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 查询订单
     *
     * @param WxPayOrderQuery $apiRequest
     * @param int             $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function orderQuery(WxPayOrderQuery $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if (!$apiRequest->IsOut_trade_noSet() && !$apiRequest->IsTransaction_idSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 关闭订单
     *
     * @param WxPayCloseOrder $apiRequest
     * @param int             $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function closeOrder(WxPayCloseOrder $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //检测必填参数
        if (!$apiRequest->IsOut_trade_noSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no必填！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 申请退款
     *
     * @param WxPayRefund $apiRequest
     * @param int         $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function refund(WxPayRefund $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //检测必填参数
        if (!$apiRequest->IsOut_trade_noSet() && !$apiRequest->IsTransaction_idSet()) {
            throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        } else if (!$apiRequest->IsOut_refund_noSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
        } else if (!$apiRequest->IsTotal_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
        } else if (!$apiRequest->IsRefund_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
        } else if (!$apiRequest->IsOp_user_idSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 查询退款
     *
     * @param WxPayRefundQuery $apiRequest
     * @param int              $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function refundQuery(WxPayRefundQuery $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        if (!$apiRequest->IsOut_refund_noSet() &&
            !$apiRequest->IsOut_trade_noSet() &&
            !$apiRequest->IsTransaction_idSet() &&
            !$apiRequest->IsRefund_idSet()) {
            throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 下载对账单
     *
     * @param WxPayDownloadBill $apiRequest
     * @param int               $timeOut
     *
     * @return string
     * @throws WxPayException
     */
    public static function downloadBill(WxPayDownloadBill $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        if (!$apiRequest->IsBill_dateSet()) {
            throw new WxPayException("对账单接口中，缺少必填参数bill_date！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $response = self::postXmlCurl($xml, $url, $timeOut);
        if (substr($response, 0, 5) == "<xml>") {
            return "";
        }
        return $response;
    }
    
    /**
     * 提交被扫支付API
     *
     * @param WxPayMicroPay $apiRequest
     * @param int           $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function micropay(WxPayMicroPay $apiRequest, $timeOut = 10)
    {
        $url = "https://api.mch.weixin.qq.com/pay/micropay";
        //检测必填参数
        if (!$apiRequest->IsBodySet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
        } else if (!$apiRequest->IsOut_trade_noSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
        } else if (!$apiRequest->IsTotal_feeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
        } else if (!$apiRequest->IsAuth_codeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 撤销订单API接口
     *
     * @param WxPayReverse $apiRequest
     * @param int          $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function reverse(WxPayReverse $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
        //检测必填参数
        if (!$apiRequest->IsOut_trade_noSet() && !$apiRequest->IsTransaction_idSet()) {
            throw new WxPayException("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 测速上报
     *
     * @param WxPayReport $apiRequest
     * @param int         $timeOut
     *
     * @return mixed
     * @throws WxPayException
     */
    public static function report(WxPayReport $apiRequest, $timeOut = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if (!$apiRequest->IsInterface_urlSet()) {
            throw new WxPayException("接口URL，缺少必填参数interface_url！");
        }
        if (!$apiRequest->IsReturn_codeSet()) {
            throw new WxPayException("返回状态码，缺少必填参数return_code！");
        }
        if (!$apiRequest->IsResult_codeSet()) {
            throw new WxPayException("业务结果，缺少必填参数result_code！");
        }
        if (!$apiRequest->IsUser_ipSet()) {
            throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
        }
        if (!$apiRequest->IsExecute_time_Set()) {
            throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetUser_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $apiRequest->SetTime(date("YmdHis"));//商户上报时间
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $response = self::postXmlCurl($xml, $url, $timeOut);
        return $response;
    }
    
    /**
     * 生成二维码规则,模式一生成支付二维码
     *
     * @param WxPayBizPayUrl $apiRequest
     *
     * @return array
     * @throws WxPayException
     */
    public static function bizpayurl(WxPayBizPayUrl $apiRequest)
    {
        if (!$apiRequest->IsProduct_idSet()) {
            throw new WxPayException("生成二维码，缺少必填参数product_id！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetTime_stamp(time());//时间戳
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        
        return $apiRequest->GetValues();
    }
    
    /**
     * 转换短链接
     *
     * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接
     *
     * @param WxPayShortUrl $apiRequest
     * @param int           $timeOut
     *
     * @return array
     * @throws WxPayException
     */
    public static function shorturl(WxPayShortUrl $apiRequest, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/tools/shorturl";
        //检测必填参数
        if (!$apiRequest->IsLong_urlSet()) {
            throw new WxPayException("需要转换的URL，签名用原串，传输需URL encode！");
        }
        $apiRequest->SetAppid($apiRequest->config['app_id']);//公众账号ID
        $apiRequest->SetMch_id($apiRequest->config['mch_id']);//商户号
        $apiRequest->SetNonce_str(self::getNonceStr());//随机字符串
        
        $apiRequest->SetSign();//签名
        $xml = $apiRequest->ToXml();
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, $timeOut);
        $result = WxPayResults::Init($response, $apiRequest->config);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        return $result;
    }
    
    /**
     * 支付结果通用通知
     *
     * @param          $callback
     * @param          $msg
     * @param array    $config 微信支付配置
     *
     * 直接回调函数使用方法: notify(you_function);
     * 回调类成员函数方法:notify(array($this, you_function));
     * $callback  原型为：function function_name($data){}
     *
     * @return bool|mixed
     */
    public static function notify($callback, &$msg, $config)
    {
        //获取通知的数据
        $xml = file_get_contents('php://input');
        //如果返回成功则验证签名
        try {
            $result = WxPayResults::Init($xml, $config);
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }
        
        return call_user_func($callback, $result);
    }
    
    /**
     * 产生随机字符串
     *
     * @param int $length
     *
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
    /**
     * 直接输出xml
     *
     * @param $xml
     */
    public static function replyNotify($xml)
    {
        echo $xml;
    }
    
    /**
     * 上报数据， 上报的时候将屏蔽所有异常流程
     *
     * @param $url
     * @param $startTimeStamp
     * @param $data
     */
    private static function reportCostTime($url, $startTimeStamp, $data)
    {
        //
    }
    
    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param      $xml
     * @param      $url
     * @param int  $second
     *
     * @return mixed
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            $error_info = curl_error($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码:{$error}，错误信息：{$error_info}");
        }
    }
    
    /**
     * 获取毫秒级别的时间戳
     *
     * @return array|string
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }
}