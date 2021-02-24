<?php

//var_dump($website);
//$website->requireUser('manager/login');
$website->setTitle('Print');

$website->appendTemplateByFile(THEME_DIR.'common/header.html');

$website->appendTemplateByFile(THEME_DIR.'page/print.html');

$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->rend();
