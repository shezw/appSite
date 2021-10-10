<?php
/**
 * Description
 * addItem.php
 */

namespace commerce;

use APS\ASAPI;
use APS\ASResult;
use APS\CommerceOrder;
use APS\DBValues;
use APS\Mixer;
use APS\SMTP;
use APS\MediaTemplate;
use PHPMailer\PHPMailer\Exception;

class orderShipped extends ASAPI
{
    const groupCharacterRequirement = ['super','manager','editor'];
    const groupLevelRequirement = 80000;

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {

        $orderid = $this->params['orderid'];
        $trackid = $this->params['trackid'];

        if( !$orderid || !$trackid ){ return $this->error(-10,'Require inputs'); }

        CommerceOrder::common()->update(DBValues::init('status')->string('shipping')->set('trackid')->stringIf($trackid),$orderid);

        $order = CommerceOrder::common()->detail($orderid)->getContent();

        $email = $order['details']['shippingAddress']['email'];

        $meta['site'] = getConfig('title','WEBSITE');
        $meta['firstname'] = $order['details']['shippingAddress']['firstname'];
        $meta['lastname']  = $order['details']['shippingAddress']['lastname'];
        $meta['trackid']   = $trackid;
        $meta['orderid']   = $orderid;

        $smtp = new SMTP();
        $smtp->setFrom(getConfig('title','WEBSITE') );

        $content = Mixer::mix($meta, MediaTemplate::common()->recent('orderShipped',Type_Email)->getContent() );

        try {
            $smtp->send($email, MediaTemplate::common()->recent('newPaidOrder',Type_EmailSubject)->getContentOr("Your product has been shipped."), $content);
        } catch (Exception $e) {
            return $this->take($e)->error(11,'Send Failed');
        }

        return $this->take($email)->success();
    }

}