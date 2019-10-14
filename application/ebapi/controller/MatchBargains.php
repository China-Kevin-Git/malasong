<?php
namespace app\ebapi\controller;


use app\admin\model\system\SystemAttachment;
use app\core\model\routine\RoutineCode;
use app\core\model\routine\RoutineTemplate;//待完善
use app\core\util\SystemConfigService;
use app\ebapi\model\match\MatchBargain;
use app\ebapi\model\match\MatchBargainUser;
use app\ebapi\model\match\MatchBargainUserHelp;
use app\core\util\GroupDataService;
use service\JsonService;
use service\UploadService;
use service\UtilService;
use think\Db;


/**
 * TODO 小程序砍价活动api接口
 * Class BargainApi
 * @package app\ebapi\controller
 */
class MatchBargains extends AuthController
{

    /**
     * TODO 获取砍价列表参数
     */
    public function get_bargain_config(){
        $info = [
            "http://chinb.org/system/images/20191012003902.png"
        ];
        return JsonService::successful($info);
    }

    /**
     * TODO 获取砍价列表
     */
    public function get_bargain_list()
    {
        $data = UtilService::postMore([['offset',0],['limit',20]]);
        $bargainList = MatchBargain::getList($data['offset'],$data['limit']);
        foreach ($bargainList as $k=>$v){
            $bargainList[$k]["stop_time"] =date("Y-m-d H:i:s",$v["stop_time"]);
        }


        MatchBargainUser::editBargainUserStatus($this->uid);// TODO 判断过期砍价活动
        return JsonService::successful($bargainList);
    }

    /**
     * TODO 砍价详情和当前登录人信息
     * @param int $bargainId  $bargainId 砍价产品
     * @return \think\response\Json
     */
    public function get_bargain(){
        list($bargainId) = UtilService::postMore([['bargainId',0]],null,true);
        if(!$bargainId) return JsonService::fail('参数错误');
        $bargain = MatchBargain::getBargainTerm($bargainId);
        if(empty($bargain)) return JsonService::fail('砍价已结束');
        $bargain['time'] = time();
        $data['userInfo'] = $this->userInfo;
        $data['bargain'] = $bargain;
        $data['bargainSumCount']=0;
        return JsonService::successful($data);
    }

    /**
     * TODO  开启砍价
     * @param int $bargainId $bargainId 砍价产品编号
     * @param int $bargainUserId  $bargainUserId 开启砍价的用户编号
     */
    public function set_bargain(){
        list($bargainId) = UtilService::postMore([['bargainId',0]],null,true);
        if(!$bargainId) return JsonService::fail('参数错误');
        $count = MatchBargainUser::isBargainUser($bargainId,$this->uid);
        if($count === false) return JsonService::fail('参数错误');
        else if($count) return JsonService::successful('参与成功');
        else $res = MatchBargainUser::setBargain($bargainId,$this->uid);
        if(!$res) return JsonService::fail('参与失败');
        else return JsonService::successful('参与成功');
    }

    /**
     * TODO 帮好友砍价
     * @param int $bargainId $bargainId  砍价产品
     * @param int $bargainUserUid  $bargainUserUid 开启砍价用户编号
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function set_bargain_help(){
        list($bargainId,$bargainUserUid) = UtilService::postMore([['bargainId',0],['bargainUserUid',0]],null,true);
        if(!$bargainId || !$bargainUserUid) return JsonService::fail('参数错误');
        $res = MatchBargainUserHelp::setBargainUserHelp($bargainId,$bargainUserUid,$this->userInfo['uid']);
        if($res) {
            if(!MatchBargainUserHelp::getSurplusPrice($bargainId,$bargainUserUid)){
                $bargainUserTableId = MatchBargainUser::getBargainUserTableId($bargainId,$bargainUserUid);// TODO 获取用户参与砍价表编号
                $bargainInfo = MatchBargain::get($bargainId);//TODO 获取砍价产品信息
                $bargainUserInfo = MatchBargainUser::get($bargainUserTableId);// TODO 获取用户参与砍价信息
                RoutineTemplate::sendBargainSuccess($bargainInfo,$bargainUserInfo,$bargainUserUid);//TODO 砍价成功给开启砍价用户发送模板消息
            }
            return JsonService::successful('砍价成功');
        }
        else return JsonService::fail('砍价失败');
    }

    /**
     * TODO 获取砍价帮
     * @param int $bargainId $bargainId 砍价产品
     * @param int $bargainUserUid $bargainUserUid 开启砍价用户编号
     * @param int $offset
     * @param int $limit
     */
    public function get_bargain_user(){
        list($bargainId,$bargainUserUid,$offset,$limit) = UtilService::postMore([
            ['bargainId',0],
            ['bargainUserUid',0],
            ['offset',0],
            ['limit',20]
        ],null,true);
        if(!$bargainId) return JsonService::fail('参数错误');
        $bargainUserTableId = MatchBargainUser::getBargainUserTableId($bargainId,$bargainUserUid); //TODO 砍价帮获取参与砍价表编号
        $MatchBargainUserHelp = MatchBargainUserHelp::getList($bargainUserTableId,$offset,$limit);
        return JsonService::successful($MatchBargainUserHelp);
    }

