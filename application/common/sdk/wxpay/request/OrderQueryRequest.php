<?php

namespace app\common\sdk\wxpay\request;

class OrderQueryRequest extends Request
{
    use OrderRequestTrait;
    
    public $api_method = 'orderquery';
}