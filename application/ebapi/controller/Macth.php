<?php

namespace app\ebapi\controller;

use app\common\service\MacthService;
use think\Controller;
use think\Db;
use think\Request;

/**
 * 赛事接口
 * Class AgentManage
 * @package app\admin\controller\agent
 */
class Macth extends Controller
{

    /**
     * 统一返回格式
     * @param array $data
     * @param string $code
     * @param string $msg
     * @return array
     */
    public static function asJson($data = [],$code = 200,$msg = 'ok')
    {
        return json_encode(['data' => $data,'code' => $code,'msg' => $msg]);
    }

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
            if($data['value']==1){
                $where="1=1";
            }

        }elseif($data['type']==2){

            $date=explode('-',$data['value']);
            $star_time = date("Y-m-d H:i:s", mktime(0, 0, 0, (float)$date[1],(float)$date[2],(float)$date[0]));
            $end_time = date("Y-m-d H:i:s", mktime(0, 0, 0, (float)$date[1], (float)$date[2]+1, (float)$date[0]));
            $time["star"] = strtotime($star_time);
            $time["end"] = strtotime($end_time);
            $where="a.match_starat BETWEEN ".$time["star"] ." AND ".$time["end"];
        }elseif($data['type']==3){
            $where=['a.province'=>$data['value']];
        }else{
            $where="1=1";
        }
        //order_type  1  时间排序 2 人气  默认时间
        if(empty($data['order_type'])){
            $data['order_type']=0;
        }

        if($data['order_type']==2){
            $order="b.follow_num desc";
        }elseif($data['order_type']==3){
            $order="a.enroll_time";
        }else{
            $order="a.match_starat";
        }

        $match=Db::name('match')
            ->field('a.id,a.match_name,a.province,a.city,a.match_starat,a.logo,b.follow_num')
            ->alias('a')
            ->join('match_follow b','a.id=b.match_id')
            ->where($where)
            ->order($order)
            ->page($data['page'],10)
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
        $id=input("post.id");
        $match=Db::name('match')->field("enroll_time,match_starat,match_name,province,city,area,logo")->where(['id'=>$id])->find();
        $match['match_starat']=date('Y-m-d',$match['match_starat']);
        $match["address"]=$match["province"].$match["city"].$match["area"];
        unset($match["province"]);
        unset($match["city"]);
        unset($match["area"]);
        return self::asJson($match);

    }

    /**
     * 赛事详情下部分
     */
    public function detailsContent(){
        $data=input("post.");
        if($data['type']==1){
            $match=Db::name('match')->field("content")->where(['id'=>$data['id']])->find();
        }elseif ($data['type']==2){
            $match['match_red']=Db::name('match_red')->field("red_id,spec_name,price")->where(['match_id'=>$data['id']])->select();
            $match['content']=Db::name('match')->where(['id'=>$data['id']])->value('content');
        }elseif ($data['type']==3){
            $match['meal']=Db::name('match_meal')->field("meal_id,title,price,logo,content")->where(['match_id'=>$data['id']])->select();
        }elseif ($data['type']==4){
            $match['match_goods']=Db::name('match_goods')->field("service_id,goods_name,price,logo,market_price")->where(['match_id'=>$data['id']])->select();
        }

        return self::asJson($match);

    }

    /**
     * 获取月份下面的场次
     */
    public function month(){
        $data = input("post.");
        $data['month']=str_replace('年','-',$data['month']);
        $data['month']=str_replace('月','',$data['month']);
        $month_start = strtotime($data['month']);//指定月份月初时间戳
        $month_end = mktime(23, 59, 59, date('m', strtotime($data['month']))+1, 00);

        $match = Db::name('match')->where("match_starat","BETWEEN",[$month_start,$month_end])->order("match_starat")->select();
        $array=[];
        foreach ($match as $k=>$v){
            $date['start'] = strtotime( date('Y-m-d 00:00:00',$v['match_starat']));
            $date['stop']  = strtotime(date('Y-m-d 23:59:59',$v['match_starat']));
            $array[ date('Y-m-d',$v['match_starat'])]['num']= Db::name('match')->where('match_starat','>',$date['start'])->where('match_starat','<',$date['stop'])->count();  //赛事表
            $array[ date('Y-m-d',$v['match_starat'])]['date']=(int)date('d',$v['match_starat']);
        }
        $array=array_values($array);
        return self::asJson($array);

    }

    /**
     * 赛事搜索
     * @return array
     */
    public function search()
    {
        $data = input("post");

        $match = Db::name("match")->field("id,match_name")->whereLike("match_name","%".$data['keyword']."%")->select();

        return self::asJson($match);
    }

    /**
     * 确认报名订单
     * @return array
     */
    public function sign()
    {
        $data = input("post");
        $str = "match-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $match_name = Db::name("match")->where(['id'=>$data['match_id']])->value("match_name");
        $pricee = Db::name("match")->where(['match_id'=>$data['match_id'],'red_id'=>$data['red_id']])->value("price");

        if(empty($data['meal_id'])){
            $data['meal_id']=0;
        }


        $order_price=$pricee;

        $add=[
            "uid"=>$data["uid"],
            "match_id"=>$data["match_id"],
            "order_price"=>$order_price,
            "match_order_sn"=>$str,
            "add_time"=>time(),
            "match_name"=>$match_name,
            "remarks"=>$data["remarks"],
            "red_id"=>$data["uid"],
            "meal_id"=>$data["meal_id"],
            "service_id"=>$data["service_id"],
        ];
        $match = Db::name("match_order")->insert($add);
        return self::asJson($match);
    }



}