<?php
/**
 * Description
 * regist.php
 */

namespace account;


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
class regist extends \APS\ASAPI{

    protected $scope = 'public';
    public  $mode = 'JSON';

    public function run(): ASResult
    {
        $scope   = $this->params["scope"] ?? 'common';

        $registUser = User::common()->add($this->params);

        if( $registUser->isSucceed() ){

            $user = new User($registUser->getContent(),null,$scope);
            return $user->access->authorize();
        }
        return $registUser;
    }

}