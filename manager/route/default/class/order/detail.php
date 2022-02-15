<?php

use APS\CommerceOrder;
use APS\CommercePayment;
use APS\Encrypt;
use APS\Time;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');
$website->setMenuActive(['order']);

$website->setTitle('Order Detail');

$detail = CommerceOrder::common()->detail($website->route['id'])->getContent();

$detail['status_'] = i18n($detail['status']);
$detail['createtime_'] = Time::common($detail['createtime'])->customOutput();
$detail['lasttime_'] = Time::common($detail['lasttime'])->customOutput();

if( $detail['paymentid'] ){

    $paymentDetail = CommercePayment::common()->detail($detail['paymentid'])->getContent();

    $paymentDetail['createtime_'] = Time::common($paymentDetail['createtime'])->humanityOutput();
    $paymentDetail['lasttime_'] = Time::common($paymentDetail['lasttime'])->humanityOutput();

    $website->setSubData('paymentDetail',"支付金额: \${$paymentDetail['amount']} 于 {$paymentDetail['createtime_']}");
    $website->setSubData('paymentTransactionid',$paymentDetail['paymenttradeno']);
}

$website->setSubData('detail',$detail);

$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/order/detail.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
