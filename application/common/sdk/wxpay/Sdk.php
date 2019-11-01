<?php

namespace app\common\sdk\wxpay;


use app\common\sdk\wxpay\request\DepositRefundRequest;
use app\common\sdk\wxpay\request\Request;
use app\common\sdk\wxpay\response\Response;
use app\common\sdk\wxpay\response\ResponseSign;

/**
 * 微信支付接口
 */
class Sdk
{


    /**
     * 企业打款
     * @param $data
     * @param $config
     * @param int $timeOut
     * @return array
     */
    public static function transfers($data = [],$config = [],$timeOut = 20){
        $ip = \think\Request::instance();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $request = new DepositRefundRequest($config);
        $request->setMchAppid($data['mch_appid']??'wx73f6fda2165a0899');//申请商户号的appid或商户号绑定的appid
//        $request->setDeviceInfo($data['device_info']);//微信支付分配的终端设备号
        $request->setMchId($data['mchid']??'1491810082');//微信支付分配的商户号
        $request->setNonceStr(self::getNonceStr());//随机字符串
        $request->setSubAppid($data['partner_trade_no']??self::getNonceStr());//商户订单号，需保持唯一性(只能是字母或者数字，不能包含有其他字符)
        $request->setOpenid($data['openid']);//商户appid下，某用户的openid
        $request->setCheckName($data['check_name']??"NO_CHECK");//NO_CHECK：不校验真实姓名,FORCE_CHECK：强校验真实姓名
        $request->setUserName($data['user_name']??"某**");//收款用户真实姓名。如果check_name设置为FORCE_CHECK，则必填用户真实姓名
        $request->setAmount($data['amount']??"10");//企业付款金额，单位为分
        $request->setDesc($data['desc']??"理赔");//企业付款备注，必填。注意：备注中的敏感词会被转成字符*
        $request->setIp($data['spbill_create_ip']??$ip->ip());//该IP同在商户平台设置的IP白名单中的IP没有关联，该IP可传用户端或者服务端的IP

        $request->SetSign();//签名
        $xml = $request->ToXml();

        $response = self::postXmlCurl($xml, $url, $timeOut,true);
        $result = WxPayResults::Init($response, $request->config);

        return static::response($request, $result);
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
     * 以post方式提交xml到对应的接口url
     *
     * @param      $xml
     * @param      $url
     * @param int  $second
     *
     * @return mixed
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $second = 30,$useCret = false,$config = null)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//严格校验
        if ($useCret) {
            $file_path = dirname(__FILE__);
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $file_path.'/tenant/apiclient_cert.pem');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $file_path.'/tenant/apiclient_key.pem');
        }
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
            //网络请求失败 记录下超时日志，方便后期维护查看
            $msg = "curl出错 [{$url}]，错误码:{$error}，错误信息：{$error_info}";

            //CURL 错误 统一码 408
            throw new \Exception("curl请求错误", 408);
//            throw new WxPayException("curl出错，错误码:{$error}，错误信息：{$error_info}");
        }
    }
    
    /**
     * 接口返回结果处理
     *
     * @param $request
     * @param $response
     *
     * @return array
     */
    private static function response($request, $response)
    {
        $response_handler = new Response($request, $response);
        
        return $response_handler->handle();
    }
    
    /**
     * 回调验签
     */
    public static function verifySign($xml, $config)
    {
        $verify = false;
        $error_msg = '';
        try {
            $verify = ResponseSign::Init($xml, $config);
        } catch (\Exception $exception) {
            $error_msg = $exception->getMessage();
        }
        return ['verify' => $verify, 'msg' => $error_msg];
    }

}