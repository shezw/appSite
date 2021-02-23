<?php
/**
 * Description
 * addItem.php
 */

namespace commerce;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class validCoupon extends ASAPI
{

    private $couponid = '';

    protected $scope = 'public';
    public    $mode = 'JSON';

    public function run(): ASResult
    {

        if( !$this->params['couponid'] ){

            return $this->error(-10,'Coupon code is required.');
        }

        $checkCouponid = \APS\CommerceCoupon::common()->list(['couponid'=>$this->params['couponid'],'status'=>'enabled']);

        return $checkCouponid->isSucceed() ? 
                $this->take($checkCouponid->getContent()[0])->success() :
                $this->error(400,'Not valid code');

    }

}