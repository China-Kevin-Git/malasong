<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/16
 * Time: 11:01
 */

namespace app\ebapi\controller;


use service\JsonService;
use think\Db;

class Run extends AuthController
{
    /**
     * 创建跑团
     */
    public function runAdd()
    {
        $data = input("post.");
        if(empty($data["run_name"])){
            return JsonService::fail('跑团名称不能为空');
        }
        if(empty($data["contacts"])){
            return JsonService::fail('联系人不能为空');
        }
        if(empty($data["phone"])){
            return JsonService::fail('联系电话不能为空');
        }
        $array = [
            "run_name"=>$data["run_name"],
            "uid"=>$this->uid,
            "add_time"=>time(),
            "contacts"=>$data["contacts"],
            "phone"=>$data["phone"],
        ];
        //修改还是增加
        if(empty($data["id"])){
            $run = Db::name("run")->where(["uid"=>$this->uid])->count();
            if(!empty($run)){
                return JsonService::fail('一个人只能创建一个跑团');
            }
            Db::name("run")->insert($array);
        }else{
            Db::name("run")->where(["id"=>$data["id"]])->update($array);
        }
        return JsonService::successful('创建成功');
    }

    /**
     *  查看审核状态
     */
    public function runType()
    {
        $data = input("post.");
        $type = Db::name("run")->where(["id"=>$data['id']])->find();
        return JsonService::successful('获取成功',$type);

    }

    /**
     * 加入跑团
     *
     */
    public function runInsert()
    {
        $run_id = input("get.run_id");
        $run_arr  = Db::name("run_arr")->where(["run_id"=>$run_id,"uid"=>$this->uid])->find();
        if(!empty($run_arr)){
            return JsonService::fail('请不要重复加入');
        }
        if($run_arr != 1){
            return JsonService::fail('跑团还未通过审核');
        }
        $array = [
            "run_id"=>$run_id,
            "uid"=>$this->uid,
            "add_time"=>time(),
        ];
        Db::name("run_arr")->insert($array);
        return JsonService::successful('加入成功');
    }

    /**
     *跑团显示
     */
    public function echoRun()
    {
        $id=input("get.id");
        $run = Db::name("run")->where(["id"=>$id])->find();
        return JsonService::successful('获取成功',$run);

    }







}