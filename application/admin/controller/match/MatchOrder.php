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
        $this->assign(OrderModel::systemPage($params));
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


}
