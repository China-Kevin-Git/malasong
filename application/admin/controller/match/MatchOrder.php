<?php

namespace app\admin\controller\match;

use app\admin\controller\AuthController;
use app\admin\model\match\MatchOrder as OrderModel;
use service\FormBuilder as Form;

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
class MatchOrder extends AuthController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $params = Util::getMore([
            ['keyword','']
        ],$this->request);
        if($params['keyword'] == ''){
            $where = "1=1";
        }else{
            $where= "a.match_order_sn  LIKE  '%".$params["keyword"]."%' ";
        }
        $match_order= Db::name("match_order")
            ->alias('a')
            ->join('user r','r.uid=a.uid','LEFT')
            ->field('a.*,r.nickname')
            ->where($where)
            ->order('a.match_order_id DESC')
            ->select();
        foreach ($match_order as $k=>$v){
            if($v["red_id"]==0){
                $match_order[$k]["red_name"] = "无";
            }else{
                $match_order[$k]["red_name"] = Db::name("match_red")->where(["red_id"=>$v["red_id"]])->value("spec_name");
            }
            if($v["meal_id"]==0){
                $match_order[$k]["meal_name"] = "无";
            }else{
                $match_order[$k]["meal_name"] = Db::name("match_meal")->where(["meal_id"=>$v["meal_id"]])->value("title");
            }
            if(empty($v["service_id"])){
                $match_order[$k]["service_name"] = "无";
            }else{
                $service_id = json_decode($v["service_id"],true);
                foreach ($service_id as $j=>$i){
                    $service_id[$j] = $i["goods_name"].":".$i["price"] ."元 X ".$i["num"]."件";
                }
                $match_order[$k]["service_name"] = implode(" , ",$service_id);
            }
        }
        $this->assign("list",$match_order);
        return $this->fetch();
    }


    /**
     * 删除分类
     * */
    public function delete($id)
    {
        $res = Db::name("match_order")->where(['match_order_id'=>$id])->delete();
        if(!$res)
            return Json::fail('删除失败,请稍候再试!');
        else
            return Json::successful('删除成功!');
    }

    /**
     * 删除分类
     * */
    public function means($id)
    {

        $f = array();
        $means = Db::name("match_means")->where("match_order_id",$id)->find();
        $f[] = Form::input('name','用户真实姓名',$means["name"]);
        $f[] = Form::input('nationality','国籍',$means["nationality"]);
        $f[] = Form::input('sex','性别',$means["sex"]);
        $f[] = Form::input('blood','血型',$means["blood"]);
        $f[] = Form::input('age','年龄',$means["age"]);
        $f[] = Form::input('mobile','电话',$means["mobile"]);
        $f[] = Form::input('mail','邮件',$means["mail"]);
        $f[] = Form::input('document','证件类型',$means["document"]);
        $f[] = Form::input('number','证件号码',$means["number"]);
        $f[] = Form::input('emergency','紧急联系人',$means["emergency"]);
        $f[] = Form::input('emergency_mobile','紧急联系人电话',$means["emergency_mobile"]);
        $f[] = Form::input('residence','居住地省份',$means["residence"]);
        $f[] = Form::input('city','居住地城市',$means["city"]);
        $f[] = Form::input('street','居住地街道信息',$means["street"]);
        $f[] = Form::input('size','衣服尺码',$means["size"]);
        $f[] = Form::input('passport_name','护照姓名（姓/Surname，名/Given names）',$means["passport_name"]);
        $f[] = Form::input('passport_number','护照号码',$means["passport_number"]);
        $form = Form::make_post_form('返回',$f,Url::build('indexs'));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }



    /**

     * 修改分类

     * */

    public function edit($id){
        if(!$id) return $this->failed('参数错误');
        $article = Db::name("match_order")->where('match_order_id',$id)->find();
        if(!$article) return Json::fail('数据不存在!');
        $f = array();
        $f[] = Form::input('number','中签信息',$article["number"]);
        $form = Form::make_post_form('编辑分类',$f,Url::build('update',array('id'=>$id)));
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');

    }

    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'number',
           ],$request);

        $match =Db::name("match_order")->where(["match_order_id"=>$id])->update($data);


        if(!$match) return Json::fail('修改失败');
        return Json::successful('修改成功!');
    }


    /**
     * 删除分类
     * */
    public function indexs()
    {
        return Json::successful('返回成功!');
    }

}
