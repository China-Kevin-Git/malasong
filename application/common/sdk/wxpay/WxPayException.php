<?php

namespace app\common\sdk\wxpay;

class WxPayException extends \Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}