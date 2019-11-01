<?php

namespace app\common\sdk\wxpay;

/**
 * 扫码支付模式一生成二维码参数
 */
class WxPayBizPayUrl extends WxPayDataBase
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     **/
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    
    function GetAppid()
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
     * @param string $value
     **/
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
    
    public function SetTime_stamp($value)
    {
        $this->values['time_stamp'] = $value;
    }
    
    public function GetTime_stamp()
    {
        return $this->values['time_stamp'];
    }
    
    public function IsTime_stampSet()
    {
        return array_key_exists('time_stamp', $this->values);
    }
    
    /**
     * 设置随机字符串
     *
     * @param string $value
     **/
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
    
    /**
     * 设置商品ID
     *
     * @param string $value
     **/
    public function SetProduct_id($value)
    {
        $this->values['product_id'] = $value;
    }
    
    public function GetProduct_id()
    {
        return $this->values['product_id'];
    }
    
    public function IsProduct_idSet()
    {
        return array_key_exists('product_id', $this->values);
    }
}