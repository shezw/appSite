<?php
/**
 * 预览
 * preview.php
 */
/** @var Website $website */

use APS\Article;
use APS\Encrypt;
use APS\UserAccount;
use APS\Website;

$page = $website->params['page'] ?? 1;
$size = $website->params['size'] ?? 12;

$articleCount = Article::common()->countByArray(['status'=>'enabled'])->getContent();

$articles = Article::common()->listWithJoinByArray(
    ['status'=>'enabled'],
    [
        APS\DBJoinParam::convinceForDetail( UserAccount::class, 'authorid', Article::table )->asSub('author')
    ],$page,$size
)->getContent();

//var_dump($articles);
$website->setSubData('articles',$articles);
$website->setSubData('random', Encrypt::shortId(8));

$website->setSubData('pager',$website->structPager($page,$size,$articleCount,$website->params));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/article/list.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('customJS',"");

$website->rend();
