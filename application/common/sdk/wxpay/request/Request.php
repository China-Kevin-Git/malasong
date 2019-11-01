<?php

namespace app\common\sdk\wxpay\request;

use app\common\sdk\wxpay\WxPayException;

/**
 * 微信接口参数基类
 */
abstract class Request
{
    use RequestTrait;
    
    /**
     * 微信支付配置
     * app_id
     * secret
     * mch_id
     * key
     * notify_url
     *
     * @var array
     */
    public $config = [];
    
    /**
     * 接口名称
     *
     * @var string
     */
    public $api_method;
    
    /**
     * Request constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * 设置签名
     *
     * @return string
     */
    public function setSign($sign_type='md5')
    {

        $this->values['sign'] = $this->makeSign($sign_type);
        
        return $this->values['sign'];
    }
    
    /**
     * 获取签名
     *
     * @return string
     */
    public function getSign()
    {
        return $this->values['sign'];
    }
    
    /**
     * 判断签名是否存在
     *
     * @return bool
     */
    public function isSignSet()
    {
        return array_key_exists('sign', $this->values);
    }
    
    /**
     * 数组转xml
     *
     * @return string
     * @throws WxPayException
     */
    public function toXml()
    {
        if (!is_array($this->values) || count($this->values) <= 0) {
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
    /*
     * 数组转换xml
     * @param array $arr 用于转换的数组
     * @return string 转换后得到的xml
     */
    public function array2xml($arr, $root = 'xml', $id = '', $statement = false)
    {
        $xml = "";
        $k = 0;
        foreach ($arr as $key => $val) {
            if ($id && $k == 0) {
                $snode = "<" . $key . " id =\"{$id}\">";
            } else {
                $snode = "<" . $key . ">";
            }
            if (is_array($val)) {
                $xml .= $snode;
                $xml .= array2xml($val, '');
                $xml .= "</" . $key . ">";
            } else if (!$val) {
                $xml .= "<" . $key . "/>";
            } else {
                $xml .= $snode;
                $xml .= $val;
                $xml .= "</" . $key . ">";
            }
            $k++;
        }

        if ($root) {
            $xml = "<{$root}>" . $xml . "</{$root}>";
        }

        if ($statement) {
            $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" . $xml;
        }
        return $xml;
    }
    /**
     * xml转数组
     *
     * @param $xml
     *
     * @return array
     * @throws WxPayException
     */
    public function fromXml($xml)
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
    public function toUrlParams()
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
    public function makeSign($signType='md5')
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->toUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->config['key'];
        //签名步骤三：MD5加密或者HMAC-SHA256
        if ($signType == 'md5') {
            //如果签名小于等于32个,则使用md5验证
            $string = md5($string);
        } else {
            //是用sha256校验
            $string = hash_hmac("sha256", $string, $this->config['key']);
        }
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        
        return $result;
    }

    /**
     * 获取设置的值
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}