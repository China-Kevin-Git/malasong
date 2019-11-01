<?php

namespace app\common\sdk\wxpay\request;

class ReverseRequest extends Request
{
    /**
     * 接口名，取自微信接口链接
     *
     * @var string
     */
    public $api_method = 'reverse';
}