<?php
/**
 * Description
 * regist.php
 */

namespace account;


use APS\ASAPI;
use APS\ASResult;
use APS\Filter;
use APS\Mixer;
use APS\User;

/**
 * 注册
 * regist
 *
 * @param   string      username
 * @param   string      password
 * @param   string      mobile
 * @package account
 */
class regist extends ASAPI{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {
        $scope   = $this->params["scope"] ?? AccessScope_Common;

        $registUser = User::common()->add($this->params);

        if( $registUser->isSucceed() ){

            $user = new User($registUser->getContent(),null,$scope);
            return $user->access->authorize();
        }
        return $registUser;
    }

}