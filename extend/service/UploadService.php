<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/24
 */

namespace service;

use Api\Storage\COS\COS;
use Api\Storage\OSS\OSS;
use Api\Storage\Qiniu\Qiniu;
use app\core\util\SystemConfigService;

class UploadService
{

    private static $uploadStatus;

    //上传图片的大小 2MB 单位字节
    private static $imageValidate = ['size'=>2097152,'ext'=>'jpg,jpeg,png,gif','mime'=>'image/jpeg,image/gif,image/png'];

    /**
     * 初始化
     */
    private static function init()
    {
        self::$uploadStatus = new \StdClass();
    }

    /**
     * 返回失败信息
     * @param $error
     * @return mixed
     */
    protected static function setError($error)
    {
        self::$uploadStatus->status = false;
        self::$uploadStatus->error = $error;
        return self::$uploadStatus;
    }

    /**
     * 返回成功信息
     * @param $path
     * @param \think\File $fileInfo
     * @return mixed
     */
    protected static function successful($path, \think\File $fileInfo)
    {
        $filePath = DS . $path . DS . $fileInfo->getSaveName();
        self::$uploadStatus->filePath = self::pathToUrl($filePath);
        self::$uploadStatus->fileInfo = $fileInfo;
        self::$uploadStatus->uploadPath = $path;
        if(strpos(PUBILC_PATH,'public') !== false){
            self::$uploadStatus->dir = $filePath;
        }else{
            self::$uploadStatus->dir = str_replace('/public','',$filePath);
        }
        self::$uploadStatus->status = true;
        return self::$uploadStatus;
    }

    /**
     * 检查上传目录不存在则生成
     * @param $dir
     * @return bool
     */
    protected static function validDir($dir)
    {
        return is_dir($dir) == true || mkdir($dir,0777,true) == true;
    }

    /**
     * 开启/关闭上出文件验证
     * @param bool $bool
     */
    protected static function autoValidate($bool = false)
    {
        self::$autoValidate = $bool;
    }

    /**
     * 生成上传文件目录
     * @param $path
     * @param null $root
     * @return string
     */
    protected static function uploadDir($path, $root=null)
    {
        if($root === null) $root = UPLOAD_PATH;
        return $root . DS . $path;
    }

