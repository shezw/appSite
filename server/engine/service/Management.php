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

        $this->setConstant('ThemePath',  $this->constants['SitePath'].'manager/themes/'. (getConfig('theme',"MANAGER")??'default') .'/');
        $this->setConstant('siteLogo', getConfig('logoUrl','MANAGER'));
        $this->setConstant('siteLogoW', getConfig('logoW','MANAGER'));
        $this->setConstant('siteLogoH', getConfig('logoH','MANAGER'));
        $this->setConstant('rootPath', getConfig('rootPath','MANAGER') ?? '/manager' );
        $this->scope = (getConfig('id','MANAGER') ?? 'APPSITE') . '_m';
        $this->initUser();
    }

}