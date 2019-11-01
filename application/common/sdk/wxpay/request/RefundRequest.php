<?php

namespace app\common\sdk\wxpay\request;

/**
 *  退款
 */
class RefundRequest extends Request
{
    use RequestTrait;
    
    /**
     * 接口名，取自微信接口链接
     *
     * @var string
     */
    public $api_method = 'refund';
    
    public function setOutRefundNo($refund_no)
    {
        $this->values['out_refund_no'] = $refund_no;
        
        return $this;
    }
    
    public function getOutRefundNo()
    {
        return $this->values['out_refund_no'];
    }
    
    public function isOutRefundNoSet()
    {
        return array_key_exists('out_refund_no', $this->values);
    }
    
    /**
     * 设置订单总金额
     *
     * @param $fee
     *
     * @return $this
     */
    public function setTotalFee($fee)
    {
        $this->values['total_fee'] = $fee;
        
        return $this;
    }
    
    public function isTotalFeeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    
    /**
     * 申请退款金额
     *
     * @param $fee
     *
     * @return $this
     */
    public function setRefundFee($fee)
    {
        $this->values['refund_fee'] = $fee;
        
        return $this;
    }
    
    public function isRefundFeeSet()
    {
        return array_key_exists('refund_fee', $this->values);
    }
    
    /**
     * 退款货币种类,默认人民币：CNY
     *
     * @param $type
     *
     * @return $this
     */
    public function setRefundFeeType($type)
    {
        $this->values['refund_fee_type'] = $type;
        
        return $this;
    }
    
    public function setNotifyUrl($url)
    {
        $this->values['notify_url'] = $url;
        
        return $this;
    }
    
    public function isNotifyUrlSet()
    {
        return array_key_exists('notify_url', $this->values);
    }
    
    /**
     * 设置操作员帐号, 默认为商户号
     *
     * @param $user
     *
     * @return $this
     */
    public function setOpUserId($user)
    {
        $this->values['op_user_id'] = $user;
        
        return $this;
    }
}