<?php
/** @var Website $website */

use APS\Encrypt;
use APS\Time;
use APS\User;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setTitle('User Detail');
$website->setMenuActive(['user','userManage']);

$_user = new User( $website->route['id'] );

$detail = $_user->fullDetail()->getContent();

$detail['status_'] = i18n($detail['status']);
$detail['createtime_'] = Time::common($detail['createtime'])->customOutput();
$detail['lasttime_'] = Time::common($detail['lasttime'])->customOutput();

//if( $detail['paymentid'] ){
//
//    $paymentDetail = \APS\CommercePayment::common()->detail($detail['paymentid'])->getContent();
//
//    $paymentDetail['createtime_'] = Time::common($paymentDetail['createtime'])->humanityOutput();
//    $paymentDetail['lasttime_'] = Time::common($paymentDetail['lasttime'])->humanityOutput();
//
//    $website->setSubData('paymentDetail',"支付金额: \${$paymentDetail['amount']} 于 {$paymentDetail['createtime_']}");
//    $website->setSubData('paymentTransactionid',$paymentDetail['paymenttradeno']);
//}

$website->setSubData('detail',$detail);

$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/user/detail.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
