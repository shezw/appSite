<?php
/**
 * 预览
 * preview.php
 */

/** @var Website $website */

use APS\CommerceProduct;
use APS\Encrypt;
use APS\Website;

$detail = CommerceProduct::common()->detail($website->route['id'])->getContent();

$website->setSubData('detail',$detail);
$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/product/detail.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('customFooter','');
$website->setSubData('customJS',"");

$website->rend();
