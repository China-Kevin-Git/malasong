<?php

namespace app\common\sdk\wxpay\request;

class FacePayQueryRequest extends Request
{
    use RequestTrait;

    public $config = [];
    public $api_method = 'FacePayQuery';
    
    public  function __construct(array $config)
    {  
        if(!$config){
            return false;
        } 
        
        $this->config = $config;
    }


     /**
     * 设置门店ID
     */
    public function setStoreId($store_id){
        $this->values['store_id'] = $store_id;
        
        return $this;
    }

    /**
     * 判断门店ID是否设置
     */
    public function isStoreIdSet(){
        return array_key_exists('store_id', $this->values);
    }

    /**
     * 设置门店名称
     */
    public function setStoreName($store_name){
        $this->values['store_name'] = $store_name;
        
        return $this;
    }

    /**
     * 判断门店名称是否设置
     */
    public function isStoreNameSet(){
        return array_key_exists('store_name', $this->values);
    }

    /**
     * 设置终端设备编号
     */
    public function setDeviceId($device_id){
        $this->values['device_id'] = $device_id;
        
        return $this;
    }

    /**
     * 判断终端设备编号是否设置
     */
    public function isDeviceIdSet(){
        return array_key_exists('device_id', $this->values);
    }

    /**
     * 设置人脸支付初始化数据
     */
    public function setRawData($data){
        $this->values['rawdata'] = $data;
        
        return $this;
    }

    /**
     * 判断人脸支付初始化数据是否设置
     */
    public function isRawDataSet(){
        return array_key_exists('rawdata', $this->values);
    }

    /**
     * 设置人脸支付初始化数据
     */
    public function setVersion(){
        $version = 1;//固定为1
        $this->values['version'] = $version;
        
        return $this;
    }

    /**
     * 设置当前时间
     */
    public function setNowTime(){
        $this->values['now'] = time();
        
        return $this;
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
     * 设置用户openid
     */
    public function setOpenId($openid){
        $this->values['openid'] = $openid;
        return $this;
    }

    /**
     * 设置face_code
     */
    public function setFaceCode($face_code){
        $this->values['face_code'] = $face_code;
        return $this;
    }

    /**
     * 设置调⽤微信⽀付API的机器IP
     */
    public function setSpbillCreateIp(string $ip){
        $this->values['spbill_create_ip'] = $ip;
        return $this;
    }
}