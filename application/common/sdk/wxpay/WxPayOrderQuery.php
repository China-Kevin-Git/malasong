<?php

namespace app\common\sdk\wxpay;

/**
 * 订单查询
 */
class WxPayOrderQuery extends WxPayDataBase
{
    /**
     * 设置公众账号ID
     *
     * @param $value
     */
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    
    /**
     * 获取公众账号ID
     *
     * @return string
     */
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    
    /**
     * 判断公众账号ID是否设置
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
    
    /**
     * 获取微信支付分配的商户号
     *
     * @return string
     */
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    
    /**
     * 判断微信支付分配的商户号是否存在
     *
     * @return bool
     */
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    
    /**
     * 设置微信的订单号
     *
     * @param $value
     */
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    
    /**
     * 获取微信的订单号
     *
     * @return string
     */
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }
    
    /**
     * 断微信的订单号是否设置
     *
     * @return bool
     */
    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
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
    
    /**
     * 获取订单号
     *
     * @return string
     */
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    
    /**
     * 判断订单号是否存在
     *
     * @return bool
     */
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
    
    /**
     * 获取随机字符串
     *
     * @return mixed
     */
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    
    /**
     * 判断是否设置了随机字符串
     *
     * @return bool
     */
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    
    /**
     * 设置子商户号
     *
     * @param $id
     */
    public function setSubMchId($id)
    {
        $this->values['sub_mch_id'] = $id;
    }
}