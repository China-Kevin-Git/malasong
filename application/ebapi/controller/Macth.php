<?php

namespace app\ebapi\controller;

use app\common\service\MacthService;
use chuanglan\demo\API\ChuanglanSmsHelper\ChuanglanSmsApi;
use think\Db;
use think\Request;

/**
 * 赛事接口
 * Class AgentManage
 * @package app\admin\controller\agent
 */
class Macth extends AuthController
{

    /**
     * 统一返回格式
     * @param array $data
     * @param string $code
     * @param string $msg
     * @return array
     */
    public static function asJson($data = [], $code = 200, $msg = 'ok')
    {
        return json_encode(['data' => $data, 'code' => $code, 'msg' => $msg]);
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
        $date['start'] = strtotime(date('Y-m-d 00:00:00', $data['date']));
        $date['stop'] = strtotime(date('Y-m-d 23:59:59', $data['date']));

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
        $data = input('post.');

        //type:1 分类 2.日期 3.地区
        if (empty($data['type'])) {
            $data['type'] = 0;
        }
        if ($data['type'] == 1) {
            $where = " match_catrgory_id=" . $data['value'];
            if ($data['value'] == 1) {
                $where = "1=1";
            }
        } elseif ($data['type'] == 2) {
            $date = explode('/', $data['value']);

            $star_time = date("Y-m-d H:i:s", mktime(0, 0, 0, (float)$date[1], (float)$date[2], (float)$date[0]));
            $end_time = date("Y-m-d H:i:s", mktime(0, 0, 0, (float)$date[1], (float)$date[2] + 1, (float)$date[0]));
            $time["star"] = strtotime($star_time);
            $time["end"] = strtotime($end_time);
            $where = "match_starat BETWEEN " . $time["star"] . " AND " . $time["end"];
        } elseif ($data['type'] == 3) {
            $where = ['province' => $data['value']];
        } else {
            $where = "1=1";
        }
        //order_type  1  时间排序 2 人气  默认时间
        if (empty($data['order_type'])) {
            $data['order_type'] = 0;
        }
        if ($data['order_type'] == 2) {
            $order = "num desc";
        } elseif ($data['order_type'] == 3) {
            $order = "enroll_time";
        } else {
            $where = " and enroll_time >" . time() . " and croll_time <" . time();
        }

        $match = Db::name('match')
            ->field('id,match_name,province,city,match_starat,logo,num as follow_num')
            ->where($where)
            ->order($order)
            ->page($data['page'], 10)
            ->select();
        foreach ($match as $k => $v) {
            $match[$k]['address'] = $v['province'] . $v['city'];
            $match[$k]['match_starat'] = date('Y-m-d', $v['match_starat']);
            unset($match[$k]['province']);
            unset($match[$k]['city']);
        }
        return self::asJson($match);

    }

    /**
     * 赛事分类
     *
     */
    public function macthCatgory()
    {
        $match = Db::name('match_catrgory')
            ->select();
        return self::asJson($match);

    }

    /**
     * 赛事详情
     */
    public function details()
    {
        $id = input("post.id");
        $match = Db::name('match')->field("enroll_time,match_starat,match_name,province,city,area,logo,croll_time,address")->where(['id' => $id])->find();
        $match_attention = Db::name("match_attention")->where("match_id", $id)->count();
        if (empty($match_attention)) {
            $match['is_new'] = 0;
        } else {
            $match['is_new'] = 1;
        }

        $match['match_starat'] = date('Y-m-d', $match['match_starat']);
        $match['enroll_time'] = $match['enroll_time'] * 1000;
        $match['croll_time'] = $match['croll_time'] * 1000;
        $match["address"] = $match["province"] . $match["city"] . $match["area"] . $match["address"];
        unset($match["province"]);
        unset($match["city"]);
        unset($match["area"]);
        return self::asJson($match);

    }

