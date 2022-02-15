<?php
/**
 * 预览
 * preview.php
 */
/** @var Website $website */

use APS\Article;
use APS\Category;
use APS\DBJoinParam;
use APS\Encrypt;
use APS\Time;
use APS\User;
use APS\UserAccount;
use APS\UserComment;
use APS\Website;

$detail = Article::common()->detailWithJoin($website->route['id'],[
    DBJoinParam::convinceForDetail(UserAccount::class,'authorid',Article::table)->asSub('author')
])->getContent();

$detail['createtime_'] = Time::common( $detail['createtime'] )->customOutput();
$commentCount = UserComment::common()->countByArray(['itemid'=>$detail['uid'],'itemtype'=>Article::alias])->getContent();

if( $commentCount > 0 ){

    $commentPage = $website->params['commentPage'] ?? 1;
    $commentSize = $website->params['commentSize'] ?? 10;

    $website->setSubData('comments', UserComment::common()->listByArray(['itemid'=>$detail['uid'],'itemtype'=>Article::alias],$commentPage,$commentSize)->getContent());

    $website->setSubData('commentPager',$website->structPager($commentPage,$commentSize,$commentCount,$website->params));

}

$website->setSubData('detail',$detail);
$website->setSubData('commentCount',$commentCount);
// $website->setSubData('author', UserAccount::common()->publicDetail($detail['authorid'])->getContent());

$website->setSubData('categories', Category::common()->listByArray(['type'=>Article::class])->getContent());
$website->setSubData('relatedArticles', Article::common()->listByArray([],1,12)->getContent());

$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/article/detail.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer-reaction.html');

$website->setSubData('customJS',"");

$website->rend();
