<?php

namespace account;

use APS\AccessToken;
use APS\AccessVerify;
use APS\ASAPI;
use APS\ASResult;
use APS\DBConditions;
use APS\Google;
use APS\Paypal;
use APS\User;
use APS\UserInfo;

/**
 * 通过谷歌登录
 * loginByCodeVerify
 * @param   string      origin  验证目标
 * @param   string      code    验证码
 * @param   string      scope   作用域( ios,manager,wxmini... )
 * @package account
 */
class loginByFacebook extends ASAPI
{
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        $facebookID = $this->params['userInfo']['facebookID'];

        if( !$facebookID ){
            return $this->error(-10,'Not valid field');
        }

        if ( !UserInfo::common()->has(DBConditions::init(UserInfo::table)->where('facebookID')->equal($facebookID) ) ){ # 未注册

            // start regist
            $addUser = User::common()->addByArray(['facebookID'=>$facebookID] );

            if (!$addUser->isSucceed()){ return $addUser;}

            $userid = $addUser->getContent();

        }else{

            // start login
            $getUserid = User::common()->getUserid('facebookID',$facebookID);

            if (!$getUserid->isSucceed()){ return $getUserid;}

            $userid = $getUserid->getContent();
        }

        $authorize = AccessToken::common()->addToken( $userid, 'common' );

        if( !$authorize->isSucceed() ){
            return $authorize;
        }
        $user = new User( $userid,$authorize->getContent()['token'], $authorize->getContent()['scope'] );
        $user->toSession((getConfig('id','WEBSITE') ?? 'APPSITE') . '_w');

        _ASRecord()->add([
            'itemid'=>$userid,
            'type'=>'user',
            'event'=>'USER_LOGIN_FACEBOOK',
            'sign'=>'loginWithFacebook',
            'content'=>['facebookID'=>$facebookID,'expire'=>getConfig('LOGINTOKEN_DURATION')+time()]
        ]);

        return $this->take(['userid'=>$userid,'token'=>$authorize->getContent()['token'],'scope'=>$scope,'expire'=>$authorize->getContent()['expire']])->success(i18n('USR_LOG_SUC'),'account/loginByFacebook');

    }

}