    /**
     * 赛事详情下部分
     */
    public function detailsContent()
    {
        $data = input("post.");
        if ($data['type'] == 1) {
            $match = Db::name('match')->field("content")->where(['id' => $data['id']])->find();
        } elseif ($data['type'] == 2) {
            $match['match_red'] = Db::name('match_red')->field("red_id,spec_name,price")->where(['match_id' => $data['id']])->select();
            $match['content'] = Db::name('match')->where(['id' => $data['id']])->value('content');
        } elseif ($data['type'] == 3) {
            $match['meal'] = Db::name('match_meal')->field("meal_id,title,price,logo,content")->where(['match_id' => $data['id']])->select();
        } elseif ($data['type'] == 4) {
            $match['match_goods'] = Db::name('match_goods')->field("service_id,goods_name,price,logo,market_price")->where(['match_id' => $data['id']])->select();
        }

        return self::asJson($match);

    }

    /**
     * 获取月份下面的场次
     */
    public function month()
    {
        $data = input("post.");
        $data['month'] = str_replace('年', '-', $data['month']);
        $data['month'] = str_replace('月', '', $data['month']);
        $month_start = strtotime($data['month']);//指定月份月初时间戳
        $month_end = mktime(23, 59, 59, date('m', strtotime($data['month'])) + 1, 00);

        $match = Db::name('match')->where("match_starat", "BETWEEN", [$month_start, $month_end])->order("match_starat")->select();
        $array = [];
        foreach ($match as $k => $v) {
            $date['start'] = strtotime(date('Y-m-d 00:00:00', $v['match_starat']));
            $date['stop'] = strtotime(date('Y-m-d 23:59:59', $v['match_starat']));
            $array[date('Y-m-d', $v['match_starat'])]['num'] = Db::name('match')->where('match_starat', '>', $date['start'])->where('match_starat', '<', $date['stop'])->count();  //赛事表
            $array[date('Y-m-d', $v['match_starat'])]['date'] = (int)date('d', $v['match_starat']);
        }
        $array = array_values($array);
        return self::asJson($array);

    }

    /**
     * 赛事搜索
     * @return array
     */
    public function search()
    {
        $data = input("post.");

        $match = Db::name("match")->field("id,match_name")->whereLike("match_name", "%" . $data['keyword'] . "%")->select();

        return self::asJson($match);
    }

