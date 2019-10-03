<?php

namespace app\admin\controller;

use app\common\service\MacthService;
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
}
