<?php

namespace app\admin\controller\system;

use app\admin\model\system\SystemAttachment as SystemAttachmentModel;
use app\admin\controller\AuthController;
use service\SystemConfigService;
use service\UploadService as Upload;
/**
 * 附件管理控制器
 * Class SystemAttachment
 * @package app\admin\controller\system
 *
 */
class SystemAttachment extends AuthController
{

//    /**
//     * 编辑器上传图片
//     * @return \think\response\Json
//     */
//    public function upload()
//    {
//        $res = Upload::image('upfile','editor/'.date('Ymd'));
//
//        if($res->status==false && $res->error){
//            exit(json_encode(['state'=>$res->error]));
//        }
//
//        //产品图片上传记录
//        $fileInfo = $res->fileInfo->getinfo();
//        $thumbPath = Upload::thumb($res->dir);
//        SystemAttachmentModel::attachmentAdd($res->fileInfo->getSaveName(),$fileInfo['size'],$fileInfo['type'],$res->dir,$thumbPath,0);
//        $info = array(
//            "originalName" => $fileInfo['name'],
//            "name" => $res->fileInfo->getSaveName(),
//            "url" => '.'.$res->dir,
//            "size" => $fileInfo['size'],
//            "type" => $fileInfo['type'],
//            "state" => "SUCCESS"
//        );
//        echo json_encode($info);
//    }

    /**
     * TODO 编辑器上传图片
     */
    public function upload()
    {
        $res = Upload::image('upfile','editor/'.date('Ymd'));
        if(is_array($res)){
            SystemAttachmentModel::attachmentAdd($res['name'],$res['size'],$res['type'],$res['dir'],$res['thumb_path'],0,$res['image_type'],$res['time']);
            $info["originalName"] = $res['name'];
            $info["name"] = $res['name'];
            $info["url"] = '.'.$res['dir'];
            $info["size"] = $res['size'];
            $info["type"] = $res['type'];
            $info["state"] = "SUCCESS";
        }
        echo json_encode($info);
    }
}
