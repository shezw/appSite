<?php

use APS\Category;
use APS\CommerceProduct;
use APS\Encrypt;

$website->requireLogin('manager/login');
$website->requireGroupLevel(40000,'manager/insufficient');
$website->requireGroupCharacter(['super','manager','editor'],'manager/insufficient');

$detail = CommerceProduct::common()->detail($website->route['id'])->getContent();

$getCategory = Category::common()->listByArray(['type'=>'product']);
if( $getCategory->isSucceed() ){

    $website->setSubData('categoryList',$getCategory->getContent());

}

$website->setTitle('Edit');
$website->setMenuActive(['commerce','productList']);

$website->setSubData('detail',$detail);
$website->setSubData('random', Encrypt::shortId(8));

$customJS = "
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

    Aps.former.switchField('.a-field-switch-selector');
	Aps.uploader.galleryInit();

    VD('#categoryid').value( '{$detail['categoryid']}' ).trigger('change');

    Aps.former.watch('.a-field.dynamic');
";

$website->setSubData('customJS',$customJS);


$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR.'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR.'class/product/edit.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer-editor.html');

$website->rend();