    /**
     * TODO 添加砍价分享次数
     */
    public function add_share_bargain(){
        list($bargainId) = UtilService::postMore([['bargainId',0]],null,true);
        $data['lookCount'] = MatchBargain::getBargainLook();//TODO 观看人数
        $data['shareCount'] = MatchBargain::getBargainShare();//TODO 分享人数
        $data['userCount'] = MatchBargainUser::count();//TODO 参与人数
        if(!$bargainId) return JsonService::successful($data);
        MatchBargain::addBargainShare($bargainId);
        $data['shareCount'] = MatchBargain::getBargainShare();//TODO 分享人数
        return JsonService::successful($data);
    }

    /**
     * TODO 添加砍价浏览次数
     */
    public function add_look_bargain(){
        list($bargainId) = UtilService::postMore([['bargainId',0]],null,true);
        $data['lookCount'] = MatchBargain::getBargainLook();//TODO 观看人数
        $data['shareCount'] = MatchBargain::getBargainShare();//TODO 分享人数
        $data['userCount'] = MatchBargainUser::count();//TODO 参与人数
        if(!$bargainId) return JsonService::successful($data);
        MatchBargain::addBargainLook($bargainId);
        $data['lookCount'] = MatchBargain::getBargainLook();//TODO 观看人数
        return JsonService::successful($data);
    }

    /**
     * TODO 获取砍价帮总人数、剩余金额、进度条、已经砍掉的价格
     * @param int $bargainId
     * @param int $bargainUserUid
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_bargain_help_count(){
        list($bargainId,$bargainUserUid) = UtilService::postMore([['bargainId',0],['bargainUserUid',0]],null,true);
        if(!$bargainId || !$bargainUserUid) return JsonService::fail('参数错误');
        $count = MatchBargainUserHelp::getBargainUserHelpPeopleCount($bargainId,$bargainUserUid);//TODO 获取砍价帮总人数
        $price = MatchBargainUserHelp::getSurplusPrice($bargainId,$bargainUserUid);//TODO 获取砍价剩余金额
        $bargainUserTableId = MatchBargainUser::getBargainUserTableId($bargainId,$bargainUserUid);//TODO 获取用户参与砍价表编号
        $alreadyPrice = MatchBargainUser::getBargainUserPrice($bargainUserTableId);//TODO 用户已经砍掉的价格 好友砍价之后获取用户已经砍掉的价格
        $pricePercent = MatchBargainUserHelp::getSurplusPricePercent($bargainId,$bargainUserUid);//TODO 获取砍价进度条
        $data['count'] = $count;
        $data['price'] = $price;
        $data['alreadyPrice'] = $alreadyPrice;
        $data['pricePercent'] = $pricePercent > 10 ? $pricePercent : 10;
        return JsonService::successful($data);
    }

    /**
     * TODO 获取帮忙砍价砍掉多少金额
     * @param int $bargainId
     * @param int $bargainUserUid
     */
    public function get_bargain_user_bargain_price(){
        list($bargainId,$bargainUserUid) = UtilService::postMore([['bargainId',0],['bargainUserUid',0]],null,true);
        if(!$bargainId || !$bargainUserUid) return JsonService::fail('参数错误');
        $bargainUserTableId = MatchBargainUser::getBargainUserTableId($bargainId,$bargainUserUid);//TODO 获取用户参与砍价表编号
        $price = MatchBargainUserHelp::getBargainUserBargainPrice($bargainId,$bargainUserTableId,$this->uid,'price');// TODO 获取用户砍掉的金额
        if($price) return JsonService::successful('ok',$price);
        else return JsonService::fail('获取失败');
    }

    /**
     * TODO 获取砍价状态
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function set_status(){
        list($bargainId,$bargainUserUid) = UtilService::postMore([['bargainId',0],['bargainUserUid',0]],null,true);
        if($bargainUserUid != $this->uid) $status = 1;
        else $status = 0;
        if(!$status && !MatchBargainUserHelp::getSurplusPrice($bargainId,$bargainUserUid)){//砍价成功
           $statusSql = MatchBargainUser::getBargainUserStatus($bargainId,$bargainUserUid);
           if($statusSql == 1) $status = 3;
           else if($statusSql == 2) $status = 4;
           else if($statusSql == 3) $status = 5;
        }else if($status && !MatchBargainUserHelp::isBargainUserHelpCount($bargainId,$bargainUserUid,$this->userInfo['uid'])) $status = 2;
        return JsonService::successful('ok',$status);
    }

    /**
     * TODO 获取砍价产品  个人中心 我的砍价
     * @throws \think\Exception
     */
    public function bargain_list($page = 0,$limit = 20){
        MatchBargainUser::editBargainUserStatus($this->uid);// TODO 判断过期砍价活动
        $list = MatchBargainUser::getBargainUserAll($this->uid,$page,$limit);
        if(count($list)) return JsonService::successful($list);
        else return JsonService::fail('暂无参与砍价');
    }

