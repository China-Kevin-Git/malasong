<?php

namespace app\common\sdk\wxpay\request;
//* 查询退款（押金）
class RefundQueryRequest extends Request
{
    use RequestTrait;

    public $config = [];
    public $api_method = 'refundquery';
    
    public  function __construct(array $config)
    {  
        if(!$config){
            return false;
        } 
        
        $this->config = $config;
    }



    /**
     * 设置加密方式
     */
    public function setSignType($type = 'MD5'){
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
     * 商户退款单号
     */
    public function setOutRefundNo(string $out_refund_no){
        $this->values['out_refund_no'] = $out_refund_no;
        return $this;
    }
    /**
     * 微信退款单号
     */
    public function setRefundId(string $refund_id){
        $this->values['refund_id'] = $refund_id;
        return $this;
    }
}