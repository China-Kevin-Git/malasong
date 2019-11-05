<?php

/**
 * Created by PhpStorm.
 * User: RJZ002
 * Date: 2019/6/4
 * Time: 12:51
 */

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * Class Receiving
 * @package app\command
 * 自动取消订单
 */

class Activity extends Command
{
    protected function configure()
    {

        $this->setName('Activity')->setDescription('Here is the remark ');
    }
    //自动删除到期的活动
    protected function execute(Input $input, Output $output)
    {

        $prom_goods = Db::name('match_order')->where(["is_pay"=>0])->field('match_order_id,add_time')->limit(100)->select();
        foreach ($prom_goods as &$v){
            if($v['add_time']+1800<=time()){
                //取消
                Db::name('match_order')->where('match_order_id',$v['match_order_id'])->update(["is_pay"=>3]);
            }
        }
        $output->writeln("Receiving:");
    }

}