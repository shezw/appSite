<?php

namespace APS;

class WebsiteConstants{

    public $SiteDir;
    public $SitePath;

    public $RootPath;

    public $Theme;
    public $ThemeDir;
    public $ThemePath;

    public $StaticPath;
    public $Params;
    public $Query;

    public $Lang;

    public $Title;
    public $SiteTitle;
    public $Description;

    public $SiteLogo;
    public $SiteLogoW;
    public $SiteLogoH;

    public function __construct(  string $rootPath = WebsiteDefaultRootPath, string $theme = WebsiteDefaultTheme, string $scope = RouteScopeWebsite )
    {
        $this->SiteDir = SITE_DIR;
        $this->SitePath = getConfig('SITE_PATH') ?? '/';
//        $this->StaticPath = $this->SitePath . 'static/';

        $this->RootPath = $rootPath;

        $this->StaticPath = getConfig('STATIC_PATH') ?? "{$this->SitePath}website/static/";
//        $this->Title  = getConfig('title',$scope);
        $this->SiteTitle  = getConfig('title',$scope);

        $this->Theme = $theme;
        $this->ThemeDir = SITE_DIR."{$this->Theme}/";
        $this->ThemePath = "{$this->SitePath}{$this->RootPath}/themes/{$this->Theme}/";

        $this->SiteLogo   = getConfig('logoUrl',$scope) ?? $this->StaticPath.'appsiteJS/images/logo480.png';
        $this->SiteLogoW  = getConfig('logoW',$scope) ?? $this->StaticPath.'appsiteJS/images/logo-W.png';
        $this->SiteLogoH  = getConfig('logoH',$scope) ?? $this->StaticPath.'appsiteJS/images/logo-H.png';

        $this->Lang = _I18n()->currentLang();


//        var_dump($this);
    }

    public function setRootPath( string $rootPath ):WebsiteConstants
    {
        $this->RootPath = $rootPath;
        return $this;
    }

    public function setTheme( string $theme ):WebsiteConstants
    {
        $this->Theme = $theme;
        $this->ThemeDir = SITE_DIR."{$this->Theme}/";
        $this->ThemePath = "{$this->SitePath}{$this->RootPath}/themes/{$this->Theme}/";

        return $this;
    }


    public function setTitle( string $title ):WebsiteConstants
    {
        $this->Title = $title;
        return $this;
    }

    public function setDescription( string $desc ): WebsiteConstants
    {
        $this->Description = $desc;
        return $this;
    }

    public function setLogo( string $logo = null, string $logoW = null, string $logoH = null ):WebsiteConstants
    {
        $this->SiteLogo = $logo ?? $this->SiteLogo;
        $this->SiteLogoW = $logoW ?? $this->SiteLogoW;
        $this->SiteLogoH = $logoH ?? $this->SiteLogoH;

        return $this;
    }

    public function setParams( array $params = null ): WebsiteConstants
    {
        $this->Params = $params;
        return $this;
    }

    public function setQuery( string $query = null ): WebsiteConstants
    {
        $this->Query = $query;
        return $this;
    }

    public function toArray( ): array{
        return [
            'SiteDir' => $this->SiteDir,
            'SitePath' => $this->SitePath,

            'RootPath' => $this->RootPath,

            'Theme' => $this->Theme,
            'ThemeDir' => $this->ThemeDir,
            'ThemePath' => $this->ThemePath,

            'StaticPath' => $this->StaticPath,
            'Params' => $this->Params,
            'Query' => $this->Query,

            'Lang' => $this->Lang,

            'Title' => $this->Title,
            'SiteTitle' => $this->SiteTitle,
            'Description' => $this->Description,

            'SiteLogo' => $this->SiteLogo,
            'SiteLogoW' => $this->SiteLogoW,
            'SiteLogoH' => $this->SiteLogoH,

        ];
    }

}