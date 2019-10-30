<?php
namespace app\ebapi\controller;

use app\admin\model\system\SystemAttachment;
use app\core\model\routine\RoutineCode;
use app\core\util\SystemConfigService;
use app\ebapi\model\match\MatchCombination;
use app\ebapi\model\store\StoreOrder;
use app\ebapi\model\store\StoreProductRelation;
use app\ebapi\model\store\StoreProductReply;
use app\ebapi\model\user\WechatUser;
use app\core\util\GroupDataService;
use service\JsonService;
use service\UploadService;
use service\UtilService;
use think\Db;


/**
 * TODO 小程序拼团产品和拼团其他api接口
 * Class PinkApi
 * @package app\ebapi\controller
 */
class MatchPink extends AuthController
{
    /**
     * TODO 获取赛事拼团列表
     */
    public function get_combination_list(){
        $data = input("post.");
        $store_combination["combination"] = Db::name("match_combination")
            ->field("id,image,title,price,start_time")
            ->where(['is_show'=>1,"is_del"=>0])
            ->page($data['page'],10)
            ->select();
        foreach ($store_combination["combination"] as $k=>$v){
            $store_combination["combination"][$k]['start_time'] = date("Y-m-d",$v['start_time']);
        }
        $store_combination["banner"]=[
          "http://chinb.org/public/system/images/20191012003902.png"
        ];
        return JsonService::successful('ok',$store_combination);
    }


    /**
     * TODO 获取赛事拼团产品详情
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function combination_detail(){

        list($id) = UtilService::postMore([['id',0]],null,true);
        if(!$id) return JsonService::fail('拼团不存在或已下架');
        $data = Db::name("match_combination")
            ->field("id,image,title,stop_time,product_id,match_price,price")
            ->where(["id"=>$id])
            ->find();
        $match = Db::name("match")->where(["id"=>$data["product_id"]])->find();

        $data['content'] = $match['content'];
        $data['address'] = $match['province'].$match['city'].$match['area'];
        $data['match_starat'] = date("Y-m-d",$match['match_starat']);

        return JsonService::successful('ok',$data);
    }

    /**
     * 获取赛事拼团用户
     */
    public function userPink()
    {
        $data = input("post.");
        $match_pink["userPink"] = Db::name("match_pink")->field("uid,people")->where(["cid"=>$data["id"],"k_id"=>0])->page($data["page"],$data["size"])->select();
        foreach ($match_pink["userPink"] as $k=>$v){
            $user = Db::name("user")->where(["uid"=>$v["uid"]])->find();
            $match_pink["userPink"][$k]["nickname"] = $user["nickname"];
            $match_pink["userPink"][$k]["avatar"] = $user["avatar"];
            $count = Db::name("match_pink")->where(["cid"=>$data["id"],"k_id"=>$v["uid"]])->page($data["page"],$data["size"])->count();
            $match_pink["userPink"][$k]["num"] = $v["people"]-$count;
        }

        $match_pink["pinkNum"] = Db::name("match_pink")->where(["cid"=>$data["id"],"k_id"=>0])->count();
        return JsonService::successful('ok',$match_pink);
    }

