<?php

/** @var \APS\Website $website */

use APS\Article;
use APS\CommerceOrder;
use APS\CommerceProduct;
use APS\DBConditions;
use APS\User;
use APS\UserAccount;

$website->requireUser('manager/login');
$website->setTitle('Dashboard');
$website->setMenuActive(['dashboard']);

$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');
//$website->setSubData('menu',['operation'=>true,'operationUser'=>true]);

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');

$website->appendTemplateByFile(THEME_DIR.'page/dashboard.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

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
        $productList[$i]["rate"] = $product["viewtimes"] / 10;
    }
    $website->setSubData('products', $productList);
}

$website->rend();
