<?php

namespace app\common\sdk\wxpay;

class WxPayNotify extends WxPayNotifyReply
{
    /**
     * 回调入口
     *
     * @param array $config
     * @param bool  $needSign
     *
     * @throws WxPayException
     */
    final public function Handle(array $config, $needSign = true)
    {
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
        $result = WxPayApi::notify(array($this, 'NotifyCallBack'), $msg, $config);
        if ($result == false) {
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return;
        } else {
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        }
        $this->ReplyNotify($needSign);
    }
    
    /**
     * 回调方法入口，子类可重写该方法
     *
     * @param array  $data 回调解释出的参数
     * @param string $msg  如果回调处理失败，可以将错误信息输出到该方法
     *
     * @return bool
     */
    public function NotifyProcess(array $data, string &$msg)
    {
        //TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        $msg = $data;// 需要重写
        return true;
    }
    
    /**
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     *
     * @param $data
     *
     * @return bool
     */
    final public function NotifyCallBack($data)
    {
        $msg = "OK";
        $result = $this->NotifyProcess($data, $msg);
        
        if ($result == true) {
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        } else {
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
        }
        return $result;
    }
    
    /**
     * 回复通知
     *
     * @param bool $needSign
     *
     * @throws WxPayException
     */
    final private function ReplyNotify($needSign = true)
    {
        //如果需要签名
        if ($needSign == true &&
            $this->GetReturn_code() == "SUCCESS") {
            $this->SetSign();
        }
        WxPayApi::replyNotify($this->ToXml());
    }
}