    /**
     * 单图上传
     * @param string $fileName 上传文件名
     * @param string $path 上传路径
     * @param bool $moveName 生成文件名
     * @param bool $autoValidate 是否开启文件验证
     * @param null $root 上传根目录路径
     * @param string $rule 文件名自动生成规则
     * @param int $type
     * @return mixed
     */
    public static function image($fileName, $path, $moveName = true, $autoValidate = true, $root = null, $rule='uniqid')
    {
        $uploadType = SystemConfigService::get('upload_type');
        $uploadType =1 ;
        $info = [];
        switch ($uploadType){
            case 1 :
                self::init();
                $path = self::uploadDir($path,$root);
                $dir = ROOT_PATH . $path;
                if(!self::validDir($dir)) return '生成上传目录失败,请检查权限!';
                if(!isset($_FILES[$fileName])) return '上传文件不存在!';
                $file = request()->file($fileName);
                if($autoValidate) $file = $file->validate(self::$imageValidate);
                $fileInfo = $file->rule($rule)->move($dir,$moveName);
                if(false === $fileInfo) return self::setError($file->getError());
                $imageInfo = self::successful($path,$fileInfo);
                $fileInfo = $imageInfo->fileInfo->getinfo();
                $info["code"] = 200;
                $info["name"] = $imageInfo->fileInfo->getSaveName();
                //TODO 入口是public需要替换图片路径
                if(strpos(PUBILC_PATH,'public') == false) $imageInfo->dir = str_replace('public/','', $imageInfo->dir);
                $info["dir"] = $imageInfo->dir;
                $info["time"] = time();
                $info["size"] = $fileInfo['size'];
                $info["type"] = $fileInfo['type'];
                $info["image_type"] = 1;
                $info['thumb_path'] = self::thumb($imageInfo->dir);
                if(!$imageInfo->status) return $imageInfo->error;
                break;
            case 2 :
                $keys = Qiniu::uploadImage($fileName);
                if(is_array($keys)){
                    foreach ($keys as $key=>&$item){
                        if(is_array($item)){
                            $info = Qiniu::imageUrl($item['key']);
                            $info['dir'] = UtilService::setHttps($info['dir']);
                            $headerArray = get_headers($info['dir'], true);
                            $info['size'] = $headerArray['Content-Length'];
                            $info['type'] = $headerArray['Content-Type'];
                            $info['image_type'] = 2;
                        }
                    }
                }else return $keys;
                break;
            case 3 :
                $serverImageInfo = OSS::uploadImage($fileName);
                if(!is_array($serverImageInfo)) return $serverImageInfo;
                $info['code'] = 200;
                $info['name'] = substr(strrchr($serverImageInfo['info']['url'],'/'),1);
                $serverImageInfo['info']['url'] = UtilService::setHttps($serverImageInfo['info']['url']);
                $info['dir'] = $serverImageInfo['info']['url'];
                $info['thumb_path'] = $serverImageInfo['info']['url'];
                $headerArray = get_headers($serverImageInfo['info']['url'], true);
                $info['size'] = $headerArray['Content-Length'];
                $info['type'] = $headerArray['Content-Type'];
                $info['time'] = time();
                $info['image_type'] = 3;
                break;
            case 4 :
                $serverImageInfo = COS::uploadImage($fileName);
                if(!is_array($serverImageInfo) && !is_object($serverImageInfo)) return $serverImageInfo;
                if(is_object($serverImageInfo)) $serverImageInfo = $serverImageInfo->toArray();
                $serverImageInfo['ObjectURL'] = UtilService::setHttps($serverImageInfo['ObjectURL']);
                $info['code'] = 200;
                $info['name'] = substr(strrchr($serverImageInfo['ObjectURL'],'/'),1);
                $info['dir'] = $serverImageInfo['ObjectURL'];
                $info['thumb_path'] = $serverImageInfo['ObjectURL'];
                $headerArray = get_headers($serverImageInfo['ObjectURL'], true);
                $info['size'] = $headerArray['Content-Length'];
                $info['type'] = $headerArray['Content-Type'];
                $info['time'] = time();
                $info['image_type'] = 4;
                break;
            default: return '上传类型错误，请先选择文件上传类型';
        }
        return $info;
    }

