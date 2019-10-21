<?php

namespace app\admin\controller\umps;

use app\admin\controller\AuthController;
use app\admin\model\match\macth;
use service\FormBuilder as Form;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\Match\macth as ProductModel;
use app\admin\model\umps\MatchCombinationAttr;
use app\admin\model\umps\MatchCombinationAttrResult;
use app\admin\model\umps\MatchCombination as MatchCombinationModel;
use think\Url;
use app\admin\model\system\SystemAttachment;
use app\admin\model\umps\MatchPink;

/**
 * 拼团管理
 * Class MatchCombination
 * @package app\admin\controller\Match
 */
class MatchCombination extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = MatchCombinationModel::class;

    /**
     * @return mixed
     */
    public function index()
    {
        $this->assign('countCombination',MatchCombinationModel::getCombinationCount());
        $this->assign(MatchCombinationModel::getStatistics());
        $this->assign('combinationId',MatchCombinationModel::getCombinationIdAll());
        return $this->fetch();
    }
    public function save_excel(){
        $where = Util::getMore([
            ['is_show',''],
            ['Match_name',''],
        ]);
        MatchCombinationModel::SaveExcel($where);
    }
    /**
     * 异步获取拼团数据
     */
    public function get_combination_list(Request $request){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['export',0],
            ['is_show',''],
            ['is_host',''],
            ['match_name','']
        ],$request);
        $combinationList = MatchCombinationModel::systemPage($where);
        if(is_object($combinationList['list'])) $combinationList['list'] = $combinationList['list']->toArray();
        $data = $combinationList['list']['data'];
        foreach ($data as $k=>$v){
            $data[$k]['_stop_time'] = date('Y/m/d H:i:s',$v['stop_time']);
        }
        return Json::successlayui(['count'=>$combinationList['list']['total'],'data'=>$data]);
    }

    public function combination($id = 0){
        if(!$id) return $this->failed('数据不存在');
        $product = macth::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::hidden('product_id',$id);
        $f[] = Form::input('title','拼团名称',$product->getData('match_name'));
        $f[] = Form::dateTimeRange('section_time','拼团时间');
        $f[] = Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')),$product->getData('logo'))->icon('image');
        $f[] = Form::number('price','拼团价')->min(0)->col(12);
        $f[] = Form::number('match_price','未拼团价格')->min(0)->col(12);
        $f[] = Form::number('people','拼团人数')->min(3)->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }
    
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $f = array();
        $f[] = Form::select('product_id','产品名称')->setOptions(function(){
            $list = ProductModel::getTierList();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['Match_name'].'/'.$menu['id']];
            }
            return $menus;
        })->filterable(1);
        $f[] = Form::input('title','拼团名称');
        $f[] = Form::dateTimeRange('section_time','拼团时间');
        $f[] = Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')))->icon('image');
        $f[] = Form::number('price','拼团价')->min(0)->col(12);
        $f[] = Form::number('people','拼团人数')->min(3)->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('save'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request,$id=0)
    {
        $data = Util::postMore([
            'product_id',
            'title',
            ['image',''],
            ['section_time',[]],
            'price',
            'match_price',
            'people',
            ['is_show',0],
            ['is_host',0],
            ['is_postage',0],
        ],$request);
        if(!$data['title']) return Json::fail('请输入拼团名称');
        if(!$data['image']) return Json::fail('请上传产品图片');
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品售价');
        if($data['match_price'] == '' || $data['match_price'] < 0) return Json::fail('请输入原价');
        if($data['people'] == '' || $data['people'] < 1) return Json::fail('请输入拼团人数');
        if(count($data['section_time'])<0) return Json::fail('请选择活动时间');
        $data['add_time'] = time();
        $data['start_time'] = strtotime($data['section_time'][0]);
        $data['stop_time'] = strtotime($data['section_time'][1]);
        unset($data['section_time']);
        if($id){
            $product = MatchCombinationModel::get($id);
            if(!$product) return Json::fail('数据不存在!');
            $data['product_id']=$product['product_id'];
            MatchCombinationModel::edit($data,$id);
            return Json::successful('编辑成功!');
        }else{
            $data['description'] = '';
            MatchCombinationModel::set($data);
            return Json::successful('添加拼团成功!');
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
        $product = MatchCombinationModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::hidden('product_id',$product->getData('product_id'));
        $f[] = Form::input('title','拼团名称',$product->getData('title'));
        $f[] = Form::dateTimeRange('section_time','拼团时间',$product->getData('start_time'),$product->getData('stop_time'));
        $f[] = Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')),$product->getData('image'))->icon('image');
        $f[] = Form::number('price','拼团价',$product->getData('price'))->min(0)->col(12);
        $f[] = Form::number('match_price','原价',$product->getData('match_price'))->min(0)->col(12);
        $f[] = Form::number('people','拼团人数',$product->getData('people'))->min(2)->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('save',compact('id')));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
//        $this->assign([
//            'title'=>'编辑产品','rules'=>$this->read($id)->getContent(),
//            'action'=>Url::build('update',array('id'=>$id))
//        ]);
//        return $this->fetch('public/common_form');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $data['is_del'] = 1;
        if(!MatchCombinationModel::edit($data,$id))
            return Json::fail(MatchCombinationModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**
     * 属性页面
     * @param $id
     * @return mixed|void
     */
    public function attr($id)
    {
        if(!$id) return $this->failed('数据不存在!');
        $result = MatchCombinationAttrResult::getResult($id);
        $image = MatchCombinationModel::where('id',$id)->value('image');
        $this->assign(compact('id','result','product','image'));
        return $this->fetch();
    }

    /**
     * 生成属性
     * @param int $id
     */
    public function is_format_attr($id = 0){
        if(!$id) return Json::fail('产品不存在');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $product = MatchCombinationModel::get($id);
        if(!$product) return Json::fail('产品不存在');
        $attrFormat = attrFormat($attr)[1];
        if(count($detail)){
            foreach ($attrFormat as $k=>$v){
                foreach ($detail as $kk=>$vv){
                    if($v['detail'] == $vv['detail']){
                        $attrFormat[$k]['price'] = $vv['price'];
                        $attrFormat[$k]['sales'] = $vv['sales'];
                        $attrFormat[$k]['pic'] = $vv['pic'];
                        $attrFormat[$k]['check'] = false;
                        break;
                    }else{
                        $attrFormat[$k]['price'] = '';
                        $attrFormat[$k]['sales'] = '';
                        $attrFormat[$k]['pic'] = $product['image'];
                        $attrFormat[$k]['check'] = true;
                    }
                }
            }
        }else{
            foreach ($attrFormat as $k=>$v){
                $attrFormat[$k]['price'] = $product['price'];
                $attrFormat[$k]['sales'] = $product['stock'];
                $attrFormat[$k]['pic'] = $product['image'];
                $attrFormat[$k]['check'] = false;
            }
        }
        return Json::successful($attrFormat);
    }

    /**
     * 添加 修改属性
     * @param $id
     */
    public function set_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $res = MatchCombinationAttr::createProductAttr($attr,$detail,$id);
        if($res)
            return $this->successful('编辑属性成功!');
        else
            return $this->failed(MatchCombinationAttr::getErrorInfo());
    }

    /**
     * 清除属性
     * @param $id
     */
    public function clear_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        if(false !== MatchCombinationAttr::clearProductAttr($id) && false !== MatchCombinationAttrResult::clearResult($id))
            return $this->successful('清空产品属性成功!');
        else
            return $this->failed(MatchCombinationAttr::getErrorInfo('清空产品属性失败!'));
    }

    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $product = MatchCombinationModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>MatchCombinationModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','Match/product/'.date('Ymd'));
        if(is_array($res)){
            SystemAttachment::attachmentAdd($res['name'],$res['size'],$res['type'],$res['dir'],$res['thumb_path'],2,$res['image_type'],$res['time']);
            return Json::successful('图片上传成功!',['name'=>$res['name'],'url'=>Upload::pathToUrl($res['thumb_path'])]);
        }else
            return Json::fail($res);
    }

    /**拼团列表
     * @return mixed
     */
    public function combina_list()
    {
        $where = Util::getMore([
            ['status',''],
            ['data',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(MatchPink::systemPage($where));
        return $this->fetch();
    }
    /**拼团人列表
     * @return mixed
     */
    public function order_pink($id){
        if(!$id) return $this->failed('数据不存在');
        $MatchPink = MatchPink::getPinkUserOne($id);
        if(!$MatchPink) return $this->failed('数据不存在!');
        $list = MatchPink::getPinkMember($id);
        $list[] = $MatchPink;
        $this->assign('list',$list);
        return $this->fetch();
    }/**
 * 修改拼团状态
 * @param $status
 * @param int $idd
 */
    public function set_combination_status($status,$id = 0){
        if(!$id) return Json::fail('参数错误');
        $res = MatchCombinationModel::edit(['is_show'=>$status],$id);
        if($res) return Json::successful('修改成功');
        else return Json::fail('修改失败');
    }


}
