<?php
/** @var Website $website */

use APS\Encrypt;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter([GroupRole_Super,GroupRole_Manager,GroupRole_Editor],'manager/insufficient');

$website->setTitle('Add category');
$website->setMenuActive(['content','category','categoryAdd']);
$website->setSubData('random', Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
$website->setSubData('customJS',"
    Aps.uploader.init(
        'image',
        {
            type:'image',
            selector:'broseImage',
            container:'uploadContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#imagePreview',input:'#image'});	

");
$website->setSubData('ValidTypes',[

    ['uid'=>Type_Common , 'description'=>i18n('common','type')],
    ['uid'=>Type_Media , 'description'=>i18n('media','type')],
    ['uid'=>Type_Article , 'description'=>i18n('article','type')],
    ['uid'=>Type_File , 'description'=>i18n('file','type')],
    ['uid'=>Type_Product , 'description'=>i18n('product','type')],
    ['uid'=>Type_Page , 'description'=>i18n('page','type')],

]);


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/category/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
