<?php

namespace app\common\sdk\wxpay;

class WxPayResults extends WxPayDataBase
{
    /**
     * @return bool
     * @throws \common\sdk\wxpay\WxPayException
     */
    public function CheckSign()
    {
        //fix异常
        if (!$this->IsSignSet()) {
            throw new WxPayException("签名错误！");
        }
        
        $sign = $this->MakeSign();
        if ($this->GetSign() == $sign) {
            return true;
        }
        
        throw new WxPayException("签名错误！");
    }
    
    /**
     * 使用数组初始化
     *
     * @param array $array
     */
    public function FromArray(array $array)
    {
        $this->values = $array;
    }
    
    /**
     * 使用数组初始化对象
     *
     * @param       $array
     * @param array $config
     * @param bool  $noCheckSign 是否检测签名
     *
     * @return WxPayResults
     * @throws WxPayException
     */
    public static function InitFromArray($array, array $config, $noCheckSign = false)
    {
        $obj = new self($config);
        $obj->FromArray($array);
        if ($noCheckSign == false) {
            $obj->CheckSign();
        }
        return $obj;
    }
    
    /**
     * 设置参数
     *
     * @param $key
     * @param $value
     */
    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }
    
    /**
     * 将xml转为array
     *
     * @param       $xml
     * @param array $config
     *
     * @return array
     * @throws WxPayException
     */
    public static function Init($xml, array $config)
    {
        $obj = new self($config);
        $obj->FromXml($xml);
        if ($obj->values['return_code'] != 'SUCCESS') {
            return $obj->GetValues();
        }
        $obj->CheckSign();
        
        return $obj->GetValues();
    }
}