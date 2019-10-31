<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/16 0016
 * Time: 10:39
 */

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use app\admin\model\Match\MatchProduct;
use service\JsonService;
use think\Request;
use service\UtilService;
use think\Db;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Url;
use app\admin\model\umps\MatchBargain as MatchBargainModel;
use app\admin\model\system\SystemAttachment;
use service\FormBuilder as Form;

//砍价
class Protocol extends AuthController
{
    use CurdControllerTrait;

    protected $bindModel = MatchBargainModel::class;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $data = Db::name("protocol")->select();
        $this->assign("list",$data);
        return $this->fetch();
    }

    /**
     * 添加砍价
     * @param  int  $id
     * @return \think\Response
     */
    public function create()
    {
        $f = array();
        $f[] = Form::input('title','名称');
        $f[] = Form::radio('type','审核状态',1)->options([['label'=>'用户服务协议','value'=>1],['label'=>'隐私政策','value'=>2],['label'=>'其他','value'=>3]])->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('update'));
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
            ['title',''],
            ['type',1],
        ],$request);
        if($data['title'] == '') return JsonService::fail('请输入名称');
        if($id){
            $product = Db::name("protocol")->where('id',$id)->find();
            if(!$product) return Json::fail('数据不存在!');
            $res = Db::name("protocol")->where("id",$id)->update($data);
            if($res) return JsonService::successful('修改成功');
            else return JsonService::fail('修改失败');
        }else{
            $res = Db::name("protocol")->insert($data);
            if($res) return JsonService::successful('添加成功');
            else return JsonService::fail('添加失败');
        }


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
        $product = Db::name("protocol")->where('id',$id)->find();
        if(!$product) return $this->failed('数据不存在!');
        $f = array();
        $f[] = Form::input('title','名称',$product['title']);
        $f[] = Form::radio('type','审核状态',$product['type'])->options([['label'=>'用户服务协议','value'=>1],['label'=>'隐私政策','value'=>2],['label'=>'其他','value'=>3]])->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('update',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }



    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','Match/bargain/'.date('Ymd'));
        if(is_array($res)){
            SystemAttachment::attachmentAdd($res['name'],$res['size'],$res['type'],$res['dir'],$res['thumb_path'],3,$res['image_type'],$res['time']);
            return Json::successful('图片上传成功!',['name'=>$res['name'],'url'=>Upload::pathToUrl($res['thumb_path'])]);
        }else
            return Json::fail($res);
    }

    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $product = Db::name("protocol")->where(['id'=>$id])->find();
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>$product['content'],
            'field'=>'content',
            'action'=>Url::build('content',['id'=>$id,'field'=>'content'])
        ]);

        return $this->fetch('public/edit_content');
    }

    public function content($id){
        $data = input("post.");
        Db::name("protocol")->where(['id'=>$id])->update(["content"=>$data['content']]);
        return Json::successful('编辑成功!');
    }

}