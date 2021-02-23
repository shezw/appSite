<?php
/**
 * orderPaid.php
 *
 * Description
 *
 *
 */

namespace commerce;

use APS\ASAPI;
use APS\ASResult;
use APS\CommerceOrder;
use APS\MailTemplate;
use APS\Mixer;
use APS\SMTP;
use APS\Time;
use PHPMailer\PHPMailer\Exception;

class orderPaid extends ASAPI
{

    protected $scope = 'system';
    public  $mode = 'JSON';

    public function run(): ASResult
    {

        $orderid = $this->params['orderid'];

        $order = CommerceOrder::common()->detail($orderid)->getContent();

        $email = $order['details']['shippingAddress']['email'];

        foreach ( $order['details']['items'] as $k => $item ){

            \APS\AnalysisProduct::common()->add([
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
        $smtp->setFrom(NULL, getConfig('title','WEBSITE') );

        $content = Mixer::mix($order, MailTemplate::$orderConfirmed );

        try {
            $smtp->send($email, "Your order has been confirmed", $content);
        } catch (Exception $e) {
            return $this->take($e)->error(11,'Send Failed');
        }


        $adminNotify = new SMTP();
        $adminNotify->setFrom(NULL, getConfig('title','WEBSITE'));

        $content = Mixer::mix( $order, MailTemplate::$newPaidOrder );

        try{
            $adminNotify->send('contact@honeybay.life', "Order payment completion.", $content);
        } catch ( Exception $e ){
            return $this->take($e)->error(11,'Send Failed');
        }

        return $this->take($email)->success();
    }

}