<?php
/**
 * Created by PhpStorm.
 * User: SAMSUNG
 * Date: 2019/10/1
 * Time: 20:34
 */

namespace app\common\service;


class Service
{
    public static function set_err($data = [],$code = '000000',$msg = '') {
        return [ 'code'=> $code ,'msg' => $msg,'data' => $data];
    }

}