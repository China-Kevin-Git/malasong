<?php

namespace app\common\sdk\wxpay;

/**
 * 短链转换:用于扫码原生支付模式一中的二维码链接转成短链接
 */
class WxPayShortUrl extends WxPayDataBase
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param $value
     */
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    
    /**
     * 设置微信支付分配的商户号
     *
     * @param $value
     */
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    
    /**
     * 设置需要转换的URL，签名用原串，传输需URL encode
     *
     * @param $value
     */
    public function SetLong_url($value)
    {
        $this->values['long_url'] = $value;
    }
    
    public function GetLong_url()
    {
        return $this->values['long_url'];
    }
    
    public function IsLong_urlSet()
    {
        return array_key_exists('long_url', $this->values);
    }
    
    /**
     * 设置随机字符串
     *
     * @param $value
     */
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
}