<?php

/** @var \APS\Website $website */

use APS\Article;
use APS\CommerceOrder;
use APS\CommerceProduct;
use APS\DBConditions;
use APS\Time;
use APS\User;
use APS\UserAccount;

$website->requireLogin('manager/login');
$website->setTitle('Dashboard');
$website->setMenuActive(['dashboard']);

$website->inquireMenu();

//$website->blendMenuAccessByFile(SITE_DIR.'route/default/menu/sidebar.php');
//$website->setSubData('menu',['operation'=>true,'operationUser'=>true]);

$website->appendTemplateByFile(THEME_DIR.'common/header.html')

        ->appendTemplateByFile(THEME_DIR.'common/sidebar.html')
        ->appendTemplateByFile(THEME_DIR.'common/topbar.html')

        ->appendTemplateByFile(THEME_DIR.'page/dashboard.html')

        ->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('statics',[
    'userCount'=> UserAccount::common()->count(DBConditions::init()->where('groupid')->equal(Group_Registered))->getContent(),
    'productCount'=> CommerceProduct::common()->count(CommerceProduct::emptyCondition())->getContent(),
    'orderCount'=> CommerceOrder::common()->count(CommerceOrder::emptyCondition())->getContent(),
    'articleCount'=> Article::common()->count(Article::emptyCondition())->getContent()
]);

$products = CommerceProduct::common()->list(DBConditions::init()->where('status')->equal(Status_Enabled),1,5,'viewtimes DESC',false);
if ($products->isSucceed()){
    $productList = $products->getContent();

    foreach ($productList as $i => $product) {

        $productList[$i]['createtime_'] = Time::common($product['createtime'])->humanityOutput();
        $productList[$i]['lasttime_'] = Time::common($product['lasttime'])->humanityOutput();

    }
    $website->setSubData('products', $productList);
}

$articles = Article::common()->listWithJoinByArray(
    ['status'=>'enabled'],
    [
        APS\DBJoinParam::convinceForDetail( UserAccount::class, 'authorid', Article::table )->asSub('author')
    ],1,5,Article::table.".createtime DESC");

if ($articles->isSucceed()){

    $articleList = $articles->getContent();

    foreach ($articleList as $i => $article) {

        $articleList[$i]['createtime_'] = Time::common($article['createtime'])->humanityOutput();
        $articleList[$i]['lasttime_'] = Time::common($article['lasttime'])->humanityOutput();

    }
    $website->setSubData('articles', $articleList);
}

$orders = CommerceOrder::common()->listWithJoinByArray(
    [],[
        APS\DBJoinParam::convinceForDetail(UserAccount::class,'userid',CommerceOrder::table)->asSub('customer')
],1,5, CommerceOrder::class.".createtime DESC" );

if ($orders->isSucceed()){

    $orderList = $orders->getContent();

    foreach ($orderList as $i => $order) {

        $orderList[$i]['status_'] = i18n($order['status'], 'STATUS');

        $orderList[$i]['createtime_'] = Time::common($order['createtime'])->humanityOutput();
        $orderList[$i]['lasttime_'] = Time::common($order['lasttime'])->humanityOutput();

    }
    $website->setSubData('orders', $orderList);
}



$website->rend();
