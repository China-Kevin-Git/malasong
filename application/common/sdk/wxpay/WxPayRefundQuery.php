<?php

namespace app\common\sdk\wxpay;

/**
 * 退款查询
 */
class WxPayRefundQuery extends WxPayDataBase
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
     * 设置微信支付分配的终端设备号
     *
     * @param $value
     */
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
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
    
    /**
     * 设置微信订单号
     *
     * @param $value
     */
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
     * 设置商户退款单号
     *
     * @param $value
     */
    public function SetOut_refund_no($value)
    {
        $this->values['out_refund_no'] = $value;
    }
    
    public function GetOut_refund_no()
    {
        return $this->values['out_refund_no'];
    }
    
    public function IsOut_refund_noSet()
    {
        return array_key_exists('out_refund_no', $this->values);
    }
    
    /**
     * 设置微信退款单号
     *
     * @param $value
     */
    public function SetRefund_id($value)
    {
        $this->values['refund_id'] = $value;
    }
    
    public function GetRefund_id()
    {
        return $this->values['refund_id'];
    }
    
    public function IsRefund_idSet()
    {
        return array_key_exists('refund_id', $this->values);
    }
    
    /**
     * 设置子商户号
     *
     * @param $id
     */
    public function SetSubMchId($id)
    {
        $this->values['sub_mch_id'] = $id;
    }
}