<?php

namespace app\common\sdk\wxpay;

class WxPayUnifiedOrder extends WxPayDataBase
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
    
    /**
     * 获取微信分配的公众账号ID的值
     *
     * @return string
     */
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    
    /**
     * 判断微信分配的公众账号ID是否存在
     *
     * @return bool
     **/
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    
    /**
     * 获取子商户号
     *
     * @return string
     */
    public function GetSub_mch_id()
    {
        return $this->values['sub_mch_id'];
    }
    
    /**
     * 设置子商户号
     *
     * @param $value
     */
    public function SetSub_mch_id($value)
    {
        $this->values['sub_mch_id'] = $value;
    }
    
    /**
     * 设置isv的商户号
     *
     * @param $value
     */
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    
    /**
     * 获取isv的商户号
     *
     * @return string
     */
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    
    /**
     * 判断isv商户号是否存在
     *
     * @return bool
     */
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    
    /**
     * 设置微信支付分配的终端设备号，商户自定义
     *
     * @param $value
     */
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    
    /**
     * 获取微信支付分配的终端设备号，商户自定义的值
     *
     * @return string
     */
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    
    /**
     * 判断终端设备号是否存在
     *
     * @return bool
     */
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    
    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
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
     * @return string
     */
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    
    /**
     * 判断随机字符串是否存在
     *
     * @return bool
     */
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    
    /**
     * 设置订单简要描述
     *
     * @param $value
     */
    public function SetBody($value)
    {
        $this->values['body'] = $value;
    }
    
    /**
     * 获取订单简要描述
     *
     * @return mixed
     */
    public function GetBody()
    {
        return $this->values['body'];
    }
    
    /**
     *  判断订单简要描述是否存在
     *
     * @return bool
     */
    public function IsBodySet()
    {
        return array_key_exists('body', $this->values);
    }
    
    /**
     * 设置商品详细描述，对于使用单品优惠的商户，改字段必须按照规范上传
     *
     * @param $value
     */
    public function SetDetail($value)
    {
        $this->values['detail'] = $value;
    }
    
    /**
     * 获取商品详细描述
     *
     * @return mixed
     */
    public function GetDetail()
    {
        return $this->values['detail'];
    }
    
    /**
     * 判断商品详细描述是否存在
     *
     * @return bool
     */
    public function IsDetailSet()
    {
        return array_key_exists('detail', $this->values);
    }
    
    /**
     * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *
     * @param $value
     */
    public function SetAttach($value)
    {
        $this->values['attach'] = $value;
    }
    
    /**
     * 获取附加数据
     *
     * @return string
     */
    public function GetAttach()
    {
        return $this->values['attach'];
    }
    
    /**
     * 判断附加数据是否存在
     *
     * @return bool
     */
    public function IsAttachSet()
    {
        return array_key_exists('attach', $this->values);
    }
    
    /**
     * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
     *
     * @param $value
     */
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    
    /**
     * 获取商户系统内部的订单号
     *
     * @return string
     */
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    
    /**
     * 判断商户系统内部的订单号是否存在
     *
     * @return bool
     */
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    
    /**
     * 设置货币类型
     *
     * 符合ISO 4217标准的三位字母代码，默认人民币：CNY
     *
     * @param $value
     */
    public function SetFee_type($value)
    {
        $this->values['fee_type'] = $value;
    }
    
    /**
     * 获取货币类型
     *
     * @return mixed
     */
    public function GetFee_type()
    {
        return $this->values['fee_type'];
    }
    
    /**
     * 判断货币类型是否设置
     *
     * @return bool
     */
    public function IsFee_typeSet()
    {
        return array_key_exists('fee_type', $this->values);
    }
    
    /**
     * 设置订单总金额
     *
     * 单位为分
     *
     * @param $value
     */
    public function SetTotal_fee($value)
    {
        $this->values['total_fee'] = $value;
    }
    
    /**
     * 获取订单总金额
     *
     * @return integer
     */
    public function GetTotal_fee()
    {
        return $this->values['total_fee'];
    }
    
    /**
     * 判断订单总金额是否设置
     *
     * @return bool
     */
    public function IsTotal_feeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    
    /**
     * 设置终端IP
     *
     * APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     *
     * @param $value
     */
    public function SetSpbill_create_ip($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }
    
    /**
     * 获取终端IP
     *
     * @return mixed
     */
    public function GetSpbill_create_ip()
    {
        return $this->values['spbill_create_ip'];
    }
    
    /**
     * 检查终端IP是否存在
     *
     * @return bool
     */
    public function IsSpbill_create_ipSet()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
    
    /**
     * 设置订单生成时间
     *
     * 如2009年12月25日9点10分10秒表示为20091225091010
     *
     * @param $value
     */
    public function SetTime_start($value)
    {
        $this->values['time_start'] = $value;
    }
    
    /**
     * 获取订单生成时间
     *
     * @return string
     */
    public function GetTime_start()
    {
        return $this->values['time_start'];
    }
    
    /**
     * 判断订单生成时间是否存在
     *
     * @return bool
     */
    public function IsTime_startSet()
    {
        return array_key_exists('time_start', $this->values);
    }
    
    /**
     * 设置订单失效时间
     *
     * 如2009年12月25日9点10分10秒表示为20091225091010
     *
     * @param $value
     */
    public function SetTime_expire($value)
    {
        $this->values['time_expire'] = $value;
    }
    
    /**
     * 获取订单失效时间
     *
     * @return string
     */
    public function GetTime_expire()
    {
        return $this->values['time_expire'];
    }
    
    /**
     * 判断订单失效时间是否存在
     *
     * @return bool
     */
    public function IsTime_expireSet()
    {
        return array_key_exists('time_expire', $this->values);
    }
    
    /**
     * 设置订单优惠标记，代金券或立减优惠功能的参数
     *
     * @param $value
     */
    public function SetGoods_tag($value)
    {
        $this->values['goods_tag'] = $value;
    }
    
    /**
     * 获取订单优惠标记
     *
     * @return string
     */
    public function GetGoods_tag()
    {
        return $this->values['goods_tag'];
    }
    
    /**
     * 判断订单优惠标记是否存在
     *
     * @return bool
     */
    public function IsGoods_tagSet()
    {
        return array_key_exists('goods_tag', $this->values);
    }
    
    /**
     * 设置接收微信支付异步通知回调地址
     *
     * @param $value
     */
    public function SetNotify_url($value)
    {
        $this->values['notify_url'] = $value;
    }
    
    /**
     * 获取接收微信支付异步通知回调地址
     *
     * @return string
     */
    public function GetNotify_url()
    {
        return $this->values['notify_url'];
    }
    
    /**
     * 判断接收微信支付异步通知回调地址是否存在
     *
     * @return bool
     */
    public function IsNotify_urlSet()
    {
        return array_key_exists('notify_url', $this->values);
    }
    
    /**
     * 设置交易类型
     *
     * JSAPI 公众号支付 NATIVE 扫码支付 APP APP支付
     *
     * @param $value
     */
    public function SetTrade_type($value)
    {
        $this->values['trade_type'] = $value;
    }
    
    /**
     * 获取交易类型
     *
     * @return string
     */
    public function GetTrade_type()
    {
        return $this->values['trade_type'];
    }
    
    /**
     * 判断交易类型是否设置
     *
     * @return bool
     */
    public function IsTrade_typeSet()
    {
        return array_key_exists('trade_type', $this->values);
    }
    
    /**
     * 设置商品ID
     *
     * trade_type=NATIVE时，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
     *
     * @param $value
     */
    public function SetProduct_id($value)
    {
        $this->values['product_id'] = $value;
    }
    
    /**
     * 获取商品id
     *
     * @return string
     */
    public function GetProduct_id()
    {
        return $this->values['product_id'];
    }
    
    /**
     * 判断商品id是否设置
     *
     * @return bool
     */
    public function IsProduct_idSet()
    {
        return array_key_exists('product_id', $this->values);
    }
    
    /**
     * 设置用户标识
     *
     * trade_type=JSAPI，此参数必传，用户在主商户appid下的唯一标识。
     * openid和sub_openid可以选传其中之一，如果选择传sub_openid,则必须传sub_appid。
     *
     * @param $value
     */
    public function SetOpenid($value)
    {
        $this->values['openid'] = $value;
    }
    
    /**
     * 获取用户标识
     *
     * @return mixed
     */
    public function GetOpenid()
    {
        return $this->values['openid'];
    }
    
    /**
     * 判断用户标识是否设置
     *
     * @return bool
     */
    public function IsOpenidSet()
    {
        return array_key_exists('openid', $this->values);
    }
}