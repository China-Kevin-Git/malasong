<?php

namespace app\common\sdk\wxpay\request;
// 申请退款（押金）
class DepositRefundRequest extends Request
{
    use RequestTrait;

    public $config = [];
//    public $api_method = 'refund';
    
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
     * NO_CHECK：不校验真实姓名,FORCE_CHECK：强校验真实姓名
     */
    public function setCheckName(string $value = 'FORCE_CHECK'){
        $this->values['check_name'] = $value;
        return $this;
    }

    /**
     * 设置打款账户名
     */
    public function setUserName($name)
    {
        $this->values['re_user_name'] = $name;
        return $this;
    }

    /**
     * 设置打款金额
     */
    public function setAmount($amount)
    {
        $this->values['amount'] = $amount;
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
     * 企业付款备注
     */
    public function setDesc($desc){
        $this->values['desc'] = $desc;
        return $this;
    }


    /**
     * 该IP同在商户平台设置的IP白名单中的IP没有关联，该IP可传用户端或者服务端的IP。
     */
    public function setIp($spbill_create_ip){
        $this->values['spbill_create_ip'] = $spbill_create_ip;
        return $this;
    }
}