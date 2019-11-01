<?php

namespace app\common\sdk\wxpay\response;

use Yii;
use common\sdk\wxpay\request\Request;

/**
 * 微信接口返回结果处理
 */
class Response
{
    /**
     * 请求对象
     *
     * @var Request
     */
    protected $request;
    
    /**
     * 接口返回结果
     *
     * @var array
     */
    protected $response = [];
    
    /**
     * 是否开启日志
     *
     * @var bool
     */
    protected $log_flag = false;
    
    public function __construct($request, $response)
    {
        // if (app()->environment() != 'production') {
        //     $this->log_flag = true;
        // }
        
        $this->request = $request;
        $this->response = $response;
    }
    
    /**
     * 结果处理
     *
     * @return mixed
     */
    public function handle()
    {
        // 通信错误返回信息
        if ($this->response['return_code'] == 'FAIL')
            return ['error' => 1, 'error_msg' => $this->response['return_msg'], 'error_code' => ''];
        
        // 接口参数错误返回信息
        if ($this->response['result_code'] == 'FAIL')
            return ['error' => 1, 'error_msg' => $this->response['err_code_des'], 'error_code' => $this->response['err_code']];
        
        $method = $this->request->api_method . 'Handle';
        
        return $this->$method();
    }
    
    /**
     * 统一下单接口处理
     *
     * @return array
     */
    protected function unifiedorderHandle()
    {
        $data = [
            'sign' => $this->response['sign'],
            'appid' => $this->response['appid'],
            'mch_id' => $this->response['mch_id'],
            'prepay_id' => $this->response['prepay_id'],
            'sub_mch_id' => $this->response['sub_mch_id'],
            'code_url' => $this->response['code_url'] ?? '',
        ];
        
        return ['error' => 0, 'data' => $data];
    }
    
    /**
     * 微信刷卡支付数据
     *
     * @return array
     */
    protected function micropayHandle()
    {
        $data = [
            'order_number' => $this->response['out_trade_no'],
            'trade_no' => $this->response['transaction_id'],
            'pay_at' => strtotime($this->response['time_end']),
            'pay_status' => 1,
        ];
        
        return ['error' => 0, 'data' => $data];
    }

    /**
     * 刷脸支付获取authinfo
     */
    protected function facePayHandle(){
        $data = [
            'order_number' => $this->response['out_trade_no'],
            'trade_no' => $this->response['transaction_id'],
            'merchant_trade_no' => $this->response['transaction_id'],
            'pay_at' => $this->response['time_end'] ? strtotime($this->response['time_end']) : 0,
            'pay_status' => $this->response['time_end'] ? 1 : 0
        ];

        return ['error' => 0, 'data' => $data];
    }
    
    /**
     * 订单查询
     *
     * @return array
     */
    protected function orderqueryHandle()
    {
        $data = [
            'pay_status' =>  $this->tradeStateTransform($this->response['trade_state']),
            'trade_no' => $this->response['transaction_id'] ?? '',
            'order_number' => $this->response['out_trade_no'],
            'pay_at' => isset($this->response['time_end']) ? strtotime($this->response['time_end']) : 0,
        ];
        
        return ['error' => 0, 'data' => $data];
    }
    /**
     * 订单查询
     *
     * @return array
     */
    protected function FacePayQueryHandle()
    {
        $data = [
            'pay_status' =>  $this->tradeStateTransform($this->response['trade_state']),
            'trade_no' => $this->response['transaction_id'] ?? '',
            'order_number' => $this->response['out_trade_no'],
            'pay_at' => isset($this->response['time_end']) ? strtotime($this->response['time_end']) : 0,
        ];

        return ['error' => 0, 'data' => $data];
    }


    /**
     * 订单关闭
     *
     * @return array
     */
    protected function closeorderHandle()
    {
        //撤销成功
        $data = [
            'pay_status' => 2
        ];

        return ['error' => 0, 'data' => $data];
    }

    
    protected function reverseHandle()
    {
        return ['error' => 0, 'data' => ''];
    }

    //退款处理
    protected function refundHandle()
    {
        $data = [
            'out_refund_no' => $this->response['refund_id'],
            'refund_status' => 0,
        ];
    
        return ['error' => 0, 'data' => $data];
    }

    //退款查询处理
    protected function refundqueryHandle()
    {
        $data = [];
        foreach ($this->response as $key => $val){
            //微信退款单号
            if(strpos($key, "refund_id") !== false){
                $data['out_refund_no'] = $val;
            }
            //退款状态 SUCCESS—退款成功 REFUNDCLOSE—退款关闭。PROCESSING—退款处理中 CHANGE—退款异常
            if(strpos($key, "refund_status") !== false){
                $status = ['SUCCESS' => 1, 'CHANGE' => 2];
                $data['refund_status'] = $status[$val] ?? 0;
            }
            //退款时间
            if(strpos($key, "refund_success_time") !== false){
                $data['success_time'] = $val;
                $data['refund_at'] = strtotime($val);
            }
        }


        return ['error' => 0, 'data' => $data];
    }
    
    /**
     * 订单支付状态转换
     *
     * @param $trade_state
     *
     * @return int
     */
    protected function tradeStateTransform($trade_state)
    {
        switch ($trade_state) {
            case "SUCCESS": // 支付成功
                $pay_status = 1;
                break;
            case "REFUND": // 退款
                $pay_status = 3;
                break;
            case "CLOSED": // 订单关闭，不可退款
                $pay_status = 2;
                break;
            case "REVOKED": // 已撤销支付(付款码支付)
                $pay_status = 2;
                break;
            case "PAYERROR": // 支付失败
                $pay_status = 4;
                break;
            default: // NOTPAY + USERPAYING 未支付
                $pay_status = 0;
        }
        
        return $pay_status;
    }
}