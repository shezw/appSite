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
 * 修改密码
 * change password
 *
 * @package account
 */
class changePassword extends ASAPI{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        if( !$this->user->isVerified() ){
            return $this->error( 9999,'Not valid user.' );
        }

        $newpassword = $this->params['newpassword'];
        $repassword  = $this->params['repassword'];
        $password    = $this->params['password'];

        $checkPassword = User::common()->checkPassword( $password, $this->user->userid );

        if( $newpassword != $repassword ){
            return $this->error(-300,'Password, Repassword not match.');
        }

        if( !$checkPassword->isSucceed() ){
            return $checkPassword;
        }

        return User::common()->updateByArray(['password'=>$password],$this->user->userid);

    }

}