    /**
     * 确认报名订单
     * @return array
     */
    public function sign()
    {
        $data = input("post.");
        $time = Db::name("match")->where(['id' => $data['match_id']])->find();
        if ($time["croll_time"] > time()) {
            return self::asJson([], 400, '赛事还没有开始报名');
        }
        if ($time["enroll_time"] < time()) {
            return self::asJson([], 400, '赛事已经结束报名');
        }

        $str = "match-" . date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $match_name = Db::name("match")->where(['id' => $data['match_id']])->value("match_name");
        $pricee = Db::name("match_red")->where(['red_id' => $data['red_id']])->value("price");

        //套餐
        if (empty($data['meal_id'])) {
            $data['meal_id'] = 0;
            $meal_price = 0;
        } else {
            $meal_price = Db::name("match_meal")->where(['meal_id' => $data['meal_id']])->value("price");
        }

        //可选服务
        $match_goods_price = 0;
        if (empty($data['service_id'])) {
            $data['service_id'] = 0;
        } else {
            $num = 0;
            $cart_id = [];
            foreach ($data['service_id'] as $k => $v) {
                $match_goods = Db::name("match_goods")->where(['service_id' => $v["service_id"]])->value("price");
                $match_goods_price += $match_goods * $v["num"];
                $num += $v["num"];
                $cart_id[$k] = $v["service_id"];
                Db::name("match_order_goods")->insert(['match_order_sn' => $str, 'num' => $v["num"], 'price' => $match_goods * $v["num"], 'add_time' => time(), 'service_id' => $v["service_id"]]);
                $json[$k] = [
                    "id" => $v["service_id"],
                    "uid" => $this->uid,
                    "type" => "product",
                    "product_id" => $v["service_id"],
                    "cart_num" => $v["num"],
                    "add_time" => time(),
                    "productInfo" => [
                        "image" => $v["logo"],
                        "price" => $v["price"],
                        "store_name" => $v["goods_name"],
                        "unit_name" => "件",
                    ],
                    "truePrice" => $v["price"],
                ];
            }
            $user = Db::name('user')->where(['uid' => $this->uid])->find();
            $user_address = Db::name('user_address')->where(['uid' => $this->uid])->find();
            $arrays = [
                "order_id" => $str,
                "uid" => $this->uid,
                "real_name" => $user["nickname"],
                "user_phone" => $user["phone"],
                "user_address" => $user_address["detail"],
                "cart_id" => json_encode($cart_id),
                "total_num" => $num,
                "total_price" => $match_goods_price,
                "pay_price" => $match_goods_price,
                "pay_type" => "weixin",
                "add_time" => time(),
                "unique" => md5(rand(1000000, 9999999)),
                "is_channel" => 1,
            ];
            Db::name("store_order")->insert($arrays);
            $store_order = Db::name("store_order")->getLastInsID();
            foreach ($json as $k => $v) {
                $store_order_cart_info = [
                    "oid" => $store_order,
                    "cart_id" => $v["id"],
                    "product_id" => $v["id"],
                    "cart_info" => json_encode($json[$k]),
                    "unique" => md5(rand(1000000, 9999999)),
                ];;
                Db::name("store_order_cart_info")->insert($store_order_cart_info);
            }


        }
        $order_price = $pricee + $meal_price + $match_goods_price;
        $order_price = round($order_price, 2);

        $add = [
            "uid" => $this->uid,
            "match_id" => $data["match_id"],
            "order_price" => $order_price,
            "match_order_sn" => $str,
            "add_time" => time(),
            "match_name" => $match_name,
            "remarks" => $data["remarks"],
            "red_id" => $data["red_id"],
            "meal_id" => $data["meal_id"],
            "service_id" => json_encode($data["service_id"]),
            "address_id" => $data["address_id"],
        ];
        Db::name("match_order")->insert($add);
        $array["match_order_id"] = Db::name("match_order")->getLastInsID();
        $array["match_order_sn"] = $str;
        return self::asJson($array);
    }

    /**
     * 完善资料
     */
    public function means()
    {

        $data = input("post.");
        if (empty($data['match_order_id']) && empty($data['mean_id'])) {
            return self::asJson([], 400, "参数错误");
        }
        if (stripos($data['match_order_id'], 'match-') !== false) {
            $data['match_order_id'] = Db::name("match_order")->where(["match_order_sn", $data['match_order_id']])->value("match_order_id");
        }
        $mean = Db::name("match_mean")->where(["mean_id" => $data['mean_id']])->find();
        $mean["match_order_id"] = $data['match_order_id'];
        Db::name("match_means")->insert($mean);
        Db::name("match_order")->where(['match_order_id' => $data['match_order_id']])->update(["status" => 3]);
        return self::asJson();
    }

    /**
     * 我的完善资料
     */
    public function mean()
    {
        $data = input("post.");
        //正则表达式
        $mobiles = preg_match_all("/^1[345789]\d{9}$/", $data["mobile"]);
        if ($mobiles == 0) {
            return self::asJson([], 400, "请输入手机格式");
        }
        $mobiles = preg_match_all("/^1[345789]\d{9}$/", $data["emergency_mobile"]);
        if ($mobiles == 0) {
            return self::asJson([], 400, "请输入手机格式");
        }
        if ($data["emergency_mobile"] == $data["mobile"]) {
            return self::asJson([], 400, "紧急联系人电话不能与联系人电话一致");
        }
        if (!empty($data["ccie"])) {
            $data["ccie"] = json_encode($data["ccie"]);
        }
        $data["user_id"] = $this->uid;
        Db::name("match_mean")->insert($data);
        return self::asJson();
    }

