<?php
/**
 * Description
 * addItem.php
 */

namespace commerce;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\CommerceCoupon;
use APS\DBConditions;

class validCoupon extends ASAPI
{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    private $couponid = '';

    public function run(): ASResult
    {

        if( !$this->params['couponid'] ){

            return $this->error(-10,'Coupon code is required.');
        }

        $checkCouponId = CommerceCoupon::common()->list(
            DBConditions::init(CommerceCoupon::table)
                ->where(CommerceCoupon::primaryid)->equal($this->params['couponid'])
                ->and('status')->equal('enabled')
        );

        return $checkCouponId->isSucceed() ?
                $this->take($checkCouponId->getContent()[0])->success() :
                $this->error(400,'Not valid code');

    }

}