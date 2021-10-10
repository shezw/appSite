<?php
/**
 * Description
 * addItem.php
 */

namespace payment;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\CommerceOrder;

class getStripeSign extends ASAPI
{

    private $orderid = '';

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        if( !$this->params['orderid'] ){
            return $this->error(-1,'Not valid Order');
        }

        $order = CommerceOrder::instance( $this->params['orderid'] );

        $stripe = new \APS\Stripe();

        return $stripe->getClientSecret( $order );

    }

}