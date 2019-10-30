<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/16 0016
 * Time: 10:39
 */

namespace app\admin\controller\article;

use app\admin\controller\AuthController;
use service\JsonService;
use service\UtilService as Util;
use service\FormBuilder as Form;
use service\UtilService;
use think\Db;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\Match\macth as ProductModel;
use app\admin\model\umps\MatchBargain as MatchBargainModel;
use app\admin\model\system\SystemAttachment;

//砍价
class Comment extends AuthController
{
    use CurdControllerTrait;


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = Db::name("article_comment")->order("add_time desc")->select();
        foreach ($data as $k=>$v){
            if($v["type"]==0){
                $data[$k]["type"] = "待审核";
            }elseif ($v["type"]==1){
                $data[$k]["type"] = "审核成功";
            }else{
                $data[$k]["type"] = "审核失败";
            }
            $data[$k]['title'] = Db::name("article")->where(["id"=>$v["artilce_id"]])->value("title");
        }
        $this->assign("list",$data);
        return $this->fetch();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = Db::name("article_comment")->where('id',$id)->find();
        if(!$product) return $this->failed('数据不存在!');
        $f = array();
        $f[] = Form::radio('type','审核状态',$product['type'])->options([['label'=>'审核成功','value'=>1],['label'=>'审核失败','value'=>-1]])->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('update',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id='')
    {
        $data = UtilService::postMore([
            ['type',0],
        ],$request);
        if($id){
            $product = Db::name("article_comment")->where('id',$id)->find();
            if(!$product) return Json::fail('数据不存在!');
            $res = Db::name("article_comment")->where("id",$id)->update($data);
            if($res) return JsonService::successful('修改成功');
            else return JsonService::fail('修改失败');
        }


    }

}