    /**
     * 我的完善资料列表
     */
    public function meanList()
    {
        $data = input("post.");
        $match_mean = Db::name("match_mean")
            ->field("mean_id,name,mobile,nationality,sex")
            ->where(["user_id"=>$this->uid])
            ->page($data["page"],10)
            ->order("mean_id desc")
            ->select();
        return self::asJson($match_mean);
    }

    /**
     * 我的完善资料编辑
     */
    public function meanEdit()
    {
        $data = input("post.");
        //正则表达式
        $mobiles = preg_match_all("/^1[345789]\d{9}$/", $data["mobile"]);
        if ($mobiles == 0) {
            return self::asJson([], 400, "请输入手机格式");
        }
        $mobiles = preg_match_all("/^1[345789]\d{9}$/", $data["emergency_mobile"]);
        if ($mobiles == 0) {
            return self::asJson([], 400, "请输入手机格式");
        }
        if ($data["emergency_mobile"] == $data["mobile"]) {
            return self::asJson([], 400, "紧急联系人电话不能与联系人电话一致");
        }
        if (!empty($data["ccie"])) {
            $data["ccie"] = json_encode($data["ccie"]);
        }
        $data["user_id"] = $this->uid;
        Db::name("match_mean")->where(["mean_id"=>$data["mean_id"]])->update($data);
        return self::asJson();
    }

    /**
     * 我的完善资料编辑回显
     */
    public function meanEcho()
    {
        $data = input("post.");

        $match_mean = Db::name("match_mean")->where(["mean_id"=>$data["mean_id"]])->find();
        $match_mean["ccie"] = json_decode($match_mean["ccie"],true);
        return self::asJson($match_mean);
    }

    /**
     * 我的完善资料删除
     */
    public function meanDelete()
    {
        $data = input("post.");
         Db::name("match_mean")->where(["mean_id"=>$data["mean_id"]])->delete();
        return self::asJson();
    }

    /**
     * 我的参赛
     */
    public function competition()
    {
        $data = input("post.");
        //type 1 全部 2 未支付  3 待完善 4 报名成功

        if ($data['type'] == 1) {
            $where = "is_pay <> 3 ";
        } elseif ($data['type'] == 2) {
            $where = ["is_pay" => 0];
        } elseif ($data['type'] == 3) {
            $where = ["is_pay" => 1, "status" => 1];
        } elseif ($data['type'] == 4) {
            $where = ["is_pay" => 1, "status" => 3];
        }

        $match_order = Db::name("match_order")
            ->field("match_order_id,match_id,order_price,is_pay,status,add_time,match_name,match_order_sn,number")
            ->where("uid", '=', $this->uid)
            ->where($where)
            ->order("add_time desc")
            ->page($data["page"], 10)
            ->select();

        foreach ($match_order as $k => $v) {
            $match_order[$k]['add_time'] = date("Y-m-d", $v['add_time']);
            $match_order[$k]['logo'] = Db::name("match")->where(['id' => $v['match_id']])->value("logo");
            if ($v['is_pay'] == 0) {
                $match_order[$k]['status_name'] = "未支付";
                $match_order[$k]['is_static'] = 1;
            } elseif ($v['is_pay'] == 1 && $v['status'] == 1) {
                $match_order[$k]['status_name'] = "待完善";
                $match_order[$k]['is_static'] = 2;
            } elseif ($v['is_pay'] == 1 && $v['status'] == 3) {
                $match_order[$k]['status_name'] = "报名成功";
                $match_order[$k]['is_static'] = 3;
            } elseif ($v['is_pay'] == 3) {
                $match_order[$k]['status_name'] = "已取消";
                $match_order[$k]['is_static'] = 4;
            }
        }

        return self::asJson($match_order);

    }

