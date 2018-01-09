<?php
namespace Home\Controller;
use Think\Controller;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

class SmsController extends Controller
{
	/**
     * 生成短信验证码
     * @param  integer $length [验证码长度]
     */
    public function createSMSCode($length = 4)
    {
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }

    /**
     * 发送验证码
     * @param  [integer] $phone [手机号]
     */
    public function send_phone($phone)
    {
        $code=$this->createSMSCode($length = 4);

        require_once  './Api/dysms/vendor/autoload.php';    //此处为你放置API的路径
        Config::load();             //加载区域结点配置

        $accessKeyId = 'LTAINF3v8WLpO8dN';
        $accessKeySecret = 'ky7pZI8swoenK98UIcD88d4KOW48Qw';
        $templateCode = 'SMS_115380600';   //短信模板ID

        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";

        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";

        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);

        // 初始化AcsClient用于发起请求
        $acsClient = new DefaultAcsClient($profile);

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phone);

        // 必填，设置签名名称
        $request->setSignName("陈贵进");

        // 必填，设置模板CODE
        $request->setTemplateCode("SMS_115380600");

        $smsData = array('code'=>$code);    //所使用的模板若有变量 在这里填入变量的值  我的变量名为username此处也为username

        //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParam(json_encode($smsData));

        //发起访问请求
        $acsResponse = $acsClient -> getAcsResponse($request);
        //返回请求结果
        $result = json_decode(json_encode($acsResponse), true);
        $resp = $result['Code'];
        if ($resp == 'OK') {
        	session('phone_code', $code);
            session('phone_time', time());
            session('phone', $phone);
            return ture;
        } else {
        	return false;
        }                                                                   
        
    }
}