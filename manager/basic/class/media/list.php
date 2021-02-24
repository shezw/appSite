<?php

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->params['itemClass'] = 'APS\Media';
$website->params['filters']   = \APS\Filter::purify( $website->params, \APS\Media::$countFilters );

$callResult = \APS\ASAPI::systemInit( 'manager\itemList', $website->params, $website->user )->run() ;

if( $callResult->isSucceed() ){
    $mediaList = $callResult->getContent()['list'];
    foreach ( $mediaList as $i => $media ){
        $mediaList[$i]['size'] = \APS\Encrypt::convertByteSize( $mediaList[$i]['size']);
    }
}

$website->setTitle('Media List');

$website->setSubData('articleList', $callResult->isSucceed() ? $mediaList : null );

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager( $nav['page'],$nav['size'] , $nav['total'], $website->params));

$getCategory = \APS\Category::common()->list(['type'=>'article']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random',\APS\Encrypt::shortId(8));


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['content','media', $website->params['status']=='trash' ? 'mediaListTrash' : 'mediaList']);

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');

$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/media/list.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
