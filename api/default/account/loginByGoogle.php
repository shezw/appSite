<?php

namespace account;

use APS\AccessVerify;
use APS\ASAPI;
use APS\ASResult;
use APS\Google;
use APS\User;

/**
 * 通过谷歌登录
 * loginByCodeVerify
 * @param   string      origin  验证目标
 * @param   string      code    验证码
 * @param   string      scope   作用域( ios,manager,wxmini... )
 * @package account
 */
class loginByGoogle extends ASAPI
{
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        $g = new Google();

        if( $this->params['idToken'] ){

            return $g->loginByIdToken($this->params['idToken']);

        }else if( $this->params['code'] ){

            return $g->loginByCode( $this->params['code'] );
        }else if( $this->params['userInfo'] ){

            $login = $g->loginWithUserInfo($this->params['userInfo']);

            if( $login->isSucceed() ){

                $user = new User( $login->getContent()['userid'],$login->getContent()['token'], $login->getContent()['scope'] );
                $user->toSession((getConfig('id','WEBSITE') ?? 'APPSITE') . '_w');
            }

            return $login;
        }

        return $this->error(-400,'No valid params');
    }

}