<?php

namespace app\common\sdk\wxpay\request;

/**
 * 订单api通用参数
 * device_info/body/detail/attach/
 * total_fee/fee_type/spbill_create_ip/goods_tag/
 * time_start/time_expire
 */
trait OrderRequestTrait
{
    use RequestTrait;
    
    /**
     * 设置终端设备号
     *
     * 门店号或收银设备ID ：PC网页或公众号内支付请传"WEB"
     *
     * @param string $deviceInfo
     *
     * @return $this
     */
    public function setDeviceInfo(string $deviceInfo = 'WEB')
    {
        $this->values['device_info'] = $deviceInfo;
        
        return $this;
    }
    
    /**
     * 获取终端设备号
     *
     * @return string
     */
    public function getDeviceInfo()
    {
        return $this->values['device_info'];
    }
    
    /**
     * 设置订单简要描述
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->values['body'] = $body;
        
        return $this;
    }
    
    /**
     * 获取订单简要描述
     *
     * PC网站——传入浏览器打开的网站主页title名-实际商品名称
     * 公众号——传入公众号名称-实际商品名称
     * H5——应用在浏览器网页上的场景，传入浏览器打开的移动网页的主页title名-实际商品名称
     * 线下门店——门店品牌名-城市分店名-实际商品名称
     * APP——需传入应用市场上的APP名字-实际商品名称
     *
     * @return string
     */
    public function getBody()
    {
        return $this->values['body'];
    }
    
    /**
     *  判断订单简要描述是否存在
     *
     * @return bool
     */
    public function isBodySet()
    {
        return array_key_exists('body', $this->values);
    }
    
    /**
     * 设置商品详细描述
     *
     * 对于使用单品优惠的商户，改字段必须按照规范上传：
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     *
     * @param string $detail
     *
     * @return $this
     */
    public function setDetail(string $detail)
    {
        $this->values['detail'] = $detail;
        
        return $this;
    }
    
    /**
     * 获取商品详细描述
     *
     * @return mixed
     */
    public function getDetail()
    {
        return $this->values['detail'];
    }
    
    /**
     * 设置附加数据
     *
     * 在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *
     * @param string $attach
     *
     * @return $this
     */
    public function setAttach(string $attach)
    {
        $this->values['attach'] = $attach;
        
        return $this;
    }
    
    /**
     * 获取附加数据
     *
     * @return string
     */
    public function getAttach()
    {
        return $this->values['attach'];
    }
    
    /**
     * 设置订单总金额
     *
     * 单位为分 只能为整数
     *
     * @param string $totalFee
     *
     * @return $this
     */
    public function setTotalFee(string $totalFee)
    {
        $this->values['total_fee'] = $totalFee;
        
        return $this;
    }
    
    /**
     * 获取订单总金额
     *
     * @return integer
     */
    public function getTotalFee()
    {
        return $this->values['total_fee'];
    }
    
    /**
     * 判断订单总金额是否设置
     *
     * @return bool
     */
    public function isTotalFeeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    
    /**
     * 设置货币类型
     *
     * 符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/native_sl.php?chapter=4_2
     *
     * @param string $feeType
     *
     * @return $this
     */
    public function setFeeType(string $feeType)
    {
        $this->values['fee_type'] = $feeType;
        
        return $this;
    }
    
    /**
     * 获取货币类型
     *
     * @return mixed
     */
    public function getFeeType()
    {
        return $this->values['fee_type'];
    }
    
    /**
     * 设置终端IP
     *
     * APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     *
     * @param string $spbillCreateIp
     *
     * @return $this
     */
    public function setSpbillCreateIp(string $spbillCreateIp)
    {
        $this->values['spbill_create_ip'] = $spbillCreateIp;
        
        return $this;
    }
    
    /**
     * 获取终端IP
     *
     * @return string
     */
    public function getSpbillCreateIp()
    {
        return $this->values['spbill_create_ip'];
    }
    
    /**
     * 检查终端IP是否存在
     *
     * @return bool
     */
    public function isSpbillCreateIp()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
    
    /**
     * 设置订单优惠标记，代金券或立减优惠功能的参数
     *
     * @param string $goodsTag
     *
     * @return $this
     */
    public function setGoodsTag(string $goodsTag)
    {
        $this->values['goods_tag'] = $goodsTag;
        
        return $this;
    }
    
    /**
     * 获取订单优惠标记
     *
     * @return string
     */
    public function getGoodsTag()
    {
        return $this->values['goods_tag'];
    }
    
    /**
     * 设置订单生成时间
     *
     * 如2009年12月25日9点10分10秒表示为20091225091010
     *
     * @param string $timeStart
     *
     * @return $this
     */
    public function setTimeStart(string $timeStart)
    {
        $this->values['time_start'] = $timeStart;
        
        return $this;
    }
    
    /**
     * 获取订单生成时间
     *
     * @return string
     */
    public function getTimeStart()
    {
        return $this->values['time_start'];
    }
    
    /**
     * 设置订单失效时间
     *
     * 如2009年12月25日9点10分10秒表示为20091225091010
     *
     * @param string $timeExpire
     *
     * @return $this
     */
    public function setTimeExpire(string $timeExpire)
    {
        $this->values['time_expire'] = $timeExpire;
        
        return $this;
    }
    
    /**
     * 获取订单失效时间
     *
     * @return string
     */
    public function getTimeExpire()
    {
        return $this->values['time_expire'];
    }

    /**
     * 设置附加数据
     *
     * 在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *
     * @param string $attach
     *
     * @return $this
     */
    public function setSceneInfo(string $sceneInfo)
    {
        $this->values['scene_info'] = $sceneInfo;

        return $this;
    }

    /**
     * 获取附加数据
     *
     * @return string
     */
    public function getSceneInfo()
    {
        return $this->values['scene_info'];
    }
}