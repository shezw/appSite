<?php
/**
 * Description
 * addItem.php
 */

namespace commerce;

use APS\ASAPI;
use APS\ASResult;
use APS\CommerceOrder;
use APS\MailTemplate;
use APS\Mixer;
use APS\SMTP;
use PHPMailer\PHPMailer\Exception;

class orderShipped extends ASAPI
{
    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 80000;

    protected $scope = 'public';
    public    $mode = 'JSON';

    public function run(): ASResult
    {

        $orderid = $this->params['orderid'];
        $trackid = $this->params['trackid'];

        if( !$orderid || !$trackid ){ return $this->error(-10,'Require inputs'); }

        CommerceOrder::common()->update(['status'=>'shipping','trackid'=>$trackid],$orderid);

        $order = CommerceOrder::common()->detail($orderid)->getContent();

        $email = $order['details']['shippingAddress']['email'];

        $meta['site'] = getConfig('title','WEBSITE');
        $meta['firstname'] = $order['details']['shippingAddress']['firstname'];
        $meta['lastname']  = $order['details']['shippingAddress']['lastname'];
        $meta['trackid']   = $trackid;
        $meta['orderid']   = $orderid;

        $smtp = new SMTP();
        $smtp->setFrom(NULL, getConfig('title','WEBSITE') );

        $content = Mixer::mix($meta, MailTemplate::$orderShipped );

        try {
            $smtp->send($email, "Your product has been shipped.", $content);
        } catch (Exception $e) {
            return $this->take($e)->error(11,'Send Failed');
        }

        return $this->take($email)->success();
    }

}