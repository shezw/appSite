<?php
/**
 * Description
 * Managerment.php
 */

namespace APS;


class Management extends Website
{
    protected $scope = 'manager';

    public function __construct(string $pathFormat)
    {
        parent::__construct($pathFormat);

        $sitePath = getConfig('SITE_PATH') ?? '/';
        $staticPath = $sitePath . 'website/static/';

        $this->setConstant('Theme',      (getConfig('theme',"MANAGER")??'stisla') .'/');
        $this->setConstant('ThemePath',  $this->constants['SitePath'].'manager/themes/'. (getConfig('theme',"MANAGER")??'stisla') .'/');
        $this->setConstant('siteTitle', getConfig('title','MANAGER') ?? 'appsite' );
        $this->setConstant('siteLogo', getConfig('logoUrl','MANAGER') ?? $staticPath.'appsiteJS/images/logo480.png' );
        $this->setConstant('siteLogoW', getConfig('logoW','MANAGER') ?? $staticPath.'appsiteJS/images/logo-W.png');
        $this->setConstant('siteLogoH', getConfig('logoH','MANAGER') ?? $staticPath.'appsiteJS/images/logo-H.png');
        $this->setConstant('rootPath', getConfig('rootPath','MANAGER') ?? '/manager' );
        $this->scope = (getConfig('id','MANAGER') ?? 'APPSITE') . '_m';
        $this->initUser();
    }

}