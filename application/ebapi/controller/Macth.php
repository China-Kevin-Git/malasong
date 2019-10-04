<?php

namespace app\ebapi\controller;

use app\common\service\MacthService;
use think\Db;
use think\Request;
use service\UtilService as Util;
use service\PHPTreeService as Phptree;
use app\admin\model\article\ArticleCategory as ArticleCategoryModel;

/**
 * 赛事接口
 * Class AgentManage
 * @package app\admin\controller\agent
 */
class Macth extends AuthController
{
    public function request()
    {
        header('Content-type:text/json');
        $request = Request::instance();
        return $request;
    }

    /**
     * 编辑赛事
     * @return array
     */
    public function index()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        $result = MacthService::editMacth($data);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson(['id' => $result['data']['id']]);
    }

    /**
     * 根据日期统计当日赛事
     * @return array
     */
    public function matchCount()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        if (!isset($data['date']) || !$data['date']) {
            return self::asJson([], '1', '缺少参数数据');
        }
        $date['start'] = strtotime( date('Y-m-d 00:00:00',$data['date']));
        $date['stop']  = strtotime(date('Y-m-d 23:59:59',$data['date']));

        $result = MacthService::countMatch($date);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 查询赛事【单条】
     * @return array
     */
    public function queryMatch()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        if (!isset($data['id']) || !$data['id']) {
            return self::asJson([], '1', '缺少参数数据');
        }
        $result = MacthService::queryMatch($data['id']);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 赛事关注查询
     * @return array
     */
    public function queryFollow()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        $result = MacthService::queryFollow($data);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 显示后台管理员添加的图文[文章]
     * @return mixed
     */
    public function article()
    {
        $result = MacthService::queryArticle();
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 创建赛事评论
     * @return array
     */
    public function comment()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        $result = MacthService::addComment($data);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 获取赛事评论
     * @return array
     */
    public function queryComment()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        $result = MacthService::queryComment($data);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 删除赛事评论
     * @return array
     */
    public function deteleComment()
    {
        $request = self::request();
        $data = $request->param(); //接收请求数据
        $result = MacthService::deleteComment($data);
        if ($result['code'] != '000000') {
            return self::asJson([], $result['code'], $result['msg']);
        }
        return self::asJson($result['data']);
    }

    /**
     * 全部赛事
     *
     */
    public function allMacth()
    {
        $data=input('post.');

        //type:1 分类 2.日期 3.地区
        if(empty($data['type'])){
            $data['type']=0;
        }
        if($data['type']==1){
            $where=" a.match_catrgory_id=".$data['value'];
        }elseif($data['type']==2){
            $date=explode('-',$data['value']);
            $star_time = date("Y-m-d H:i:s", mktime(0, 0, 0, (float)$date[1],(float)$date[2],(float)$date[0]));
            $end_time = date("Y-m-d H:i:s", mktime(0, 0, 0, (float)$date[1], (float)$date[2]+1, (float)$date[0]));
            $time["star"] = strtotime($star_time);
            $time["end"] = strtotime($end_time);
            $where="a.match_starat BETWEEN ".$time["star"] ." AND ".$time["end"];
        }elseif($data['type']==3){
            $where=" a.province=".$data['value'];
        }else{
            $where="1=1";
        }
        //order_type  1  时间排序 2 人气  默认时间
        if(empty($data['order_type'])){
            $data['order_type']=0;
        }

        if($data['order_type']==2){
            $order="b.follow_num desc";
        }else{
            $order="a.match_starat desc";
        }

        $match=Db::name('match')
            ->field('a.id,a.match_name,a.province,a.city,a.match_starat,a.logo,b.follow_num')
            ->alias('a')
            ->join('match_follow b','a.id=b.match_id')
            ->where($where)
            ->order($order)
            ->page($data['page'],5)
            ->select();
        foreach ($match as $k=>$v){
            $match[$k]['address']=$v['province'].$v['city'];
            $match[$k]['match_starat']=date('Y-m-d',$v['match_starat']);
            unset($match[$k]['province']);
            unset($match[$k]['city']);
        }
        return self::asJson($match);

    }

    /**
     * 赛事分类
     *
     */
    public function macthCatgory(){
        $match=Db::name('match_catrgory')
            ->select();
        return self::asJson($match);

    }

    /**
     * 赛事详情
     */
    public function details(){

    }

}