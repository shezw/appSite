<?php

namespace APS;

/**
 * 短信
 * SMS
 * @package APS\extension
 */
class SMS extends ASObject{

    /**
     * 短信认证
     * verify with SMS
     * @param  string  $mobile  手机号
     * @param  string  $scope   作用域 ( login, verify, findPassword... )
     * @return \APS\ASResult
     * @version  1.1
     */
    public function verify( string $mobile, string $scope = 'verify' ) {

        $beginVerify = AccessVerify::common()->begin($mobile,getConfig('ACCESSVERIFY_VALID')??300,$scope);

        if (!$beginVerify->isSucceed()) {

            _ASRecord()->add([
                'status'=>500,
                'content'=>$beginVerify,
                'event'=>'SERVER_INIT',
                'type'=>'SERVER'
            ]);

            return $this->take($beginVerify)->error(500,i18n('SMS_SEND_FAL','SMS->verify'));
        }

        $code = $beginVerify->getContent();

        $sendSMS = $this->send(['code'=>$code],$mobile, getConfig($scope,'SMS_MODULE_CODE') );

        return $sendSMS->isSucceed() ?
            $this->take($scope)->success(i18n('SMS_SEND_SUC'),'SMS->verify') :
            $this->take($sendSMS->getContent())->error(100,i18n('SMS_SEND_FAL'),'SMS::verify');

    }

    /**
     * 发送短信
     * send
     * @param  array   $params          模板参数
     *                                  模板参数会自动填充到对应短信模板，如 验证码是:{$code} 会填充为 验证码是:123456
     * @param  string  $mobile          手机号
     * @param  string  $templateCode    对应模板id
     * @return \APS\ASResult
     */
    public  function send( array $params, string $mobile, string $templateCode ){

        switch (getConfig('SMS_PROVIDER')) {

            case 'ALIYUNSMS':
            case 'AliyunSMS':
                //开始发送短信
                $smsParams["PhoneNumbers"]  = $mobile;                            # 手机号
                $smsParams["SignName"]      = getConfig('SMS_SIGN_VERIFY','ALIYUN');   # 短信签名
                $smsParams["TemplateCode"]  = $templateCode ?? getConfig('verify','SMS_MODULE_CODE'); # 模版代码
                $smsParams['OutId']         = (string) microtime(true);           # 流水号
                $smsParams['TemplateParam'] = !empty($params)?$params:'';         # 模版参数 k-v json

                $sms  = new AliyunSMS();
                $send = $sms->send($smsParams);

                _ASRecord()->add([
                    'type'     => 'SMS',
                    'status'   => ( $send->Message == 'OK' || $send->Code == 'OK' ) ? 0 : 20008,
                    'content'  => $smsParams,
                    'event'    => 'SMS_SEND_CUSTOM',
                    'sign'     => 'SMS::send',
                ]);

                return ( $send->Message == 'OK' || $send->Code == 'OK' ) ?
                    $this->take($mobile)->success(i18n('SMS_SEND_SUC'),'SMS::send') :
                    $this->take($send->Message)->error(20008,i18n('SMS_SEND_FAL'),'SMS->send');

                break;

            default:
                return $this->error(8,i18n('SYS_CONF_REQ'),'SMS->send');
                break;
        }
    }



}