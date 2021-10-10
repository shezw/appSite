<?php
/**
 * orderPaid.php
 *
 * Description
 *
 *
 */

namespace commerce;

use APS\AnalysisProduct;
use APS\ASAPI;
use APS\ASResult;
use APS\CommerceOrder;
use APS\MediaTemplate;
use APS\Mixer;
use APS\SMTP;
use APS\Time;
use PHPMailer\PHPMailer\Exception;

class orderPaid extends ASAPI
{

    const scope = ASAPI_Scope_System;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        $orderId = $this->params['orderid'];

        $order = CommerceOrder::common()->detail($orderId)->getContent();

        $email = $order['details']['shippingAddress']['email'];

        foreach ( $order['details']['items'] as $k => $item ){

            AnalysisProduct::common()->addByArray([
                'productid'=>$item['productid'],
                'price'=>$item['price'],
                'sale'=>$item['sale'],
                'count'=>$item['count'],
                'cover'=>$item['cover'],
                'title'=>$item['title'],
                'total'=>$item['subTotal']
            ]);
        }
        $order['createtime_'] = Time::common($order['createtime'])->customOutput();

        $order['total'] = $order['amount'];
        $order['shippingAmount'] = $order['delivery']['price'] ?? 0;
        $order['couponAmount'] = $order['coupon'] ? $order['coupon']['amount'] : 0;
        $order['subTotal'] = $order['total'] - $order['couponAmount'] - $order['shippingAmount'];

        $order['site'] = getConfig('title','WEBSITE');

        $smtp = new SMTP();
        $smtp->setFrom(getConfig('title','WEBSITE') );

        $content = Mixer::mix($order, MediaTemplate::common()->recent('orderConfirmed',Type_Email)->getContent() );

        try {
            $smtp->send($email, MediaTemplate::common()->recent('orderConfirmed',Type_EmailSubject)->getContentOr("Your order has been confirmed"), $content);
        } catch (Exception $e) {
            return $this->take($e)->error(11,'Send Failed');
        }


        $adminNotify = new SMTP();
        $adminNotify->setFrom(getConfig('title','WEBSITE'));

        $content = Mixer::mix( $order, MediaTemplate::common()->recent('newPaidOrder',Type_Email)->getContent() );

        try{
            $adminNotify->send('', MediaTemplate::common()->recent('newPaidOrder',Type_EmailSubject)->getContentOr("Order payment completion."), $content);
        } catch ( Exception $e ){
            return $this->take($e)->error(11,'Send Failed');
        }

        return $this->take($email)->success();
    }

}