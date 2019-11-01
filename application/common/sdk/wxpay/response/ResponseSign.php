<?php

namespace app\common\sdk\wxpay\response;

use common\sdk\wxpay\request\Request;
use common\sdk\wxpay\WxPayException;

class ResponseSign extends Request
{
    /**
     * 签名验证
     *
     * @return bool
     * @throws WxPayException
     */
    public function checkSign()
    {
        if (!$this->isSignSet()) {
            throw new WxPayException("签名错误！");
        }
        
        $sign = $this->makeSign();
        if ($this->getSign() == $sign) {
            return true;
        }
        
        throw new WxPayException("签名错误！");
    }
    
    /**
     * 初始化数据
     *
     * @param array $values
     */
    public function fromArray(array $values)
    {
        $this->values = $values;
    }
    
    /**
     * 验证数据签名
     *
     * @param array $values
     * @param array $config
     * @param bool  $noCheckSign
     *
     * @return ResponseSign
     * @throws WxPayException
     */
    public static function initFromArray(array $values, array $config, $noCheckSign = false)
    {
        $obj = new self($config);
        $obj->fromArray($values);
        if ($noCheckSign == false) {
            $obj->checkSign();
        }
        
        return $obj;
    }
    
    /**
     * 设置数据
     *
     * @param $key
     * @param $value
     */
    public function setData($key, $value)
    {
        $this->values[$key] = $value;
    }
    
    /**
     * 回调通知验证
     *
     * @param string $xml
     * @param array  $config
     *
     * @return array
     * @throws WxPayException
     */
    public static function init(string $xml, array $config)
    {
        $obj = new self($config);
        $obj->fromXml($xml);
        if ($obj->values['return_code'] != 'SUCCESS') {
            return $obj->getValues();
        }
        $obj->checkSign();
        
        return $obj->getValues();
    }
}