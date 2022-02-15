<?php

/** @var Website $website */

use APS\Category;
use APS\Encrypt;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$website->setMenuActive(['commerce','productAdd']);
$getCategory = Category::common()->listByArray(['type'=>'product']);

if( $getCategory->isSucceed() ){
    $website->setSubData('categoryList',$getCategory->getContent());
}

$website->setSubData('random', Encrypt::shortId(8));
$website->setSubData('customFooter',"
");
$website->setSubData('customJS',"

$('#summerNote').summernote();

Aps.uploader.initSummernoteUploader('.note-editor');
	
    Aps.uploader.init(
        'image',
        {
            type:'image',
            selector:'broseImage',
            container:'uploadContainer',
            FileUploaded:Aps.uploader.mediaUploaded,
        },
        {preview:'#imagePreview',input:'#image'});	

	Aps.uploader.init(
		'gallery',
		{
			multi:true,
			progress:1,
			type:'image',
			selector:'browseFiles',
			container:'galleryContainer',
			fileList:'#fileList',
			uploadBtn:'#uploadFiles',
			FileUploaded:Aps.uploader.galleryUploaded,
		},
		{galleryContainer:'#galleryList',input:'#gallery'});

");


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/product/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
