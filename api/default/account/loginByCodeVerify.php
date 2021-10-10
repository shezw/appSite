<?php

namespace account;

use APS\AccessVerify;
use APS\ASAPI;
use APS\ASResult;
use APS\User;

/**
 * 通过验证码登录
 * loginByCodeVerify
 * @param   string      origin  验证目标
 * @param   string      code    验证码
 * @param   string      scope   作用域( ios,manager,wxmini... )
 * @package account
 */
class loginByCodeVerify extends ASAPI
{
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {
        $origin = $this->params['origin'];

        # 加入防暴力破解机制 6位数字验证码

        $checkVerify = AccessVerify::common()->validate($origin,$this->params['code'],$this->params['scope']);

        if( !$checkVerify->isSucceed() ){
            return $checkVerify;
        }

        $field = strstr($origin,"@") ? 'email' : 'mobile';
        $checkUser = User::common()->getUserid($field,$origin);

        if( !$checkUser->isSucceed() ){
            if( getConfig('AUTO_LOGINTOREGIST') ){ # 启用未注册用户自动登录  Enable auto sign up when not registed

                $addUser = User::common()->addByArray([$field=>$origin]);
                if( !$addUser->isSucceed() ){ return $addUser; }
                $userid = $addUser->getContent();

            }else{
                return $checkUser;
            }
        }else{
            $userid = $checkUser->getContent();
        }

        return User::common()->systemAuthorize($userid, $this->params['scope']);
    }

}