    /**
     * 获取订单总价格
     */
    public function orderPrice()
    {
        $data = input("post.");
        $pricee = Db::name("match_red")->where(['red_id' => $data['red_id']])->value("price");

        //套餐
        if (empty($data['meal_id'])) {
            $data['meal_id'] = 0;
            $meal_price = 0;
        } else {
            $meal_price = Db::name("match_meal")->where(['meal_id' => $data['meal_id']])->value("price");
        }

        //可选服务
        $match_goods_price = 0;
        if (empty($data['service_id'])) {
            $data['service_id'] = 0;
        } else {
            foreach ($data['service_id'] as $k => $v) {
                $match_goods = Db::name("match_goods")->where(['service_id' => $v["service_id"]])->value("price");
                $match_goods_price += $match_goods * $v["num"];
            }

        }
        $order_price = $pricee + $meal_price + $match_goods_price;
        $order_price = round($order_price, 2);
        return self::asJson($order_price);

    }

    /**
     * 获取赛是分类
     */
    public function crfy()
    {
        $data = input("post.");

        $match_catrgory_id = Db::name("match")->where(["id" => $data['id']])->value("match_catrgory_id");

        $match_catrgory = Db::name("match_catrgory")->where(['id' => $match_catrgory_id])->find();

        return self::asJson($match_catrgory);
    }

    /**
     * 文章评论
     */
    public function article_commen()
    {
        $data = input("post.");
        if (empty($data["artilce_id"])) {
            return self::asJson([], 400, "确实参数");
        }
        $data["uid"] = $this->uid;
        $data["add_time"] = time();
        Db::name("article_comment")->insert($data);
        return self::asJson();
    }

    /**
     * 文章评论
     */
    public function article_index()
    {
        $data = input("post.");

        $comment = Db::name("article_comment")
            ->field("uid,content,add_time")
            ->where(["artilce_id" => $data["artilce_id"]])
            ->order("add_time desc")
            ->where(["type" => 1])
            ->page($data["page"], $data["size"])
            ->select();
        foreach ($comment as $k => $v) {
            $user = Db::name("user")->where("uid", "=", $v["uid"])->find();
            $comment[$k]["nickname"] = $user["nickname"];
            $comment[$k]["avatar"] = $user["avatar"];
            $comment[$k]["add_time"] = date("Y-m-d", $v["add_time"]);
        }


        return self::asJson($comment);
    }

    /**
     * 赛事取消
     */
    public function cancel()
    {
        $data = input("post.");
        Db::name("match_order")->where(["match_order_sn" => $data["match_order_sn"]])->update(["is_pay" => 3]);
        return self::asJson();
    }

    /**
     * 赛事添加关注
     */
    public function attention()
    {
        $data = input("post.");
        $match_attention = Db::name("match_attention")->where(["uid" => $this->uid, "match_id" => $data["match_id"]])->count();
        if (!empty($match_attention)) {
            return self::asJson([], 400, "请不要重复关注");
        }
        Db::name("match_attention")->insert(["uid" => $this->uid, "match_id" => $data["match_id"]]);
        Db::name("match")->where("id", $data["match_id"])->setInc('num');
        return self::asJson([], 200, "关注成功");
    }

    /**
     * 我的关注赛事
     */
    public function myAttention()
    {
        $match_attention = Db::name("match_attention")->where(["uid" => $this->uid])->select();
        $data = [];
        if (empty($match_attention)) {
            return self::asJson($data, 200, "获取成功");
        }

        foreach ($match_attention as $k => $v) {
            $data[$k] = Db::name("match")->field("id,match_starat,match_name,province,city,area,num,logo")->where("id", $v["match_id"])->find();
            $data[$k]["match_starat"] = date("Y-m-d", $data[$k]["match_starat"]);
        }
        return self::asJson($data, 200, "获取成功");
    }

