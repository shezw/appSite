<?php

use APS\Encrypt;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setMenuActive(['commerce','shipping','shippingAdd']);

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


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/shipping/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
