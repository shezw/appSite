<?php
/** @var \APS\Website $website */

$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$detail = \APS\Category::common()->detail($website->route['id'])->getContent();

$website->setTitle('Edit Category');
$website->setMenuActive(['content','category']);

$website->setSubData('detail',$detail);
$website->setSubData('random',\APS\Encrypt::shortId(8));

$customJS = "

    Aps.uploader.init(
        'image',
        {
            type:'image',
            selector:'broseImage',
            container:'uploadContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#imagePreview',input:'#image'});	

    VD('#featured').toggleAttr( 'checked' , {$detail['featured']} );

    Aps.former.watch('.a-field');
";

$website->setSubData('customJS',$customJS);

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/category/edit.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
