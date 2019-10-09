<?php

namespace app\admin\controller\match;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use app\admin\model\store\StoreProductAttr;
use app\admin\model\store\StoreProductAttrResult;
use app\admin\model\store\StoreProductRelation;
use app\admin\model\system\SystemConfig;
use service\JsonService;
use think\Db;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreCategory as CategoryModel;
use app\admin\model\store\StoreProduct as ProductModel;
use think\Url;

use app\admin\model\system\SystemAttachment;


/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class MatchRed extends AuthController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $match = Db::name("match_red")->select();
        foreach ($match as $k=>$v){
            $match[$k]['match_name'] = Db::name("match")->where(['id'=>$v['match_id']])->value("match_name");
        }
        $this->assign("list",$match);
        return $this->fetch();
    }

    /**

     * 添加分类管理

     * */

    public function create(){
        $f = array();
        $match =   Db::name("match")->select();
        $list=[];
        foreach ($match as $k=>$v){
            $list[$k]['value']=$v['id'];
            $list[$k]['label']=$v['match_name'];
        }

        $f[] = Form::checkbox('label','表单',[])->options(
            $list
        )->col(Form::col(50));

        $f[] = Form::select('spec_id','赛事赛程')->setOptions(function(){
            $list = Db::name("match_spec")->select();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['spec_id'],'label'=>$menu['name']];
            }
            return $menus;
        })->filterable(1);

        $f[] = Form::number('price','产品售价')->min(0)->col(8);
        $form = Form::make_post_form('添加分类',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    /**
     * s上传图片
     * */
    public function upload(){
        $res = Upload::image('file','article');
        $thumbPath = Upload::thumb($res->dir);
        if($res->status == 200)
            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    /**

     * 保存分类管理

     * */

    public function save(Request $request){
        $data = Util::postMore([
            'spec_id',
            'price',
            ['label',[]]],$request);

        if(empty($data['label'])) return Json::fail('所属赛事');
        if(empty($data['price'])) return Json::fail('产品售价');
        $data['car_time'] = time();
        $data['spec_name'] =Db::name("match_spec")->where(["spec_id"=>$data["spec_id"]])->value("name");
        $label = $data['label'];
        unset($data["image"]);
        unset($data["label"]);
        foreach ($label as $k=>$v){
            $data['match_id'] = $v;
            Db::name("match_red")->insert($data);
        }
        return Json::successful('添加成功!');
    }

    /**

     * 修改分类

     * */

    public function edit($id){
        if(!$id) return $this->failed('参数错误');
        $article = Db::name("match_red")->where(['red_id'=>$id])->find();
        if(!$article) return Json::fail('数据不存在!');
        $f = array();

//        $match =   Db::name("match")->select();
//        $list=[];
//        foreach ($match as $k=>$v){
//            $list[$k]['value']=$v['id'];
//            $list[$k]['label']=$v['match_name'];
//        }
//
//        $f[] = Form::checkbox('label','表单',[])->options(
//            $list
//        )->col(Form::col(50));


        $f[] = Form::number('price','产品售价',$article['price'])->min(0)->col(8);

        $form = Form::make_post_form('编辑分类',$f,Url::build('update',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }



    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'price',
                    ],$request);
        if(empty($data['price'])) return Json::fail('产品售价');

        $data['car_time'] = time();
        Db::name("match_red")->where(['red_id'=>$id])->update($data);


        return Json::successful('修改成功!');
    }

    /**
     * 删除分类
     * */
    public function delete($id)
    {
        $res = Db::name("match_red")->where(['red_id'=>$id])->delete();
        if(!$res)
            return Json::fail('删除失败,请稍候再试!');
        else
            return Json::successful('删除成功!');
    }



}
