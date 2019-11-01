<?php

namespace app\common\sdk\wxpay\request;

class MicroPayRequest extends Request
{
    use OrderRequestTrait;
    
    /**
     * 接口名，取自微信接口链接
     *
     * @var string
     */
    public $api_method = 'micropay';
    
    /**
     * 设置授权码
     *
     * @param $authCode
     *
     * @return $this
     */
    public function setAuthCode(string $authCode)
    {
        $this->values['auth_code'] = $authCode;
        
        return $this;
    }
    
    public function getAuthCode()
    {
        return $this->values['auth_code'];
    }
    
    public function isAuthCodeSet()
    {
        return array_key_exists('auth_code', $this->values);
    }
}