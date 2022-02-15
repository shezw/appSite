<?php

/** @var Website $website */

use APS\ASAPI;
use APS\Encrypt;
use APS\Filter;
use APS\User;
use APS\Website;

$website->requireLogin('manager/login');
$website->requireGroupLevel(GroupLevel_Admin, 'manager/insufficient');
$website->requireGroupCharacter([GroupRole_Super,GroupRole_Manager,Group_Editor], 'manager/insufficient');

$website->setTitle('User');
$website->setMenuActive(['user','userManage']);

$website->params['itemClass'] = User::class;
$website->params['filters'] = Filter::purify($website->params, User::filterFields);
$website->params['filters']['groupid'] = Group_Registered;

$callResult = ASAPI::systemInit(manager\itemList::class, $website->params, $website->user)->run();

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

$website->setSubData('random', Encrypt::shortId(8));

$website->appendTemplateByFile(THEME_DIR . 'common/header.html');

$website->appendTemplateByFile(THEME_DIR . 'common/sidebar.html');
$website->appendTemplateByFile(THEME_DIR . 'common/topbar.html');

$website->appendTemplateByFile(THEME_DIR . 'class/user/list.html');

$website->appendTemplateByFile(THEME_DIR . 'common/footer.html');

$website->rend();
