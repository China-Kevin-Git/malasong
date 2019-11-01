<?php

namespace app\common\sdk\wxpay;

/**
 * JSAPI支付实现类
 *
 * 从微信公众平台获取code、通过code获取openid和access_token
 *
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 *
 */
class JsApiPay
{
    /**
     * 网页授权接口微信服务器返回的数据，返回样例如下
     *
     * "access_token":"ACCESS_TOKEN",
     * "expires_in":7200,
     * "refresh_token":"REFRESH_TOKEN",
     * "openid":"OPENID",
     * "scope":"SCOPE",
     * "unionid":""
     *
     * @var array
     */
    
    public $data = null;
    
    /**
     * 微信支付配置
     * app_id secret mch_id key notify_url
     *
     * @var array
     */
    public $config = [];
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * 通过跳转获取用户的openid
     *
     * @param $total
     * @param $remark
     *
     * @return string
     */
    public function GetOpenid($total, $remark)
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'] . "&total=" . $total . "&remark" . $remark);
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            
            return $openid;
        }
    }
    
    /**
     * 获取jsapi支付的参数
     *
     * @param       $UnifiedOrderResult
     * @param array $config
     *
     * @return string json数据，可直接填入js函数作为参数
     * @throws WxPayException
     */
    public function GetJsApiParameters($UnifiedOrderResult, array $config, $sub_appid = null)
    {
        if (!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "") {
            throw new WxPayException("参数错误，传入数据：" . var_export($UnifiedOrderResult, true));
        }
        $app_id = $sub_appid ?: $UnifiedOrderResult["appid"];
        $jsapi = new WxPayJsApiPay($config);
        $jsapi->SetAppid($app_id);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        
        return $parameters;
    }
    
    /**
     * 通过code从工作平台获取openid
     *
     * @param $code
     *
     * @return string
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        $openid = $data['openid'];
        
        return $openid;
    }
    
    /**
     * 拼接签名字符串
     *
     * @param $urlObj
     *
     * @return string
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        
        $buff = trim($buff, "&");
        
        return $buff;
    }
    
    /**
     * 获取地址js参数
     *
     * 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     *
     * @return string
     */
    public function GetEditAddressParameters()
    {
        $getData = $this->data;
        $data = array();
        
        $data["appid"] = $this->config['app_id'];
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);
        
        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => $this->config['app_id'],
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        
        return $parameters;
    }
    
    /**
     * 构造获取code的url连接
     *
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return string
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->config['app_id'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }
    
    /**
     * 构造获取open和access_toke的url地址
     *
     * @param string $code 微信跳转带回的code
     *
     * @return string
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->config['app_id'];
        $urlObj["secret"] = $this->config['secret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }
}