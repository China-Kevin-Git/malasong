<?php

namespace app\common\sdk\wxpay;

/**
 * 提交JSAPI
 */
class WxPayJsApiPay extends WxPayDataBase
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     **/
    public function SetAppid($value)
    {
        $this->values['appId'] = $value;
    }
    
    public function GetAppid()
    {
        return $this->values['appId'];
    }
    
    public function IsAppidSet()
    {
        return array_key_exists('appId', $this->values);
    }
    
    /**
     * 设置支付时间戳
     *
     * @param string $value
     **/
    public function SetTimeStamp($value)
    {
        $this->values['timeStamp'] = $value;
    }
    
    public function GetTimeStamp()
    {
        return $this->values['timeStamp'];
    }
    
    public function IsTimeStampSet()
    {
        return array_key_exists('timeStamp', $this->values);
    }
    
    /**
     * 随机字符串
     *
     * @param string $value
     **/
    public function SetNonceStr($value)
    {
        $this->values['nonceStr'] = $value;
    }
    
    public function GetReturn_code()
    {
        return $this->values['nonceStr'];
    }
    
    public function IsReturn_codeSet()
    {
        return array_key_exists('nonceStr', $this->values);
    }
    
    /**
     * 设置订单详情扩展字符串
     *
     * @param string $value
     **/
    public function SetPackage($value)
    {
        $this->values['package'] = $value;
    }
    
    public function GetPackage()
    {
        return $this->values['package'];
    }
    
    public function IsPackageSet()
    {
        return array_key_exists('package', $this->values);
    }
    
    /**
     * 设置签名方式
     *
     * @param string $value
     **/
    public function SetSignType($value)
    {
        $this->values['signType'] = $value;
    }
    
    public function GetSignType()
    {
        return $this->values['signType'];
    }
    
    public function IsSignTypeSet()
    {
        return array_key_exists('signType', $this->values);
    }
    
    /**
     * 设置签名方式
     *
     * @param string $value
     **/
    public function SetPaySign($value)
    {
        $this->values['paySign'] = $value;
    }
    
    public function GetPaySign()
    {
        return $this->values['paySign'];
    }
    
    public function IsPaySignSet()
    {
        return array_key_exists('paySign', $this->values);
    }
}