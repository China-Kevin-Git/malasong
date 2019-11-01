<?php

namespace app\common\sdk\wxpay\request;

class OrderCloseRequest extends Request
{
    use OrderRequestTrait;
    
    public $api_method = 'closeorder';
}