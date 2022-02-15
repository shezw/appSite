<?php

use \APS\ASSetting;

$website->requireLogin('manager/login');
$website->requireGroupLevel(90000,'manager/insufficient');
$website->requireGroupCharacter('manager','manager/insufficient');

$website->setMenuActive(['setting','settingConfig']);
$website->setTitle('Config Management');

$website->setSubData('SystemCfg'     ,ASSetting::common()->listByArray(['scope'=>'IS_NULL'],1,100)->getContent());
$website->setSubData('ManagerCfg'    ,ASSetting::common()->listByArray(['scope'=>'MANAGER'],1,100)->getContent());
$website->setSubData('WebsiteCfg'    ,ASSetting::common()->listByArray(['scope'=>'WEBSITE'],1,100)->getContent());
//$website->setSubData('PrivatesCfg'   ,ASSetting::common()->list(['scope'=>'PRIVATES'],1,100)->getContent());
$website->setSubData('WechatCfg'     ,ASSetting::common()->listByArray(['scope'=>'WECHAT'],1,100)->getContent());
$website->setSubData('AliyunCfg'     ,ASSetting::common()->listByArray(['scope'=>'ALIYUN'],1,100)->getContent());
$website->setSubData('BaiduyunCfg'   ,ASSetting::common()->listByArray(['scope'=>'BAIDUYUN'],1,100)->getContent());
$website->setSubData('PointBonusCfg' ,ASSetting::common()->listByArray(['scope'=>'POINTBONUS_RULES'],1,100)->getContent());
$website->setSubData('SMSModuleCfg'  ,ASSetting::common()->listByArray(['scope'=>'SMS_MODULE_CODE'],1,100)->getContent());
$website->setSubData('SMTPModuleCfg' ,ASSetting::common()->listByArray(['scope'=>'SMTP_MODULE_CODE'],1,100)->getContent());
$website->setSubData('AppleIAPCfg'   ,ASSetting::common()->listByArray(['scope'=>'APPLE_IAP_ITEMS'],1,100)->getContent());

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'common/modal/styleCustomize.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/search.html');
$website->appendTemplateByFile(THEME_DIR.'common/modal/notification.html');

$website->appendTemplateByFile(THEME_DIR.'common/sidebar.html');

$website->appendTemplateByFile(THEME_DIR.'class/config/view.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
