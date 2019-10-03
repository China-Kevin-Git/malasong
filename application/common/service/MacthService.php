<?php

namespace app\common\service;
use app\admin\model\match\comment;
use app\admin\model\match\follow;
use app\admin\model\match\macth;
use think\Db;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: SAMSUNG
 * Date: 2019/10/1
 * Time: 20:26
 */

class MacthService extends Service
{
    /**
     * 编辑赛事内容
     * @param $data
     * @return array
     */
    public static function editMacth($data)
    {
        if(
            (!isset($data['match_name']) || !$data['match_name']) ||
            (!isset($data['province']) || !$data['province']) ||
            (!isset($data['city']) || !$data['city']) ||
            (!isset($data['area']) || !$data['area'])
        ){
            return self::set_err([],'1','参数错误，请检查填写数据');
        }
        try{
            if(isset($data['id']) && $data['id']){
                $macth = macth::get($data['id']);  //赛事表
                $macth->update_at = time();

                $follow = follow::get(["match_id" => $data['id']]); //关注表
                $follow->update_at = time();

                unset($data['id']);
            }else{
                $macth = new macth();
                $follow = new follow();
            }
            $macth->match = json_encode($data);  //赛事表 数据存储
            $macth->match_starat = $data['match_starat']??"";  //赛事开始时间
            $macth->match_stop = $data['match_stop']??"";  //赛事结束时间
            if($macth->save() === false){
                return self::set_err([],'1','赛事库保存失败');
            }
            //关注表数据存储
            $follow->match_name = $data['match_name'];
            $follow->province = $data['province'];
            $follow->city = $data['city'];
            $follow->area = $data['area'];
            $follow->match_id = $macth->id;
            $follow->save();
        }catch (Exception $e){
            return self::set_err([],'1',$e->getFile().$e->getData().$e->getLine());
        }
        return self::set_err($macth->toArray());
    }

    /**
     * 获取赛事内容
     * @param $id
     * @return array
     */
    public static function queryMatch($id)
    {
        if(!$id){
            return self::set_err([],'1','参数错误，请检查填写数据');
        }
        $macth = macth::get($id)->toArray();  //赛事表
        if(!$macth){
            return self::set_err([],'1','数据不存在');
        }
        $data = json_decode($macth['match'],true);
        $data['match_starat'] = date('Y-m-d',$macth['match_starat']);
        $data['match_stop'] = date('Y-m-d',$macth['match_stop']);
        return self::set_err($data);
    }

    /**
     * 统计赛事
     * @param $id
     * @return array
     */
    public static function countMatch($where)
    {

        $macth = macth::where('match_starat','>',$where['start'])->where('match_starat','<',$where['stop'])->count();  //赛事表

        return self::set_err(['count' => $macth],true);
    }

    /**
     * 获取赛事内容
     * @param $id
     * @return array
     */
    public static function queryFollow($data)
    {
        if(isset($data['city']) && $data['city']){  //本地赛事
            $follow = Db::table('eb_match_follow')->where(['city' => $data['city']])->page(1,3)->order('create_at desc')->select();
        }else if(isset($data['popular']) && $data['popular']){ //最热赛事
            $follow = Db::table('eb_match_follow')->page(1,3)->order('follow_num desc')->select();
        }else if(isset($data['page']) && $data['page']){ //更多
            $follow = Db::table('eb_match_follow')->page($data['page'],5)->order('create_at desc')->select();
        }else{ //最新
            $follow = Db::table('eb_match_follow')->page(1,3)->order('create_at desc')->select();
        }
        foreach ($follow as $key => $value){
            $follow[$key]['create_at'] = date('Y-m-d',$value['create_at']);
            $follow[$key]['update_at'] = date('Y-m-d',$value['update_at']);
        }
        return self::set_err($follow);
    }

    /**
     * 获取文章列表
     * @return array
     */
    public static function queryArticle()
    {
        $res = Db::table('eb_article')
            ->where(['status' => 1,'hide' => 0])
//            ->where('hide',0)
            ->page(1,3)
            ->order('add_time desc')
//            ->select(['id','cid','title','author','image_input','synopsis','url','add_time']);
            ->select();
        return  self::set_err($res);
    }

    /**
     * 创建赛事评论
     * @param $data
     * @return array
     */
    public static function addComment($data)
    {
        if(
           (!isset($data['match_id']) || !$data['match_id']) ||
           (!isset($data['user_name']) || !$data['user_name'])
        ){
            return self::set_err([],'1','缺少相关参数数据');
        }
        $comment = new comment();
        $comment->match_id = $data['match_id'];
        $comment->user_id = $data['user_id']??"0";
        $comment->user_name = $data['user_name'];
        $comment->comment = $data['comment'];
       if( $comment->save() === false){
           return self::set_err([],'1','评论发布失败');
       }
       return self::set_err();
    }

    /**
     * 获取评论列表
     * @param $data
     * @return array
     */
    public static function queryComment($data)
    {
        if(isset($data['match_id']) && $data['match_id']){ //获取赛事评论列表
           $where = ['match_id' => $data['match_id']];
        }else if(isset($data['user_id']) && $data['user_id']){ //获取自己所有评论
            $where = ['user_id' => $data['user_id']];
        }else{
            return self::set_err([],'1','请求数据失败');
        }
        $res = Db::table('eb_comment')
            ->where($where)
            ->page($data['page']??1,$data['size']??10)
            ->order('create_at desc')
            ->select();
        foreach ($res as $key => $value){
            $res[$key]['create_at'] = date('Y-m-d H:i:s',$value['create_at']);
            $res[$key]['update_at'] = date('Y-m-d H:i:s',$value['update_at']);
        }
        return self::set_err($res);
    }

    /**
     * 删除评论
     * @param $data
     * @return array
     */
    public static function deleteComment($data)
    {
        if(!isset($data['id']) || !$data['id']){
            return self::set_err([],'1','参数错误');
        }
        comment::where(['id' => $data['id']])->delete();
        return self::set_err();
    }
}