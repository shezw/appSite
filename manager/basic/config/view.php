<?php

use \APS\ASSetting;

$website->requireUser('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$website->setTitle('Config Management');

$website->setSubData('SystemCfg'     ,ASSetting::common()->list(['scope'=>'IS_NULL'],1,100)->getContent());
$website->setSubData('ManagerCfg'    ,ASSetting::common()->list(['scope'=>'MANAGER'],1,100)->getContent());
$website->setSubData('WebsiteCfg'    ,ASSetting::common()->list(['scope'=>'WEBSITE'],1,100)->getContent());
//$website->setSubData('PrivatesCfg'   ,ASSetting::common()->list(['scope'=>'PRIVATES'],1,100)->getContent());
$website->setSubData('WechatCfg'     ,ASSetting::common()->list(['scope'=>'WECHAT'],1,100)->getContent());
$website->setSubData('AliyunCfg'     ,ASSetting::common()->list(['scope'=>'ALIYUN'],1,100)->getContent());
$website->setSubData('BaiduyunCfg'   ,ASSetting::common()->list(['scope'=>'BAIDUYUN'],1,100)->getContent());
$website->setSubData('PointBonusCfg' ,ASSetting::common()->list(['scope'=>'POINTBONUS_RULES'],1,100)->getContent());
$website->setSubData('SMSModuleCfg'  ,ASSetting::common()->list(['scope'=>'SMS_MODULE_CODE'],1,100)->getContent());
$website->setSubData('SMTPModuleCfg' ,ASSetting::common()->list(['scope'=>'SMTP_MODULE_CODE'],1,100)->getContent());
$website->setSubData('AppleIAPCfg'   ,ASSetting::common()->list(['scope'=>'APPLE_IAP_ITEMS'],1,100)->getContent());

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/modal/styleCustomize.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/search.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/notification.html');

$website->setMenuActive(['setting','settingConfig']);
$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');
$website->blendMenuAccessByFile(SITE_DIR.'basic/menu/sidebar.php');

$website->appendTemplateByFile(THEME_DIR.'class/config/view.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