    /**
     * TODO 取消砍价
     */
    public function cancel_bargain(){
        list($bargainId) = UtilService::postMore([['bargainId',0]],null,true);
        $status = MatchBargainUser::getBargainUserStatus($bargainId,$this->uid);
        if($status != 1) return JsonService::fail('状态错误');
        $id = MatchBargainUser::getBargainUserTableId($bargainId,$this->uid);
        $res = MatchBargainUser::edit(['is_del'=>1],$id);
        if($res) return JsonService::successful('取消成功');
        else return JsonService::successful('取消失败');
    }

    /**
     * TODO 生成海报
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bargain_share_poster()
    {
        list($bargainId) = UtilService::postMore([['id',0]],null,true);
        $MatchBargainInfo = MatchBargain::getBargain($bargainId);
        $price = MatchBargainUserHelp::getSurplusPrice($bargainId,$this->uid);//TODO 获取砍价剩余金额
        $alreadyPrice = MatchBargainUser::getBargainUserPrice(MatchBargainUser::getBargainUserTableId($bargainId,$this->uid));
        try{
            $data['title'] = $MatchBargainInfo['title'];
            if(stripos($MatchBargainInfo['image'], '/public/uploads/')) $data['image'] = ROOT_PATH.substr($MatchBargainInfo['image'],stripos($MatchBargainInfo['image'], '/public/uploads/'),strlen($MatchBargainInfo['image']));
            $data['price'] = bcsub($MatchBargainInfo['price'],$alreadyPrice,2);
            $data['label'] = '已砍至';
            $data['msg'] = '还差'.$price.'元即可砍价成功';
            $name = $bargainId.'_'.$this->userInfo['uid'].'_'.$this->userInfo['is_promoter'].'_bargain_share.jpg';
            $imageInfo = SystemAttachment::getInfo($name,'name');
            $siteUrl = SystemConfigService::get('site_url').DS;
            if(!$imageInfo){
                $valueData = 'id='.$bargainId.'&bargain='.$this->uid;
                if($this->userInfo['is_promoter'] || SystemConfigService::get('Match_brokerage_statu')==2) $valueData.='&pid='.$this->uid;
                $res = RoutineCode::getPageCode('pages/activity/goods_bargain_details/index',$valueData,280);
                if(!$res) return JsonService::fail('二维码生成失败');
                $imageInfo = UploadService::imageStream($name,$res,'routine/activity/bargain/code');
                if(!is_array($imageInfo)) return JsonService::fail($imageInfo);
                if($imageInfo['image_type'] == 1) $remoteImage = UtilService::remoteImage($siteUrl.$imageInfo['dir']);
                else $remoteImage = UtilService::remoteImage($imageInfo['dir']);
                if(!$remoteImage['status']) return JsonService::fail($remoteImage['msg']);
                SystemAttachment::attachmentAdd($imageInfo['name'],$imageInfo['size'],$imageInfo['type'],$imageInfo['dir'],$imageInfo['thumb_path'],1,$imageInfo['image_type'],$imageInfo['time']);
                $url = $imageInfo['dir'];
            }else $url = $imageInfo['att_dir'];
            if($imageInfo['image_type'] == 1) $data['url'] = ROOT_PATH.$url;
            else $data['url'] = $url;
            $posterImage = UtilService::setShareMarketingPoster($data,'routine/activity/bargain/poster');
            if(!is_array($posterImage)) return JsonService::fail('海报生成失败');
            SystemAttachment::attachmentAdd($posterImage['name'],$posterImage['size'],$posterImage['type'],$posterImage['dir'],$posterImage['thumb_path'],1,$posterImage['image_type'],$posterImage['time']);
            if($posterImage['image_type'] == 1) $posterImage['dir'] = $siteUrl.$posterImage['dir'];
            return JsonService::successful('ok',$posterImage['dir']);
        }catch (\Exception $e){
            return JsonService::fail('成海报失败',['line'=>$e->getLine(),'message'=>$e->getMessage()]);
        }
    }

    /**
     * 生成砍价订单
     */
    public function bargains_order()
    {
        $data = input("post.");
        $str = "match-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $match=MatchBargainUser::setBargainUserStatus($data["bargainId"], $this->userInfo['uid']); //修改砍价状态StoreBargainUser::setBargainUserStatus($bargainId, $this->userInfo['uid']); //修改砍价状态
        if($match ===false){
            JsonService::fail('砍价未成功，请重试');
        }

        $seckill = Db::name("match_bargain")->field("id,product_id,image,title,price,stop_time,min_price")->where(["id"=>$data["bargainId"]])->find();
        $price = MatchBargainUser::getBargainUserDiffPriceFloat($data["bargainId"]);
        $add=[
            "uid"=>$this->uid,
            "match_id"=>$seckill["product_id"],
            "order_price"=>$seckill["min_price"],
            "match_order_sn"=>$str,
            "add_time"=>time(),
            "match_name"=>$seckill["title"],
            "type"=>2,
            "pay_time"=>time(),
            "status"=>1,
        ];
        Db::name("match_order")->insert($add);
        $pay = new AuthApi();
        $pay->pay_order($str);
    }
}