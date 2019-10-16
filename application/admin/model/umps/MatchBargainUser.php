<?php
namespace app\admin\model\umps;

use basic\ModelBasic;
use traits\ModelTrait;

/**
 * 参与砍价Model
 * Class StoreBargainUser
 * @package app\admin\model\ump
 */
class MatchBargainUser extends ModelBasic
{
    use ModelTrait;

    /**
     * 获取参与人数
     * @param int $bargainId $bargainId 砍价产品ID
     * @param int $status   $status 状态
     * @return int|string
     */
    public static function getCountPeopleAll($bargainId = 0,$status = 0){
        if(!$bargainId) return 0;
        $model = new self();
        $model = $model->where('bargain_id',$bargainId);
        if($status) $model = $model->where('status',$status);
        return $model->count();
    }

}