<?php

namespace account;

use APS\ASAPI;
use APS\ASResult;
use APS\Wechat;

/**
 * 通过微信登录
 * loginByCodeVerify
 * @package account
 */
class loginByWechat extends ASAPI
{
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {
        $userLogin = Wechat::common()->oauthLogin( $this->params,$this->params['origin'] ?? 'common' );

        if (!$userLogin->isSucceed()) {
            $message = 'failed';
        }else{
            $message = 'success';
        }

        $callbackURL = $this->params['callbackurl'];

        $userid = $userLogin->getContent()['userid'];
        $token  = $userLogin->getContent()['token'];
        $expire = $userLogin->getContent()['expire'];
        $scope  = $userLogin->getContent()['scope'];

        if(isset($this->params['code']))  unset($this->params['code']);
        if(isset($this->params['state'])) unset($this->params['state']);
        unset($this->params['callbackurl']);
        unset($this->params['uri']);

        $callbackURL.= strstr($callbackURL,'?') ? '&' : '?';
        $callbackURL.= 'userid='.$userid.'&token='.$token.'&expire='.$expire.'&scope='.$scope.'&message='.$message;

        Header("Location: $callbackURL");
        exit();
    }

}