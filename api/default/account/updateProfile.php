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
 * 更新用户信息
 * update user information
 *
 * @package account
 */
class updateProfile extends ASAPI{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        if( !$this->user->isVerified() ){
            return $this->error( 9999,'Not valid user.' );
        }

        return User::common()->update($this->params,$this->user->userid);

    }

}