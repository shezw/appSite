<?php

/** @var \APS\Website $website */

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
    'userCount'=>\APS\User::common()->count(['groupid'=>'100'])->getContent(),
    'productCount'=>\APS\CommerceProduct::common()->count([])->getContent(),
    'orderCount'=>\APS\CommerceOrder::common()->count([])->getContent(),
    'articleCount'=>\APS\Article::common()->count([])->getContent()
]);

$products = \APS\CommerceProduct::common()->list(['status'=>'enabled'],1,5,'viewtimes DESC',false);
if ($products->isSucceed()){
    $productList = $products->getContent();

    foreach ($productList as $i => $product) {
        $productList[$i]["rate"] = $product["viewtimes"] / 10;
    }
    $website->setSubData('products', $productList );
}

$website->rend();
