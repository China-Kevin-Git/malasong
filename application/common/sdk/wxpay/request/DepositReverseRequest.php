<?php

namespace app\common\sdk\wxpay\request;
//支付押金（撤销订单）
class DepositReverseRequest extends Request
{
    use RequestTrait;

    public $config = [];
    public $api_method = 'reverse';
    
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

}