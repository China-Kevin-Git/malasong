<?php

namespace app\common\sdk\wxpay;

/**
 * 提交被扫输入
 */
class WxPayMicroPay extends WxPayDataBase
{
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
    
    public function SetBody($value)
    {
        $this->values['body'] = $value;
    }
    
    public function GetBody()
    {
        return $this->values['body'];
    }
    
    public function IsBodySet()
    {
        return array_key_exists('body', $this->values);
    }
    
    public function SetDetail($value)
    {
        $this->values['detail'] = $value;
    }
    
    public function GetDetail()
    {
        return $this->values['detail'];
    }
    
    public function IsDetailSet()
    {
        return array_key_exists('detail', $this->values);
    }
    
    public function SetAttach($value)
    {
        $this->values['attach'] = $value;
    }
    
    public function GetAttach()
    {
        return $this->values['attach'];
    }
    
    public function IsAttachSet()
    {
        return array_key_exists('attach', $this->values);
    }
    
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
    
    public function SetTotal_fee($value)
    {
        $this->values['total_fee'] = $value;
    }
    
    public function GetTotal_fee()
    {
        return $this->values['total_fee'];
    }
    
    public function IsTotal_feeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    
    public function SetFee_type($value)
    {
        $this->values['fee_type'] = $value;
    }
    
    public function GetFee_type()
    {
        return $this->values['fee_type'];
    }
    
    public function IsFee_typeSet()
    {
        return array_key_exists('fee_type', $this->values);
    }
    
    public function SetSpbill_create_ip($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }
    
    public function GetSpbill_create_ip()
    {
        return $this->values['spbill_create_ip'];
    }
    
    public function IsSpbill_create_ipSet()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
    
    public function SetTime_start($value)
    {
        $this->values['time_start'] = $value;
    }
    
    public function GetTime_start()
    {
        return $this->values['time_start'];
    }
    
    public function IsTime_startSet()
    {
        return array_key_exists('time_start', $this->values);
    }
    
    public function SetTime_expire($value)
    {
        $this->values['time_expire'] = $value;
    }
    
    public function GetTime_expire()
    {
        return $this->values['time_expire'];
    }
    
    public function IsTime_expireSet()
    {
        return array_key_exists('time_expire', $this->values);
    }
    
    public function SetGoods_tag($value)
    {
        $this->values['goods_tag'] = $value;
    }
    
    public function GetGoods_tag()
    {
        return $this->values['goods_tag'];
    }
    
    public function IsGoods_tagSet()
    {
        return array_key_exists('goods_tag', $this->values);
    }
    
    /**
     * 设置扫码支付授权码
     *
     * @param $value
     */
    public function SetAuth_code($value)
    {
        $this->values['auth_code'] = $value;
    }
    
    public function GetAuth_code()
    {
        return $this->values['auth_code'];
    }
    
    public function IsAuth_codeSet()
    {
        return array_key_exists('auth_code', $this->values);
    }
}