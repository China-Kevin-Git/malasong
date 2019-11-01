<?php

namespace app\common\sdk\wxpay\request;
//支付押金（人脸支付）
class DepositFacePayRequest extends Request
{
    use RequestTrait;

    public $config = [];
    public $api_method = 'facepay';
    
    public  function __construct(array $config)
    {  
        if(!$config){
            return false;
        }
        
        $this->config = $config;
    }

    /**
     * 是否押金人脸支付，Y-是,N-普通人脸支付
     */
    public function setDeposit($is_deposit){
        $this->values['deposit'] = $is_deposit;
        
        return $this;
    }

    /**
     * 设置加密方式
     */
    public function setSignType($type = 'HMAC-SHA256'){
        $this->values['sign_type'] = $type;
        
        return $this;
    }
    /**
     * 设置加密方式
     */
    public function getSignType($type = 'HMAC-SHA256'){
        $this->values['sign_type'] = $type;

        return $this;
    }

    /**
     * 设置商品描述
     */
    public function setBody(string $body){
        $this->values['body'] = $body;
        return $this;
    }

    /**
     * 设置支付金额
     */
    public function setTotalFee($amount)
    {
        $this->values['total_fee'] = $amount;
        return $this;
    }

    /**
     * 设置用户openid
     */
    public function setOpenId($openid){
        $this->values['openid'] = $openid;
        return $this;
    }

    /**
     * 设置face_code
     */
    public function setFaceCode($face_code){
        $this->values['face_code'] = $face_code;
        return $this;
    }

    /**
     * 设置调⽤微信⽀付API的机器IP
     */
    public function setSpbillCreateIp(string $ip){
        $this->values['spbill_create_ip'] = $ip;
        return $this;
    }

    /**
     * 设置符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
     * @param string $value
     **/
    public function setFeeType($value)
    {
        $this->values['fee_type'] = $value;
        return $this;
    }

    /**
     * 商品详情
     * @param string $value
     **/
    public function setDetail($value)
    {
        $this->values['detail'] = $value;
        return $this;
    }

    /**
     *附加数据
     * @param string $value
     **/
    public function setAttach($value)
    {
        $this->values['attach'] = $value;
        return $this;
    }
    /**
     *订单优惠标记
     * @param string $value
     **/
    public function setGoodsTag($value)
    {
        $this->values['goods_tag'] = $value;
        return $this;
    }
    /**
     *订单优惠标记
     * @param string $value
     **/
    public function setLimitPay($value)
    {
        $this->values['limit_pay'] = $value;
        return $this;
    }
    /**
     *交易起始时间
     * @param string $value
     **/
    public function setTimeStart($value)
    {
        $this->values['time_start'] = $value;
        return $this;
    }
    /**
     *交易结束时间
     * @param string $value
     **/
    public function setTimeExpire($value)
    {
        $this->values['time_expire'] = $value;
        return $this;
    }
    /**
     *设备号
     * @param string $value
     **/
    public function setDeviceInfo($value)
    {
        $this->values['device_info'] = $value;
        return $this;
    }

}