    /**
     * 新闻添加关注
     */
    public function news()
    {
        $data = input("post.");
        $match_attention = Db::name("news")->where(["uid" => $this->uid, "article_id" => $data["article_id"]])->count();
        if (!empty($match_attention)) {
            return self::asJson([], 400, "请不要重复关注");
        }
        Db::name("news")->insert(["uid" => $this->uid, "article_id" => $data["article_id"]]);
        Db::name("article")->where("id", $data["article_id"])->setInc('num');
        return self::asJson([], 200, "关注成功");
    }

    /**
     * 新闻点赞
     */
    public function zan()
    {
        $data = input("post.");
        Db::name("article")->where("id", $data["article_id"])->setInc('zan_num');
        return self::asJson([], 200, "点赞成功");
    }

    /**
     * 我的关注新闻
     *
     */
    public function myAtt()
    {
        $match_attention = Db::name("news")->where(["uid" => $this->uid])->select();
        $data = [];
        if (empty($match_attention)) {
            return self::asJson($data, 200, "获取成功");
        }
        foreach ($match_attention as $k => $v) {
            $data[$k] = Db::name("article")->field("id,title,image_input,add_time")->where("id", $v["article_id"])->where(["hide"=>0,"status"=>1])->find();
            $data[$k]["add_time"] = date("Y-m-d", $data[$k]["add_time"]);
        }
        return self::asJson($data, 200, "获取成功");
    }

    /**
     * 我的评论
     */
    public function myComment()
    {
        $article_comment = Db::name("article_comment")->where(["uid" => $this->uid])->order("add_time desc")->select();

        foreach ($article_comment as $k => $v) {
            $article_comment[$k]["add_time"] = date("Y-m-d", $v["add_time"]);
            if ($v["type"] == 0) {
                $article_comment[$k]["type_name"] = "审核中";
            } elseif ($v["type"] == 1) {
                $article_comment[$k]["type_name"] = "发布成功";
            } else {
                $article_comment[$k]["type_name"] = "发布失败";
            }
        }
        return self::asJson($article_comment, 200, "获取成功");
    }

    /**
     * 评论我的
     */
    public function CommentMy()
    {
        $article = Db::name("article")->where(["uid" => $this->uid])->column("id");
        $article_comment = Db::name("article_comment")->where("artilce_id", "in", $article)->order("add_time desc")->select();
        foreach ($article_comment as $k => $v) {
            $article_comment[$k]["add_time"] = date("Y-m-d", $v["add_time"]);
        }
        return self::asJson($article_comment, 200, "获取成功");
    }

    /**
     * 图片上传
     */
    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            //获取图片的存放相对路径
            $filePath = 'public' . DS . 'uploads' . DS . $info->getSaveName();
            $getInfo = $info->getInfo();
            return self::asJson($_SERVER['SERVER_NAME'] . "\\" . $filePath, 200, "获取成功");
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    /**
     * 删除评论
     */
    public function delete()
    {
        $data = input("post.");
        Db::name("article_comment")->where(["id" => $data["id"]])->delete();
        return self::asJson([], 200, "删除成功");
    }

    /**
     * 添加资讯
     */
    public function add()
    {
        $data = input("post.");
        Db::name("article")->insert([
            "cid" => $data["cid"],
            "title" => $data["title"],
            "author" => "佚名",
            "status" => 1,
            "image_input" => $data["image_input"],
            "share_title" => $data["title"],
            "uid" => $this->uid,
            "add_time" => time(),
        ]);
        $id = Db::name("article")->getLastInsID();

        Db::name("article_content")->insert(["nid" => $id, "content" => $data["content"]]);
        return self::asJson([], 200, "添加成功");
    }

    /**
     * 用户协议列表
     */
    public function protocol()
    {
        $protocol = Db::name("protocol")->select();
        return self::asJson($protocol, 200, "获取成功");
    }

