<?php

namespace app\common\sdk\wxpay\request;

/**
 * 统一下单接口
 */
class UnifiedOrderRequest extends Request
{
    use OrderRequestTrait;
    
    /**
     * 接口名，取自微信接口链接
     *
     * @var string
     */
    public $api_method = 'unifiedorder';
    
    /**
     * 微信分配的子商户公众账号ID
     *
     * 如需在支付完成后获取sub_openid则此参数必传。
     *
     * @param string $subAppId
     *
     * @return $this
     */
    public function setSubAppId(string $subAppId)
    {
        $this->values['sub_appid'] = $subAppId;
        
        return $this;
    }
    
    /**
     * 获取微信分配的子商户公众账号ID
     *
     * @return string
     */
    public function getSubAppId()
    {
        return $this->values['sub_appid'];
    }
    
    /**
     * 设置接收微信支付异步通知回调地址
     *
     * @param string $notifyUrl
     *
     * @return $this
     */
    public function setNotifyUrl(string $notifyUrl)
    {
        $this->values['notify_url'] = $notifyUrl;
        
        return $this;
    }
    
    /**
     * 获取接收微信支付异步通知回调地址
     *
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->values['notify_url'];
    }
    
    /**
     * 判断接收微信支付异步通知回调地址是否存在
     *
     * @return bool
     */
    public function isNotifyUrlSet()
    {
        return array_key_exists('notify_url', $this->values);
    }
    
    /**
     * 设置交易类型
     *
     * JSAPI 公众号支付 NATIVE 扫码支付 APP APP支付
     *
     * @param string $tradeType
     *
     * @return $this
     */
    public function setTradeType(string $tradeType)
    {
        $this->values['trade_type'] = $tradeType;
        
        return $this;
    }
    
    /**
     * 获取交易类型
     *
     * @return string
     */
    public function getTradeType()
    {
        return $this->values['trade_type'];
    }
    
    /**
     * 判断交易类型是否设置
     *
     * @return bool
     */
    public function isTradeTypeSet()
    {
        return array_key_exists('trade_type', $this->values);
    }
    
    /**
     * 设置商品ID
     *
     * trade_type=NATIVE时，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
     *
     * @param string $productId
     *
     * @return $this
     */
    public function setProductId(string $productId)
    {
        $this->values['product_id'] = $productId;
        
        return $this;
    }
    
    /**
     * 获取商品id
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->values['product_id'];
    }
    
    /**
     * 判断是否设置了商品id
     *
     * @return bool
     */
    public function isProductIdSet()
    {
        return array_key_exists('product_id', $this->values);
    }
    
    /**
     * 设置用户标识
     *
     * trade_type=JSAPI，此参数必传，用户在主商户appid下的唯一标识。
     * openid和sub_openid可以选传其中之一，如果选择传sub_openid,则必须传sub_appid。
     *
     * @param string $openId
     *
     * @return $this
     */
    public function setOpenId(string $openId)
    {
        $this->values['openid'] = $openId;
        
        return $this;
    }
    
    /**
     * 获取用户标识
     *
     * @return mixed
     */
    public function getOpenId()
    {
        return $this->values['openid'];
    }
    
    /**
     * 判断用户标识是否设置
     *
     * @return bool
     */
    public function isOpenIdSet()
    {
        return array_key_exists('openid', $this->values);
    }
    
    /**
     * 设置用户子标识
     *
     * openid和sub_openid可以选传其中之一，如果选择传sub_openid,则必须传sub_appid。
     *
     * @param string $subOpenId
     *
     * @return $this
     */
    public function setSubOpenId(string $subOpenId)
    {
        $this->values['sub_openid'] = $subOpenId;
        
        return $this;
    }
    
    /**
     * 获取用户子标识
     *
     * @return mixed
     */
    public function getSubOpenId()
    {
        return $this->values['sub_openid'];
    }
    
    /**
     * 判断用户子标识是否设置
     *
     * @return bool
     */
    public function isSubOpenIdSet()
    {
        return array_key_exists('sub_openid', $this->values);
    }
}