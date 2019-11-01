<?php

namespace app\common\sdk\wxpay;

/**
 * 下载对账单
 */
class WxPayDownloadBill extends WxPayDataBase
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
     * 设置微信支付分配的终端设备号，填写此字段，只下载该设备号的对账单
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
     * 设置下载对账单的日期，格式：20140603
     *
     * @param $value
     */
    public function SetBill_date($value)
    {
        $this->values['bill_date'] = $value;
    }
    
    public function GetBill_date()
    {
        return $this->values['bill_date'];
    }
    
    public function IsBill_dateSet()
    {
        return array_key_exists('bill_date', $this->values);
    }
    
    /**
     * 设置账单类型
     *
     * 默认ALL，返回当日所有订单信息
     * SUCCESS，返回当日成功支付的订单
     * REFUND，返回当日退款订单
     * RECHARGE_REFUND，返回当日充值退款订单
     *
     * @param $value
     */
    public function SetBill_type($value)
    {
        $this->values['bill_type'] = $value;
    }
    
    public function GetBill_type()
    {
        return $this->values['bill_type'];
    }
    
    public function IsBill_typeSet()
    {
        return array_key_exists('bill_type', $this->values);
    }
}