    /**
     * TODO 单图上传 内容
     * @param $key
     * @param $content
     * @param $path
     * @param null $root
     * @return array|string
     * @throws \Exception
     */
    public static function imageStream($key, $content, $path, $root = null){
        $uploadType = SystemConfigService::get('upload_type');
        $uploadType =1 ;
        $siteUrl = SystemConfigService::get('site_url').DS;
        $info = [];
        switch ($uploadType){
            case 1 :
                self::init();
                $path = self::uploadDir($path,$root);
                $dir = ROOT_PATH . $path;
                if(!self::validDir($dir)) return '生成上传目录失败,请检查权限!';
                $name = '.'.DS.$path.DS.$key;
                file_put_contents($name,$content);
                $info["code"] = 200;
                $info["name"] = $key;
                //TODO 入口是public需要替换图片路径
                if(strpos(PUBILC_PATH,'public') == false) $path = str_replace('public/','', $path);
                $info["dir"] = $path.DS.$key;
                $info["time"] = time();
                $headerArray = get_headers(str_replace('\\','/',$siteUrl.$info['dir']), true);
                $info['size'] = $headerArray['Content-Length'];
                $info['type'] = $headerArray['Content-Type'];
                $info["image_type"] = 1;
                $info['thumb_path'] = DS.$info['dir'];
                $info['dir'] = DS.$info['dir'];
                break;
            case 2 :
                $keys = Qiniu::uploadImageStream($key,$content);
                if(is_array($keys)){
                    foreach ($keys as $key=>&$item){
                        if(is_array($item)){
                            $info = Qiniu::imageUrl($item['key']);
                            $info['dir'] = UtilService::setHttps($info['dir']);
                            $headerArray = get_headers(str_replace('\\','/',$info['dir']), true);
                            $info['size'] = $headerArray['Content-Length'];
                            $info['type'] = $headerArray['Content-Type'];
                            $info['image_type'] = 2;
                        }
                    }
                }else return $keys;
                break;
            case 3 :
                $serverImageInfo = OSS::uploadImageStream($key,$content);
                if(!is_array($serverImageInfo)) return $serverImageInfo;
                $info['code'] = 200;
                $info['name'] = substr(strrchr($serverImageInfo['info']['url'],'/'),1);
                $serverImageInfo['info']['url'] = UtilService::setHttps($serverImageInfo['info']['url']);
                $info['dir'] = $serverImageInfo['info']['url'];
                $info['thumb_path'] = $serverImageInfo['info']['url'];
                $headerArray = get_headers(str_replace('\\','/',$serverImageInfo['info']['url']), true);
                $info['size'] = $headerArray['Content-Length'];
                $info['type'] = $headerArray['Content-Type'];
                $info['time'] = time();
                $info['image_type'] = 3;
                break;
            case 4 :
                $serverImageInfo = COS::uploadImageStream($key,$content);
                if(!is_array($serverImageInfo) && !is_object($serverImageInfo)) return $serverImageInfo;
                if(is_object($serverImageInfo)) $serverImageInfo = $serverImageInfo->toArray();
                $serverImageInfo['ObjectURL'] = UtilService::setHttps($serverImageInfo['ObjectURL']);
                $info['code'] = 200;
                $info['name'] = substr(strrchr($serverImageInfo['ObjectURL'],'/'),1);
                $info['dir'] = $serverImageInfo['ObjectURL'];
                $info['thumb_path'] = $serverImageInfo['ObjectURL'];
                $headerArray = get_headers(str_replace('\\','/',$serverImageInfo['ObjectURL']), true);
                $info['size'] = $headerArray['Content-Length'];
                $info['type'] = $headerArray['Content-Type'];
                $info['time'] = time();
                $info['image_type'] = 4;
                break;
            default: return '上传类型错误，请先选择文件上传类型';
        }
        return $info;
    }

    /**
     * 文件上传
     * @param string $fileName 上传文件名
     * @param string $path 上传路径
     * @param bool $moveName 生成文件名
     * @param bool $autoValidate 验证规则 [size:1024,ext:[],type:[]]
     * @param null $root 上传根目录路径
     * @param string $rule 文件名自动生成规则
     * @return mixed
     */
    public static function file($fileName, $path, $moveName = true, $autoValidate=[], $root=null, $rule='uniqid')
    {
        self::init();
        $path = self::uploadDir($path,$root);
        $dir = ROOT_PATH . $path;
        if(!self::validDir($dir)) return self::setError('生成上传目录失败,请检查权限!');
        if(!isset($_FILES[$fileName])) return self::setError('上传文件不存在!');
        $extension = strtolower(pathinfo($_FILES[$fileName]['name'], PATHINFO_EXTENSION));
        if(strtolower($extension) == 'php' || !$extension)
            return self::setError('上传文件非法!');
        $file = request()->file($fileName);
        if(count($autoValidate)>0) $file = $file->validate($autoValidate);
        $fileInfo = $file->rule($rule)->move($dir,$moveName);
        if(false === $fileInfo) return self::setError($file->getError());
        return self::successful($path,$fileInfo);
    }

    public static function pathToUrl($path)
    {
        return trim(str_replace(DS, '/', $path),'.');
    }

    public static function openImage($filePath)
    {
        return \think\Image::open($filePath);
    }


    /**
     * 图片压缩
     *
     * @param string $filePath 文件路径
     * @param int $ratio 缩放比例 1-9
     * @param string $pre 前缀
     * @return string 压缩图片路径
     */
    public static function thumb($filePath, $ratio=5, $pre='s_')
    {
        $uname=php_uname('s');
        if(strstr($uname,'Windows')!==false) $filePath = ltrim($filePath,'\\');
        else $filePath = ltrim($filePath,'/');
        $img = self::openImage($filePath);
        $width = $img->width() * $ratio / 10;
        $height = $img->height() * $ratio / 10;
        $dir = dirname($filePath);
        $fileName = basename($filePath);
        $savePath = $dir.DS.$pre.$fileName;
        $img->thumb($width,$height)->save($savePath);
        return DS.$savePath;
    }
}