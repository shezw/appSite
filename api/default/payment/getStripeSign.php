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

    protected $scope = 'public';
    public    $mode = 'JSON';

    // protected static $groupCharacterRequirement = ['super','manager','editor'];
    // protected static $groupLevelRequirement = 10000;

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