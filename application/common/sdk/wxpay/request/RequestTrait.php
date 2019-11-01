<?php

namespace app\common\sdk\wxpay\request;

/**
 * 微信支付API接口通用参数设置 app_id/mch_id/nonce_str/out_trade_no/transaction_id
 *
 * 包括isv公众账号id、isv商户号、随机字符串、订单号
 */
trait RequestTrait
{
    /**
     * 接口参数数据
     *
     * @var array
     */
    public $values = [];
    
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     *
     * @return $this
     **/
    public function setAppid($value)
    {
        $this->values['appid'] = $value;
        
        return $this;
    }

    /**
     * 申请商户号的appid或商户号绑定的appid
     *
     * @param string $value
     *
     * @return $this
     **/
    public function setMchAppid($value){

         $this->values['mch_appid'] = $value;

        return $this;
    }

    /**
     * 获取微信分配的公众账号ID的值
     *
     * @return string
     */
    public function getAppid()
    {
        return $this->values['appid'];
    }
    
    /**
     * 判断微信分配的公众账号ID是否存在
     *
     * @return bool
     **/
    public function isAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    
    /**
     * 设置商户号
     *
     * @param $value
     *
     * @return $this
     */
    public function setMchId($value)
    {
        $this->values['mchid'] = $value;
        
        return $this;
    }
    
    /**
     * 获取isv的商户号
     *
     * @return string
     */
    public function getMchId()
    {
        return $this->values['mchid'];
    }
    
    /**
     * 判断isv商户号是否存在
     *
     * @return bool
     */
    public function isMchIdSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    
    /**
     * 设置子的商户号
     *
     * @param $value
     *
     * @return $this
     */
    public function setSubMchId($value)
    {
        $this->values['sub_mch_id'] = $value;
        
        return $this;
    }
    
    /**
     * 获取子的商户号
     *
     * @return string
     */
    public function getSubMchId()
    {
        return $this->values['sub_mch_id'];
    }
    
    /**
     * 判断子商户号是否存在
     *
     * @return bool
     */
    public function isSubMchIdSet()
    {
        return array_key_exists('sub_mch_id', $this->values);
    }
    
    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     *
     * @param string $nonceStr
     *
     * @return $this
     */
    public function setNonceStr(string $nonceStr)
    {
        $this->values['nonce_str'] = $nonceStr;
        
        return $this;
    }
    
    /**
     * 获取随机字符串
     *
     * @return string
     */
    public function getNonceStr()
    {
        return $this->values['nonce_str'];
    }
    
    /**
     * 判断随机字符串是否存在
     *
     * @return bool
     */
    public function isNonceStrSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    
    /**
     * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
     *
     * @param string $outTradeNo
     *
     * @return $this
     */
    public function setOutTradeNo(string $outTradeNo)
    {
        $this->values['out_trade_no'] = $outTradeNo;
        
        return $this;
    }
    
    /**
     * 获取商户系统内部的订单号
     *
     * @return string
     */
    public function getOutTradeNo()
    {
        return $this->values['out_trade_no'];
    }
    
    /**
     * 判断商户系统内部的订单号是否存在
     *
     * @return bool
     */
    public function isOutTradeNoSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    
    /**
     * 设置微信订单号
     *
     * @param string $transactionId
     *
     * @return $this
     */
    public function setTransactionId(string $transactionId)
    {
        $this->values['transaction_id'] = $transactionId;
        
        return $this;
    }
    
    /**
     * 获取微信订单号
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->values['transaction_id'];
    }
    
    /**
     * 判断微信订单号是否存在
     *
     * @return bool
     */
    public function isTransactionIdSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }
    /**
     * 设置微信订单号
     *
     * @param string $transactionId
     *
     * @return $this
     */
    public function setSubAppid(string $transactionId)
    {
        $this->values['partner_trade_no'] = $transactionId;//wx26453a0cccb36663

        return $this;
    }

    /**
     * 获取微信订单号
     *
     * @return string
     */
    public function getSubAppid()
    {
        return $this->values['partner_trade_no'];
    }

    /**
     * 微信支付分配的终端设备号
     *
     * @return string
     */
    public function setDeviceInfo($value)
    {
        $this->values['device_info'] = $value;

        return $this;
    }

    /**
     * 微信支付分配的终端设备号
     *
     * @return string
     */
    public function getDeviceInfo()
    {
        return $this->values['device_info'];
    }
}