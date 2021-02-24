<?php

/** @var \APS\Website $website */

$website->requireUser('manager/login');
$website->requireGroupLevel(80000, 'manager/insufficient');
$website->requireGroupCharacter(['super', 'manager', 'editor'], 'manager/insufficient');

$website->params['itemClass'] = 'APS\User';
$website->params['filters'] = \APS\Filter::purify($website->params, \APS\User::$countFilters);
$website->params['filters']['groupid'] = '100';

$callResult = \APS\ASAPI::systemInit('manager\itemList', $website->params, $website->user)->run();

$website->setTitle('User');
$website->setMenuActive(['user','userManage']);

$website->setSubData('userList', $callResult->isSucceed() ? $callResult->getContent()['list'] : null);
$website->setSubData('customJS',"

    VL('.datetimepicker').each( function(vd){ vd.value(''); } );
	VL('.datetimepicker').on('blur',function(vd){
        
		var stringTime = vd.value().replaceAll('-','/');
		var timeStamp  = Date.parse(new Date(stringTime))/1000;
        vd.parent('div').find('.a-field-main').value(timeStamp);
	});

");

$nav = $callResult->getContent()['nav'];

$website->setSubData('pager', $website->structPager($nav['page'], $nav['size'], $nav['total'], $website->params));

$website->setSubData('random', \APS\Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR . 'common/header.html');

$website->appendTemplateByFile(THEME_DIR . 'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR . 'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR . 'class/user/list.html');

$website->appendTemplateByFile(THEME_DIR . 'common/footer.html');

$website->blendMenuAccessByFile(SITE_DIR . 'basic/menu/sidebar.php');

$website->rend();
