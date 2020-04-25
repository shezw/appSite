<?php

namespace APS;

/**
 * 阿里云短信
 * AliyunSMS
 * @package APS\extension
 */
class AliyunSMS extends ASModel {

    private $accessKeyId;
    private $accessKeySecret;
    private $regionId;

    function __construct( $accessKeyId = null, $accessKeySecret = null, $regionId = 'cn-hangzhou' ){

        parent::__construct();

        $this->accessKeyId     = $accessKeyId     ?? getConfig('SMS_KEYID','ALIYUN') ;
        $this->accessKeySecret = $accessKeySecret ?? getConfig('SMS_KEYSECRET','ALIYUN') ;
        $this->regionId        = $regionId;
    }

    /**
     * [send 单条发送 Send a single message]
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-06-14T12:37:04+0800
     * @version  [version]
     * @param    [type]                   $params         [K-V array]

     * PhoneNumbers = "17000000000";     # 手机号
     * TemplateCode = "SMS_0000001";     # 模版代码
     * SignName = "短信签名";             # 短信签名
    ? OutId = (string) microtime(true); # 流水号
    ? TemplateParam = [ "code" => "12345", "product" => "阿里通信" ];  # 模版参数 k-v json

     * @return   [type]                                   [description]
     */
    public function send($params) {

        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, [
                "RegionId" => $this->regionId,
                "Action"   => "SendSms",
                "Version"  => "2017-05-25",
            ])
            // fixme 选填: 启用https
            ,true
        );

        return $content;
    }

    /**
     * [bulkSend 批量发送短信 Send messages] 最高100条
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-06-14T12:39:09+0800
     * @version  1.0
     * @param    [type]                   $params         [description]

     * PhoneNumberJson = ["1500000000","1500000001",];   # 手机号列表 que json
     * SignNameJson = ["云通信","云通信2",];               # 签名列表 que json
     * TemplateCode = "SMS_1000000";                     # 模版代码
    ? SmsUpExtendCodeJson = json_encode(["90997","90998"]); # 上行短信扩展码 7位以下 无特殊需求忽略
    ? TemplateParamJson = [ [ "name" => "Tom",   "code" => "123", ],[ "name" => "Jack",  "code" => "456", ],];
    #模版参数 k-v json in que json
    友情提示:换行等字符需要参照标准的JSON协议, 比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n

     * @return   [type]                                   [description]
     */
    public function bulkSend($params) {

        $params["TemplateParamJson"]  = json_encode($params["TemplateParamJson"], JSON_UNESCAPED_UNICODE);
        $params["SignNameJson"]       = json_encode($params["SignNameJson"], JSON_UNESCAPED_UNICODE);
        $params["PhoneNumberJson"]    = json_encode($params["PhoneNumberJson"], JSON_UNESCAPED_UNICODE);

        if(!empty($params["SmsUpExtendCodeJson"] && is_array($params["SmsUpExtendCodeJson"]))) {
            $params["SmsUpExtendCodeJson"] = json_encode($params["SmsUpExtendCodeJson"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, [
                "RegionId" => $this->regionId,
                "Action" => "SendBatchSms",
                "Version" => "2017-05-25",
            ])
            // fixme 选填: 启用https
            ,true
        );

        return $content;
    }

    /**
     * 发送记录查询
     * Detail of sended message
     * @param    array    $params         [description]
                PhoneNumber = "17000000000";       # 手机号
                SendDate = "20170710";             # 发送日期，格式Ymd，支持近30天记录查询
                PageSize = 10;                     # 分页大小
                CurrentPage = 1;                   # 分页号
                BizId = (string) microtime(true);  # 发送流水号
     * @return   [type]  [description]
     */
    public function sendDetails($params) {

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => $this->regionId,
                "Action"   => "QuerySendDetails",
                "Version"  => "2017-05-25",
            ))
            // fixme 选填: 启用https
            ,true
        );

        return $content;
    }

}



/**
 * 签名助手 2017/11/19
 *
 * Class SignatureHelper
 */
class SignatureHelper {

    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security=false) {
        $apiParams = array_merge(array (
            "SignatureVersion" => "1.0",
            "Format"           => "JSON",
            "SignatureMethod"  => "HMAC-SHA1",
            "AccessKeyId"      => $accessKeyId,
            "Timestamp"        => gmdate("Y-m-d\TH:i:s\Z"),
            "SignatureNonce"   => uniqid(mt_rand(0,0xffff), true),
        ), $params);

        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $k => $v) {
            $sortedQueryStringTmp .= "&" . $this->encode($k) . "=" . $this->encode($v);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&",true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http')."://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content);
        } catch( \Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
}