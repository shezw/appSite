<?php
/**
 * 预览
 * preview.php
 */
/** @var \APS\Website $website */

$detail = \APS\Article::common()->detail($website->route['id'])->getContent();

$detail['createtime_'] = \APS\Time::common( $detail['createtime'] )->customOutput();
$commentCount = \APS\UserComment::common()->count(['itemid'=>$detail['uid'],'itemtype'=>'Article'])->getContent();

if( $commentCount > 0 ){

    $commentPage = $website->params['commentPage'] ?? 1;
    $commentSize = $website->params['commentSize'] ?? 10;

    $website->setSubData('comments',\APS\UserComment::common()->list(['itemid'=>$detail['uid'],'itemtype'=>'Article'],$commentPage,$commentSize)->getContent());

    $website->setSubData('commentPager',$website->structPager($commentPage,$commentSize,$commentCount,$website->params));

}

$website->setSubData('detail',$detail);
$website->setSubData('commentCount',$commentCount);
$website->setSubData('author',\APS\User::common()->publicDetail($detail['authorid'])->getContent());

$website->setSubData('categories',\APS\Category::common()->list([])->getContent());
$website->setSubData('relatedArticles',\APS\Article::common()->list([],1,12)->getContent());

$website->setSubData('random',\APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/article/detail.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('customJS',"");

$website->rend();
