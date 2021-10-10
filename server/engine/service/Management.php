<?php
/**
 * 管理后台基础控制器
 * Management website Controller based on Website Class
 * Managerment.php
 */

namespace APS;


class Management extends Website
{
    const scope = RouteScopeManagement;
    const rootPath  = ManagementDefaultRootPath;
    const theme     = ManagementDefaultTheme;
    const defaultID = ManagementDefaultID;

//    public function __construct(string $pathFormat)
//    {
//        parent::__construct($pathFormat);
//
//        $this->constants
//            ->setRootPath('manager')
//            ->setTheme(getConfig('theme',"MANAGER")??'stisla')
//            ->setTitle(getConfig('title','MANAGER') ?? 'appsite')
//            ->setLogo(getConfig('logoUrl','MANAGER'),getConfig('logoW','MANAGER'), getConfig('logoH','MANAGER'))
//            ;
//        $this->scope = (getConfig('id','MANAGER') ?? 'APPSITE') . '_m';
//        $this->initUser();
//    }

}