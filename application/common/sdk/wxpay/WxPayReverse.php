<?php


namespace app\common\sdk\wxpay;


class WxPayReverse extends WxPayDataBase
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
    
    /**
     * 设置微信的订单号，优先使用
     *
     * @param string $value
     **/
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }
    
    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }
    
    
    /**
     * 设置商户系统内部的订单号,transaction_id、out_trade_no二选一，如果同时存在优先级：transaction_id> out_trade_no
     *
     * @param string $value
     **/
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    
    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
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
}