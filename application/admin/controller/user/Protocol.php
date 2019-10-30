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
use service\UtilService as Util;
use service\FormBuilder as Form;
use service\UtilService;
use think\Db;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Url;
use app\admin\model\umps\MatchBargain as MatchBargainModel;
use app\admin\model\system\SystemAttachment;

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