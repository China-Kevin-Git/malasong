<?php

namespace app\common\sdk\wxpay;

/**
 * 数据对象基础类
 */
abstract class WxPayDataBase
{
    protected $values = [];
    
    /**
     * @var array 微信支付配置：
     *            app_id
     *            secret
     *            mch_id
     *            key
     *            notify_url
     */
    public $config = [];
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * 设置签名
     *
     * @return string
     */
    public function SetSign()
    {
        $sign = $this->MakeSign();
        $this->values['sign'] = $sign;
        return $sign;
    }
    
    /**
     * 获取签名
     *
     * @return string
     */
    public function GetSign()
    {
        return $this->values['sign'];
    }
    
    /**
     * 判断签名是否存在
     *
     * @return bool
     */
    public function IsSignSet()
    {
        return array_key_exists('sign', $this->values);
    }
    
    /**
     * 数组转xml
     *
     * @return string
     * @throws WxPayException
     */
    public function ToXml()
    {
        if (!is_array($this->values)
            || count($this->values) <= 0) {
            throw new WxPayException("数组数据异常！");
        }
        
        $xml = "<xml>";
        foreach ($this->values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    
    /**
     * xml转数组
     *
     * @param $xml
     *
     * @return array|mixed
     * @throws WxPayException
     */
    public function FromXml($xml)
    {
        if (!$xml) throw new WxPayException("xml数据异常！");
        
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        
        return $this->values;
    }
    
    /**
     * 参数格式化成url参数
     *
     * @return string
     */
    public function ToUrlParams()
    {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        
        return $buff;
    }
    
    /**
     * 生成签名
     *
     * @return string
     */
    public function MakeSign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->config['key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        
        return $result;
    }
    
    /**
     * 获取设置的值
     *
     * @return array
     */
    public function GetValues()
    {
        return $this->values;
    }
}