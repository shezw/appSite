<?php
/**
 * Description
 * regist.php
 */

namespace account;


use APS\ASResult;
use APS\Filter;
use APS\Mixer;
use APS\UserAddress;

/**
 * 更新用户信息
 * update user information
 *
 * @package account
 */
class updateAddress extends \APS\ASAPI{

    protected $scope = 'public';
    public  $mode = 'JSON';

    public function run(): ASResult
    {

        if( !$this->user->isVerified() ){
            return $this->error( 9999,'Not valid user.' );
        }

        return UserAddress::common()->update($this->params,$this->params['addressid']);

    }

}