    /**
     * 生产拼团订单
     */
    public function pinkOrder()
    {
        $data = input("post.");
        $combination = Db::name("match_combination")->field("id,product_id,people,price,stop_time")->where(["product_id"=>$data["id"]])->find();
        $str = "match-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        if($data["type"]==1){
            $add=[
                "uid"=>$this->uid,
                "match_id"=>$combination["product_id"],
                "order_price"=>$data["amount"],
                "match_order_sn"=>$str,
                "add_time"=>time(),
                "match_name"=>Db::name("match")->where(["id"=>$combination["product_id"]])->value("match_name"),
            ];
        }elseif($data["type"]==2){
            $count =  Db::name("match_pink")->where(["uid"=>$this->uid,"cid"=>$combination["product_id"],"status"=>1])->count();
            if(!empty($count)){
                return JsonService::fail('该赛事您已经在拼团了');
            }
            if(empty($data["tid"])){
                $data["tid"]= 0;
            }
            $add=[
                "uid"=>$this->uid,
                "match_id"=>$combination["product_id"],
                "order_price"=>$data["amount"],
                "match_order_sn"=>$str,
                "add_time"=>time(),
                "type"=>3,
                "k_id"=>$data["tid"],
            ];
        }
        Db::name("match_order")->insert($add);
        $pay = new AuthApi();
        $pay->pay_order($str);

    }

    /**
     * 开团页面
     * @param int $id
     * @return mixed
     */
    public function get_pink($id = 0){
        $match_pink = Db::name("match_pink")->where(["id"=>$id])->find();

        if($match_pink["k_id"]==0){
            $count = Db::name("match_pink")
                ->alias("a")
                ->join("user b","a.uid=b.uid")
                ->field("a.uid,b.avatar")
                ->where(["a.cid"=>$match_pink["cid"],"a.k_id"=>$match_pink["uid"]])
                ->whereOr(["a.cid"=>$match_pink["cid"],"a.uid"=>$match_pink["uid"]])
                ->count();

        }else{
            $count = Db::name("match_pink")
                ->alias("a")
                ->join("user b","a.uid=b.uid")
                ->field("a.uid,b.avatar")
                ->where(["a.cid"=>$match_pink["cid"],"a.k_id"=>$match_pink["k_id"]])
                ->whereOr(["a.cid"=>$match_pink["cid"],"a.uid"=>$match_pink["k_id"]])
                ->count();
        }


        if($match_pink["stop_time"]<time() &&$match_pink["status"]==1){

            Db::name("match_pink")
                ->where(["cid"=>$match_pink["cid"],"k_id"=>$match_pink["k_id"]])
                ->whereOr(["cid"=>$match_pink["cid"],"uid"=>$match_pink["k_id"]])
                ->update(["status"=>3]);
            $match_pink["status"] = 3 ;
        }elseif($match_pink["stop_time"]>time() &&$match_pink["status"]==1){
            Db::name("match_pink")
                ->where(["cid"=>$match_pink["cid"],"k_id"=>$match_pink["k_id"]])
                ->whereOr(["cid"=>$match_pink["cid"],"uid"=>$match_pink["k_id"]])
                ->update(["status"=>2]);
            $match_pink["status"] = 2 ;
        }


        $match_combination = Db::name("match_combination")->where(["id"=>$match_pink["cid"]])->find();
        $data["images"] = $match_combination["image"];
        $data["title"] = $match_combination["title"];
        $data["price"] = $match_pink["price"];
        $data["people"] = $match_pink["people"];
        $data["peo_ple"] = $match_pink["people"]-$count;
        $data["stop_time"] = date("Y-m-d H:i:s",$match_combination["stop_time"]);
        if($match_pink["status"]==1){
            $data["status_name"] = "拼团中";
        }elseif($match_pink["status"]==2){
            $data["status_name"] = "拼团成功";
        }elseif($match_pink["status"]==3){
            $data["status_name"] = "拼团失败";
        }
        if($match_pink["k_id"]==0){

            $data["users"] = Db::name("match_pink")
                ->alias("a")
                ->join("user b","a.uid=b.uid")
                ->field("a.uid,b.avatar")
                ->where(["a.cid"=>$match_pink["cid"],"a.k_id"=>$match_pink["uid"]])
                ->whereOr(["a.cid"=>$match_pink["cid"],"a.uid"=>$match_pink["uid"]])
                ->select();

        }else{
            $data["users"] = Db::name("match_pink")
                ->alias("a")
                ->join("user b","a.uid=b.uid")
                ->field("a.uid,b.avatar")
                ->where(["a.cid"=>$match_pink["cid"],"a.k_id"=>$match_pink["k_id"]])
                ->whereOr(["a.cid"=>$match_pink["cid"],"a.uid"=>$match_pink["k_id"]])
                ->select();

        }

        return JsonService::successful('ok',$data);
    }

    /**
     * 获取今天正在拼团的人的头像和名称
     * @return \think\response\Json
     */
    public function get_pink_second_one()
    {
        return JsonService::successful(\app\ebapi\model\match\MatchPink::getPinkSecondOne());
    }

    /**
     * 取消开团
     * @param int $pink_id 团长id
     * */
    public function remove_pink($pink_id=0,$cid=0,$formId='')
    {
        if(!$pink_id || !$cid) return JsonService::fail('缺少参数');
        $res=\app\ebapi\model\match\MatchPink::removePink($this->uid,$cid,$pink_id,$formId);
        if($res)
            return JsonService::successful('取消成功');
        else{
            $error=\app\ebapi\model\match\MatchPink::getErrorInfo();
            if(is_array($error))
                return JsonService::status($error['status'],$error['msg']);
            else
                return JsonService::fail($error);
        }
    }

    /**
     * TODO 生成海报
     */
    public function pink_share_poster()
    {
        list($pinkId) = UtilService::postMore([['id',0]],null,true);
        $pinkInfo = \app\ebapi\model\match\MatchPink::getPinkUserOne($pinkId);
        $MatchCombinationInfo = MatchCombination::getCombinationOne($pinkInfo['cid']);
        $data['title'] = $MatchCombinationInfo['title'];
        if(stripos($MatchCombinationInfo['image'], '/public/uploads/')) $data['image'] = ROOT_PATH.substr($MatchCombinationInfo['image'],stripos($MatchCombinationInfo['image'], '/public/uploads/'),strlen($MatchCombinationInfo['image']));
        $data['price'] = $pinkInfo['total_price'];
        $data['label'] = $pinkInfo['people'].'人团';
        if($pinkInfo['k_id']) $pinkAll = \app\ebapi\model\match\MatchPink::getPinkMember($pinkInfo['k_id']);
        else $pinkAll = \app\ebapi\model\match\MatchPink::getPinkMember($pinkInfo['id']);
        $count = count($pinkAll)+1;
        $data['msg'] = '原价￥'.$MatchCombinationInfo['match_price'].' 还差'.(int)bcsub((int)$pinkInfo['people'],$count,0).'人拼团成功';
        try{
            $name = $pinkId.'_'.$this->userInfo['uid'].'_'.$this->userInfo['is_promoter'].'_pink_share.jpg';
            $imageInfo = SystemAttachment::getInfo($name,'name');

            $siteUrl = SystemConfigService::get('site_url').DS;
            if(!$imageInfo){
                $valueData = 'id='.$pinkId;
                if($this->userInfo['is_promoter'] || SystemConfigService::get('store_brokerage_statu')==2) $valueData.='&pid='.$this->uid;
                $res = RoutineCode::getPageCode('pages/activity/goods_combination_status/index',$valueData,280);
                if(!$res) return JsonService::fail('二维码生成失败');
                $imageInfo = UploadService::imageStream($name,$res,'routine/activity/pink/code');
                if(!is_array($imageInfo)) return JsonService::fail($imageInfo);
                if($imageInfo['image_type'] == 1) $remoteImage = UtilService::remoteImage($siteUrl.$imageInfo['dir']);
                else $remoteImage = UtilService::remoteImage($imageInfo['dir']);
                if(!$remoteImage['status']) return JsonService::fail($remoteImage['msg']);
                SystemAttachment::attachmentAdd($imageInfo['name'],$imageInfo['size'],$imageInfo['type'],$imageInfo['dir'],$imageInfo['thumb_path'],1,$imageInfo['image_type'],$imageInfo['time']);
                $url = $imageInfo['dir'];
            }else $url = $imageInfo['att_dir'];
            if($imageInfo['image_type'] == 1) $data['url'] = ROOT_PATH.$url;
            else $data['url'] = $url;
            $posterImage = UtilService::setShareMarketingPoster($data,'routine/activity/pink/poster');
            if(!is_array($posterImage)) return JsonService::fail('海报生成失败');
            SystemAttachment::attachmentAdd($posterImage['name'],$posterImage['size'],$posterImage['type'],$posterImage['dir'],$posterImage['thumb_path'],1,$posterImage['image_type'],$posterImage['time']);
            if($posterImage['image_type'] == 1) $posterImage['dir'] = $siteUrl.$posterImage['dir'];
            return JsonService::successful('ok',$posterImage['dir']);
        }catch (\Exception $e){
            return JsonService::fail('系统错误：生成图片失败',['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }

    }

    /**
     * 获取拼单订单列表
     */
    public function pinkList()
    {
        $data = input("post.");

       $match_pink=Db::name("match_pink")
            ->field("id,cid,add_time,total_price,stop_time,status,k_id")
            ->where(["uid"=>$this->uid])
            ->page($data["page"],$data["size"])
            ->select();
       foreach($match_pink as $k=>$v){
           $match_pink[$k]["add_time"] = date("Y-m-d",$v["add_time"]);
           $match_pink[$k]["stop_time"] = date("Y-m-d H:i:s",$v["stop_time"]);
           $match_pink[$k]["images"] = Db::name("match_combination")->where(["product_id"=>$v["cid"]])->value("image");
           $match_pink[$k]["title"] = Db::name("match_combination")->where(["product_id"=>$v["cid"]])->value("title");
           if($v["status"]==1){
               $match_pink[$k]["status_name"] = "拼团中";
           }elseif($v["status"]==2){
               $match_pink[$k]["status_name"] = "拼团成功";
           }elseif($v["status"]==3){
               $match_pink[$k]["status_name"] = "拼团失败";
           }
       }

        return JsonService::successful('ok',$match_pink);

    }


}