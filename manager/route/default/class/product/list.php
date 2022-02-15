<?php
/** @var Website $website */

use APS\ASAPI;
use APS\Category;
use APS\CommerceProduct;
use APS\Encrypt;
use APS\Filter;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = CommerceProduct::class;
$website->params['filters']   = Filter::purify( $website->params, CommerceProduct::filterFields );

$callResult = ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

$website->setTitle('Products');
$website->setMenuActive(['commerce','product',$website->params['status']??'all'=='trash' ? 'productListTrash' : 'productList']);

$website->setSubData('productList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$getCategory = Category::common()->listByArray(['type'=>'product']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random', Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/product/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
