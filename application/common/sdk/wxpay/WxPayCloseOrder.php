<?php

namespace app\common\sdk\wxpay;

/**
 * 订单关闭
 */
class WxPayCloseOrder extends WxPayDataBase
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
    
    /**
     * 获取微信分配的公众账号ID
     *
     * @return string
     */
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    
    /**
     * 判断微信分配的公众账号ID是否设置
     *
     * @return bool
     */
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
     * 设置订单号
     *
     * @param $value
     */
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