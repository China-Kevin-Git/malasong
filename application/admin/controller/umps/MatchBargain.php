<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/16 0016
 * Time: 10:39
 */

namespace app\admin\controller\umps;

use app\admin\controller\AuthController;
use app\admin\model\match\macth;
use app\admin\model\Match\MatchProduct;
use service\JsonService;
use service\UtilService as Util;
use service\FormBuilder as Form;
use service\UtilService;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\Match\macth as ProductModel;
use app\admin\model\umps\MatchBargain as MatchBargainModel;
use app\admin\model\system\SystemAttachment;

//砍价
class MatchBargain extends AuthController
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
        $where = Util::getMore([
            ['status',''],
            ['match_name',''],
            ['export',0],
            ['data',''],
        ],$this->request);
        $limitTimeList = [
            'today'=>implode(' - ',[date('Y/m/d'),date('Y/m/d',strtotime('+1 day'))]),
            'week'=>implode(' - ',[
                date('Y/m/d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                date('Y/m/d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
            ]),
            'month'=>implode(' - ',[date('Y/m').'/01',date('Y/m').'/'.date('t')]),
            'quarter'=>implode(' - ',[
                date('Y').'/'.(ceil((date('n'))/3)*3-3+1).'/01',
                date('Y').'/'.(ceil((date('n'))/3)*3).'/'.date('t',mktime(0,0,0,(ceil((date('n'))/3)*3),1,date('Y')))
            ]),
            'year'=>implode(' - ',[
                date('Y').'/01/01',date('Y/m/d',strtotime(date('Y').'/01/01 + 1year -1 day'))
            ])
        ];
        $this->assign('where',$where);
        $this->assign('countBargain',MatchBargainModel::getCountBargain());
        $this->assign('limitTimeList',$limitTimeList);
        $this->assign(MatchBargainModel::systemPage($where));
        $this->assign('bargainId',MatchBargainModel::getBargainIdAll($where));
        return $this->fetch();
    }

    /**
     * 异步获取砍价数据
     */
    public function get_bargain_list(){
        $where=Util::getMore([
            ['page',1],
            ['limit',20],
            ['export',0],
            ['match_name',''],
            ['status',''],
            ['data','']
        ]);
        $bargainList = MatchBargainModel::systemPage($where);
        if(is_object($bargainList['list'])) $bargainList['list'] = $bargainList['list']->toArray();
        $data = $bargainList['list']['data'];
        foreach ($data as $k=>$v){
            $data[$k]['_stop_time'] = date('Y/m/d H:i:s',$v['stop_time']);
        }
        return Json::successlayui(['count'=>$bargainList['list']['total'],'data'=>$data]);
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

    /**
     * 添加砍价
     * @param  int  $id
     * @return \think\Response
     */
    public function create()
    {
        $f = array();
        $f[] = Form::input('title','砍价活动名称');
        $f[] = Form::input('info','砍价活动简介')->type('textarea');
        $f[] = Form::input('Match_name','砍价产品名称');
        $f[] = Form::input('unit_name','单位')->placeholder('个、位');
        $f[] = Form::dateTimeRange('section_time','活动时间');
        $f[] = Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')))->icon('image');
        $f[] = Form::frameImages('images','产品轮播图(640*640px)',Url::build('admin/widget.images/index',array('fodder'=>'images')))->maxLength(5)->icon('images');
        $f[] = Form::number('price','显示原价')->min(0)->col(12);
        $f[] = Form::number('min_price','最低购买价')->min(0);
        $f[] = Form::number('bargain_max_price','单次砍价的最大金额')->min(0)->col(12);
        $f[] = Form::number('bargain_min_price','单次砍价的最小金额')->min(0)->col(12);
        $f[] = Form::number('cost','成本价')->min(0)->col(12);
        $f[] = Form::number('bargain_num','单次砍价的次数')->min(0)->col(12);
        $f[] = Form::number('stock','库存')->min(0)->col(12);
        $f[] = Form::number('sales','销量')->min(0)->col(12);
        $f[] = Form::number('sort','排序')->col(12);
        $f[] = Form::number('num','单次允许购买数量')->col(12);
        $f[] = Form::number('give_integral','赠送积分')->min(0)->col(12);
        $f[] = Form::number('postage','邮费')->min(0)->col(12);
        $f[] = Form::radio('is_postage','是否包邮',1)->options([['label'=>'是','value'=>1],['label'=>'否','value'=>0]])->col(12);
        $f[] = Form::radio('is_hot','热门推荐',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $f[] = Form::radio('status','活动状态',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $form = Form::make_post_form('添加用户通知',$f,Url::build('update'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
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
        $product = MatchBargainModel::get($id);
        if(!$product) return $this->failed('数据不存在!');
        $f = array();
        $f[] = Form::input('title','砍价活动名称',$product->getData('title'));
        $f[] = Form::hidden('product_id',$product->getData('product_id'));
        $f[] = Form::input('match_name','砍价产品名称',$product->getData('store_name'));
        $f[] = Form::dateTimeRange('section_time','活动时间',$product->getData('start_time'),$product->getData('stop_time'));//->format("yyyy-MM-dd HH:mm:ss");
        $f[] = Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')),$product->getData('image'))->icon('image');
        $f[] = Form::number('price','显示原价',$product->getData('price'))->min(0)->col(12);
        $f[] = Form::number('min_price','最低购买价',$product->getData('min_price'))->min(0)->col(12);
        $f[] = Form::number('bargain_max_price','单次砍价的最大金额',$product->getData('bargain_max_price'))->min(0)->col(12);
        $f[] = Form::number('bargain_min_price','单次砍价的最小金额',$product->getData('bargain_min_price'))->min(0)->col(12);
        $f[] = Form::number('bargain_num','单次砍价的次数',$product->getData('bargain_num'))->min(0)->col(12);
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
            ['title',''],
            ['info',''],
            ['match_name',''],
            ['section_time',[]],
            ['image',''],
            ['price',0],
            ['min_price',0],
            ['bargain_max_price',0],
            ['bargain_min_price',0],
            ['bargain_num',0],
            ['give_integral',0],
            ['product_id',0]
        ],$request);
        if($data['title'] == '') return JsonService::fail('请输入砍价活动名称');
        if($data['match_name'] == '') return JsonService::fail('请输入砍价产品名称');
        if(count($data['section_time'])<1) return JsonService::fail('请选择活动时间');
        if(!$data['section_time'][0]) return JsonService::fail('请选择活动时间');
        if(!$data['section_time'][1]) return JsonService::fail('请选择活动时间');
        $data['start_time'] = strtotime($data['section_time'][0]);
        $data['stop_time'] = strtotime($data['section_time'][1]);
        $data['cost'] = $data['price'];
        $data['store_name'] = $data['match_name'];
        unset($data['section_time']);
        if(!($data['image'])) return JsonService::fail('请选择推荐图');
        if($data['price'] == '' || $data['price'] < 0) return JsonService::fail('请输入砍价金额');
        if($data['min_price'] == '' || $data['min_price'] < 0) return JsonService::fail('请输入砍价最低金额');
        if($data['bargain_max_price'] == '' || $data['bargain_max_price'] < 0) return JsonService::fail('请输入用户单次砍价的最大金额');
        if($data['bargain_min_price'] == '' || $data['bargain_min_price'] < 0) return JsonService::fail('请输入用户单次砍价的最小金额');
        if($data['bargain_num'] == '' || $data['bargain_num'] < 0) return JsonService::fail('请输入用户单次砍价的次数');
        unset($data['img']);
        if($id){
            $product = MatchBargainModel::get($id);
            if(!$product) return Json::fail('数据不存在!');
            $res = MatchBargainModel::edit($data,$id);
            if($res) return JsonService::successful('修改成功');
            else return JsonService::fail('修改失败');
        }
        else{
            $res = MatchBargainModel::set($data);
            if($res) return JsonService::successful('添加成功');
            else return JsonService::fail('添加成功');
        }


    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!$id) return Json::fail('数据不存在');
        $product = MatchBargainModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $data['is_del'] = 1;
        if(MatchBargainModel::edit($data,$id))
            return Json::successful('删除成功!');
        else
            return Json::fail(MatchBargainModel::getErrorInfo('删除失败,请稍候再试!'));
    }

    /**
     * 显示内容窗口
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $seckill = MatchBargainModel::get($id);
        if(!$seckill) return $this->failed('数据不存在');
        $this->assign([
            'content'=>MatchBargainModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }
    public function edit_rule($id){
        if(!$id) return $this->failed('数据不存在');
        $seckill = MatchBargainModel::get($id);
        if(!$seckill) return $this->failed('数据不存在');
        $this->assign([
            'content'=>MatchBargainModel::where('id',$id)->value('rule'),
            'field'=>'rule',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'rule'])
        ]);
        return $this->fetch('public/edit_content');
    }
    /**
     * 开启砍价产品
     * @param int $id
     * @return mixed|\think\response\Json|void
     */
    public function bargain($id = 0){
        if(!$id) return $this->failed('数据不存在');
        $product = macth::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::input('title','砍价活动名称');
        $f[] = Form::input('info','砍价活动简介')->type('textarea');
        $f[] = Form::hidden('product_id',$product->getData('id'));
        $f[] = Form::input('match_name','砍价产品名称',$product->getData('match_name'));
        $f[] = Form::dateTimeRange('section_time','活动时间');//->format("yyyy-MM-dd HH:mm:ss");
        $f[] = Form::frameImageOne('image','产品主图片(305*305px)',Url::build('admin/widget.images/index',array('fodder'=>'image')),$product->getData('logo'))->icon('image');
        $f[] = Form::number('price','砍价金额')->min(0)->col(12);
        $f[] = Form::number('min_price','砍价最低金额',0)->min(0)->col(12);
        $f[] = Form::number('bargain_max_price','单次砍价的最大金额',10)->min(0)->col(12);
        $f[] = Form::number('bargain_min_price','单次砍价的最小金额',0.01)->min(0)->precision(2)->col(12);
        $f[] = Form::number('bargain_num','单次砍价的次数',1)->min(0)->col(12);
        $f[] = Form::number('num','单次购买的砍价产品数量',1)->col(12);
        $f[] = Form::radio('status','活动状态',1)->options([['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]])->col(12);
        $form = Form::make_post_form('开启砍价活动',$f,Url::build('update'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }
    /**
     * 修改砍价状态
     * @param $status
     * @param int $id
     */
    public function set_bargain_status($status,$id = 0){
        if(!$id) return JsonService::fail('参数错误');
        $res = MatchBargainModel::edit(['status'=>$status],$id);
        if($res) return JsonService::successful('修改成功');
        else return JsonService::fail('修改失败');
    }
}