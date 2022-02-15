<?php

use APS\ASAPI;
use APS\Category;
use APS\Encrypt;
use APS\Filter;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setTitle('Media List');

$website->setMenuActive(['content','media', 'trash'==($website->params['status']??'all') ? 'mediaListTrash' : 'mediaList']);

$website->params['itemClass'] = 'APS\Media';
$website->params['filters']   = Filter::purify( $website->params, \APS\Media::filterFields );

$callResult = ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

if( $callResult->isSucceed() ){
    $mediaList = $callResult->getContent()['list'];
    foreach ( $mediaList as $i => $media ){
        $mediaList[$i]['size'] = Encrypt::convertByteSize( $mediaList[$i]['size']);
    }
}

$website->setSubData('articleList', $callResult->isSucceed() ? $mediaList : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$getCategory = Category::common()->list(\APS\DBConditions::init()->where('type')->belongTo([
    'media','image','audio','video','document'
]));
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random', Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/media/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
