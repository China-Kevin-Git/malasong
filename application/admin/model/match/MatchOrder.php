<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\admin\model\match;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class SystemAdmin
 * @package app\admin\model\system
 */
class MatchOrder extends ModelBasic
{
    use ModelTrait;
    public static function systemPage($params)
    {

        $model = self::alias('a')->join('user r','r.uid=a.uid','LEFT')->field('a.*,r.nickname');
        if($params['keyword'] !== '') $model = $model->where('match_order_sn','LIKE',"%$params[keyword]%");
        $model = $model->order('a.match_order_id DESC');
        return self::page($model,$params);
    }
}