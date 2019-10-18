<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/16
 * Time: 11:01
 */

namespace app\ebapi\controller;


use app\core\model\routine\RoutineCode;
use service\JsonService;
use think\Db;

class Run extends AuthController
{
    /**
     * 创建跑团
     */
    public function runAdd()
    {
        $data = input("post.");
        if(empty($data["run_name"])){
            return JsonService::fail('跑团名称不能为空');
        }
        if(empty($data["contacts"])){
            return JsonService::fail('联系人不能为空');
        }
        if(empty($data["phone"])){
            return JsonService::fail('联系电话不能为空');
        }
        $array = [
            "run_name"=>$data["run_name"],
            "uid"=>$this->uid,
            "add_time"=>time(),
            "contacts"=>$data["contacts"],
            "phone"=>$data["phone"],
        ];
        //修改还是增加
        if(empty($data["id"])){
            $run = Db::name("run")->where(["uid"=>$this->uid])->count();
            if(!empty($run)){
                return JsonService::fail('一个人只能创建一个跑团');
            }
            Db::name("run")->insert($array);
        }else{
            Db::name("run")->where(["id"=>$data["id"]])->update($array);
        }
        return JsonService::successful('创建成功');
    }

    /**
     *  查看审核状态
     */
    public function runType()
    {
        $data = input("post.");
        $type = Db::name("run")->where(["id"=>$data['id']])->find();
        return JsonService::successful('获取成功',$type);

    }

    /**
     * 加入跑团
     *
     */
    public function runInsert()
    {
        $run_id = input("get.run_id");
        $run_arr  = Db::name("run_arr")->where(["run_id"=>$run_id,"uid"=>$this->uid])->find();
        if(!empty($run_arr)){
            return JsonService::fail('请不要重复加入');
        }
        if($run_arr != 1){
            return JsonService::fail('跑团还未通过审核');
        }
        $array = [
            "run_id"=>$run_id,
            "uid"=>$this->uid,
            "add_time"=>time(),
        ];
        Db::name("run_arr")->insert($array);
        return JsonService::successful('加入成功');
    }

    /**
     *跑团显示
     */
    public function echoRun()
    {
        $run = Db::name("run")->where(["uid"=>$this->uid])->find();
        return JsonService::successful('获取成功',$run);

    }

    /**
     * 生产小程序二维码
     */
    public function qrCode()
    {
        //配置APPID、APPSECRET
        $APPID = "wx73f6fda2165a0899";
        $APPSECRET = "67a712eeb331e680bfd0e0c29f8767f3";
//获取access_token
        $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$APPSECRET";
//缓存access_token
        session_start();
        $_SESSION['access_token'] = "";
        $_SESSION['expires_in'] = 0;

        $ACCESS_TOKEN = "";
        if (!isset($_SESSION['access_token']) || (isset($_SESSION['expires_in']) && time() > $_SESSION['expires_in'])) {

            $json =$this->httpRequest($access_token);
            $json = json_decode($json, true);
//             var_dump($json);
            $_SESSION['access_token'] = $json['access_token'];
            $_SESSION['expires_in'] = time() + 7200;
            $ACCESS_TOKEN = $json["access_token"];
        } else {

            $ACCESS_TOKEN = $_SESSION["access_token"];
        }

//构建请求二维码参数
//path是扫描二维码跳转的小程序路径，可以带参数?id=xxx
//width是二维码宽度
        $qcode = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$ACCESS_TOKEN";
        $id = Db::name("run")->where(["uid"=>$this->uid])->value("id");

        $param = json_encode(array("path" => "ebapi/run/runInsert?id=".$id, "width" => 150));

//POST参数
        $result = $this->httpRequest($qcode, $param, "POST");
//生成二维码
        file_put_contents("qrcode.png", $result);
        $base64_image = "data:image/jpeg;base64," . base64_encode($result);
    }



//把请求发送到微信服务器换取二维码
       public function httpRequest($url, $data='', $method='GET'){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            if($method=='POST')
            {
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data != '')
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
            }

            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            return $result;
        }








}