<?php

namespace app\admin\model\match;

use think\Model;

/**
 * Created by PhpStorm.
 * User: SAMSUNG
 * Date: 2019/10/1
 * Time: 19:58
 */
class comment extends Model
{
    protected $table = 'eb_comment';


//    protected $insert = ['match'];
//
//    protected $update = ['match'];

    protected $createTime = 'create_at';

    protected $updateTime = 'update_at';

    protected $autoWriteTimestamp = true;  //自动存储

    protected $dateFormat = 'Y-m-d H:i:s';  //返回时自动转换为时间格式类型

}