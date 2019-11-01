<?php

namespace app\common\sdk\wxpay\request;

class DepositOrderQueryRequest extends Request
{
    use RequestTrait;

    public $config = [];
    public $api_method = 'orderquery';
    
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