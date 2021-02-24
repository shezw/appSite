<?php

/** @var \APS\Website $website */
$website->requireUser('manager/login');
$website->requireGroupLevel(80000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$getCategory = \APS\Category::common()->list(['type'=>'product']);
if( $getCategory->isSucceed() ){
    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setSubData('random',\APS\Encrypt::shortId(8));
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
			selector:'brosweFiles',
			container:'galleryContainer',
			fileList:'#fileList',
			uploadBtn:'#uploadFiles',
			FileUploaded:Aps.uploader.galleryUploaded,
		},
		{galleryContainer:'#galleryList',input:'#gallery'});

");


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->setMenuActive(['commerce','productAdd']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/product/add.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