    /**
     * 用户协议
     */
    public function protocols()
    {
        $data = input("post.");
        $protocol = Db::name("protocol")->where(["id" => $data["id"]])->find();
        return self::asJson($protocol, 200, "获取成功");
    }

    /**
     * 我的资讯
     */
    public function myNew()
    {
        $article = Db::name("article")->field("id,title,image_input,add_time")->where(["uid" => $this->uid])->select();
        foreach ($article as $k => $v) {
            $article[$k]["add_time"] = date("Y-m-d", $v["add_time"]);
        }
        return self::asJson($article, 200, "获取成功");
    }

    /**
     * 提现报名
     */
    public function apply()
    {
        $data = input("post.");
        $moblie = Db::name("user")->where(["uid"=>$this->uid])->value("phone");
        //设置编码格式为utf-8;json格式统一使用utf-8封装
        header(
            "Content-type:text/html; charset=UTF-8"
        );
        //实例化 ChuanglanSmsApi 类
        $clapi  = new ChuanglanSmsApi();
        $match = Db::name("match")->where(["id"=>$data["id"]])->find();
        if($match["enroll_time"]<time()){
            return self::asJson([], 400, "报名已截止");
        }
        if($match["croll_time"]>time()){
            return self::asJson([], 400, "报名未开始");
        }
        $time = ceil(($match["enroll_time"]- time())/(24*3600));

        //设置您要发送的内容：其中“【】”中括号为运营商签名符号，多签名内容前置添加提交
        $result = $clapi->sendSMS($moblie,'【马拉松报名网】您好！开始报名：'.$match["match_name"].'赛事已经开始报名了 ，请前往小程序参与报名。'.$match["match_name"].'赛事距离报名还剩'.$time.'天，请尽快报名');
        return self::asJson([], 200, "提醒成功");

    }

    /**
     * 錢包
     */
    public function qian()
    {
        $data = input("post.");
        //已提现的
        $user["price"] = Db::name("user_bill")->where("uid",$this->uid)->where("type","extract")->where('status',1)->sum("number");

        //可提现的
        $user_money = Db::name("user")->where("uid",$this->uid)->value("now_money");
        $user["price_money"] = $user_money;
        //总收益
        $user["now_money"] =round($user_money + $user["price"],2);
        if($data["type"]==1){
            $user["user_bill"] =  Db::name("user_bill")->where("uid",$this->uid)->where("pm",1)->where("type","extract")->order("add_time desc")->page($data["page"],10)->select();
        }elseif ($data["type"]==2){
            $user["user_bill"] =  Db::name("user_bill")->where("uid",$this->uid)->where("pm",0)->where("type","extract")->order("add_time desc")->page($data["page"],10)->select();
        }else{
            $user["user_bill"] =  Db::name("user_bill")->where("uid",$this->uid)->order("add_time desc")->where("type","extract")->page($data["page"],10)->select();
        }
        foreach ($user["user_bill"] as $k=>$v){
            $user["user_bill"][$k]["add_time"] = date("Y-m-d",$v["add_time"]);
        }
        return self::asJson($user, 200, "获取成功");

    }

    /**
     * 地区二级联动
     */
    public function area()
    {
        $data = input("post.");
        //type :1 国家 2省  3市 4区
        if($data["type"] == 1){
            $country =  Db::name("country")->field("id,country as name")->where(["parentid"=>0])->select();
        }elseif($data["type"] == 2){
            $country =  Db::name("country")->field("id,state as name")->where(["parentid"=>$data["parentid"]])->select();
        }elseif($data["type"] == 3){
            $country =  Db::name("country")->field("id,city as name")->where(["parentid"=>$data["parentid"]])->select();
        }elseif($data["type"] == 4){
            $country =  Db::name("country")->field("id,region as name")->where(["parentid"=>$data["parentid"]])->select();
        }
        return self::asJson($country, 200, "获取成功");
    }


}