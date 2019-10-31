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
class Match extends AuthController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $match = Db::name("match")->select();
        foreach ($match as $k=>$v){
            $match[$k]['match_stop'] = date("Y-m-d",$v['match_stop']);
            $match[$k]['enroll_time'] = date("Y-m-d",$v['enroll_time']);
            $match[$k]['address'] = $v['province'].$v['city'].$v['area'];
        }
        $this->assign("list",$match);
        return $this->fetch();
    }

    /**
     * 添加分类管理
     * */
    public function create(){
        $f = array();

        $f[] = Form::select('match_catrgory_id','父级id')->setOptions(function(){
            $list = Db::name("match_catrgory")->select();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['name']];
            }
            return $menus;
        })->filterable(1);

        $f[] = Form::input('match_name','赛事名字');
        //省市二级联动组件
        $f[] = Form::cityArea('address','比赛地址',[]);
        //日期区间选择组件
        $f[] = Form::dateRange(
            'limit_time',
            '比赛区间日期',
            strtotime('- 10 day'),
            time()
        );
        //日期区间选择组件
        $f[] = Form::dateTime(
            'enroll_time',
            '报名截止时间'
        );
        $f[] = Form::frameImageOne('image','赛事图片',Url::build('admin/widget.images/index',array('fodder'=>'image')))->icon('image');
        $form = Form::make_post_form('添加分类',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    /**
     * 上传图片
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
            'match_name',
            'match_catrgory_id',
            'content',
            'image',
            ['address',[]],
            ['limit_time',[]],
            'enroll_time',],$request);
        if(!$data['match_name']) return Json::fail('请输入赛事名称');
        if(!$data['match_catrgory_id']) return Json::fail('请输入赛分类名称');
        if(empty($data['image'])) return Json::fail('请选择赛事图片，并且只能上传一张');
        if(empty($data['address'])) return Json::fail('地址不能为空');

        $count = Db::name("match")->where('match_name',$data['match_name'])->count();
        if(!empty($count)) return Json::fail('赛事名称不能重复');

        $data['create_at'] = time();
        $data['logo'] = $data['image'];
        $data['province'] = $data['address'][0];
        $data['city'] = $data['address'][1];
        $data['area'] = $data['address'][2];
        $data['match_starat'] =strtotime($data['limit_time'][0]) ;
        $data['match_stop'] =strtotime($data['limit_time'][1]) ;
        $data['enroll_time'] =strtotime($data['enroll_time']);
        unset($data['address']);
        unset($data['limit_time']);
        unset($data['image']);
        $data['match'] =json_encode($data);
        Db::name("match")->insert($data);
        $match_follow['match_id'] = Db::name("match")->getLastInsID();
        $match_follow['match_name'] = $data['match_name'];
        $match_follow['province'] = $data['province'];
        $match_follow['city'] = $data['city'];
        $match_follow['area'] = $data['area'];
        $match_follow['create_at'] = time();
        $match = Db::name("match_follow")->insert($match_follow);
        if(!$match) return Json::fail('添加失败');
        return Json::successful('添加成功!');
    }

    /**

     * 修改分类

     * */

    public function edit($id){
        if(!$id) return $this->failed('参数错误');
        $article = Db::name("match")->where('id',$id)->find();
        if(!$article) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::select('match_catrgory_id','父级id',(string)$article['match_catrgory_id'])->setOptions(function(){
            $list = Db::name("match_catrgory")->select();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['name']];
            }
            return $menus;
        })->filterable(1);

        $f[] = Form::input('match_name','赛事名字',$article['match_name']);
        //省市二级联动组件
        $f[] = Form::cityArea('address','比赛地址',[$article['province'],$article['city'],$article['area']]);
        //日期区间选择组件
        $f[] = Form::dateRange(
            'limit_time',
            '比赛区间日期',
            $article['match_starat'],
            $article['match_stop']
        );
        //日期区间选择组件
        $f[] = Form::dateTime(
            'enroll_time',
            '报名截止时间',
            $article['enroll_time']
        );
        $f[] = Form::frameImageOne('image','赛事图片',Url::build('admin/widget.images/index',array('fodder'=>'image')),$article['logo'])->icon('image');
        $form = Form::make_post_form('编辑分类',$f,Url::build('update',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'match_name',
            'match_catrgory_id',
            'image',
            ['address',[]],
            ['limit_time',[]],
            'enroll_time',],$request);
        if(!$data['match_name']) return Json::fail('请输入赛事名称');
        if(!$data['match_catrgory_id']) return Json::fail('请输入赛分类名称');
        if(empty($data['image'])) return Json::fail('请选择赛事图片，并且只能上传一张');
        if(empty($data['address'])) return Json::fail('地址不能为空');
        $count = Db::name("match")->where('id',$id)->value("match_name");
        if($count != $data['match_name']){
            $count = Db::name("match")->where('match_name',$data['match_name'])->count();
            if(!empty($count)) return Json::fail('赛事名称不能重复');
        }

        $data['create_at'] = time();
        $data['logo'] = $data['image'];
        $data['province'] = $data['address'][0];
        $data['city'] = $data['address'][1];
        $data['area'] = $data['address'][2];
        $data['match_starat'] =strtotime($data['limit_time'][0]);
        $data['match_stop'] =strtotime($data['limit_time'][1]);
        $data['enroll_time'] =strtotime($data['enroll_time']);

        unset($data['address']);
        unset($data['limit_time']);
        unset($data['image']);

        $data['match'] =json_encode($data);
        $match =Db::name("match")->where(["id"=>$id])->update($data);
        $match_follow['match_id'] = $id;
        $match_follow['match_name'] = $data['match_name'];
        $match_follow['city'] = $data['city'];
        $match_follow['area'] = $data['area'];
        $match_follow['create_at'] = time();
         Db::name("match_follow")->where(["match_id"=>$id])->update($match_follow);

        if(!$match) return Json::fail('修改失败');
        return Json::successful('修改成功!');
    }

    /**
     * 删除分类
     * */
    public function delete($id)
    {
        $res = Db::name("match")->where(['id'=>$id])->delete();
        if(!$res)
            return Json::fail('删除失败,请稍候再试!');
        else
            return Json::successful('删除成功!');
    }

    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $product = Db::name("match")->where(['id'=>$id])->find();
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
        Db::name("match")->where(['id'=>$id])->update(["content"=>$data['content']]);
        return Json::successful('编辑成功!');
    }

}
