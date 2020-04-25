<?php

namespace APS;

/**
 * 网站
 * Website
 *
 * 网站模块用于解析网站的数据,渲染网站数据和模板、处理网站路由
 *
 *
 * 网站静态数据包含:
 *
 * SiteDir      网站所在物理路径
 * SitePath     网站所在URL路径
 * ThemeDir     主题物理路径
 * ThemePath    主题URL路径
 * StaticPath   静态资源URL路径
 * Params       页面参数
 * Query        页面参数URL Query形式
 * Lang         语言 i18n ID
 *
 * 动态数据包含:
 * Data         主数据
 * User         用户
 *              isVerified  是否登录成功
 *              userid      用户id
 *              avatar      头像
 *              username    用户名
 *              nickname    昵称
 *              groupid     用户组id
 *              character   用户组角色
 *              level       用户组级别
 *
 * @package APS
 */
class Website extends ASRoute {

    /**
     * 静态数据
     * @var array
     */
    public $constants = [];

    /**
     * 当前用户对象 User Object
     * @var \APS\User
     */
    public $user;

    /**
     * 用户数据 User information data
     * @var array
     */
    public $userData;

    /**
     * 用于最终渲染的页面数据
     * Page Data For final render
     * @var array
     */
    public $data = [];

    /**
     * 页面模板
     * @var string
     */
    public $html_template;

    protected $scope = 'website';

    public function __construct( string $pathFormat )
    {
        parent::__construct($pathFormat, 'HTML');

        $theme = getConfig("theme",'WEBSITE') ?? 'default';
        $sitePath = getConfig('SITE_PATH') ?? '/';
        $this->constants = [
            'SiteDir'   => SITE_DIR,
            'SitePath'  => $sitePath,
            'ThemeDir'  => SITE_DIR."{$theme}/",
            'ThemePath' => "{$sitePath}website/themes/{$theme}/",
            'StaticPath'=> getConfig('STATIC_PATH') ?? "{$sitePath}website/static/",
            'Params'    => $this->params,
            'Query'     => $this->querys,
            'Lang'      => _I18n()->currentLang(),
            'title'     => getConfig('title','WEBSITE') ?? 'AppSite',
            'siteLogo'  => getConfig('logoUrl','WEBSITE'),
            'siteLogoW' => getConfig('logoW','WEBSITE'),
            'siteLogoH' => getConfig('logoH','WEBSITE'),
        ];

        $this->scope = (getConfig('id','WEBSITE') ?? 'APPSITE') . '_w';
        $this->initUser();
    }

    public function setConstant( string $key, $value ){
        $this->constants[$key] = $value;
    }

    public function setTitle( string $title ){
        $this->constants['title'] = $title;
    }

    public function setKeywords( string $keywords ){
        $this->constants['keywords'] = $keywords;
    }

    public function setDescription( string $description ){
        $this->constants['description'] = $description;
    }

    public function appendTemplate(string $htmlString ){

        $this->html_template .= $htmlString;
    }

    public function initUser( User $user = null){

        if( isset($user) ){
            $this->user = $user;
        }else{

            # 优先SESSION
            session_start();
            if( isset($_SESSION[$this->scope.'_userid']) ){
                $this->user = User::fromSession($this->scope);
                if( !$this->user->isVerified() ){
                    $this->user->removeFromSession($this->scope);
                }
            }

            # 检查HeaderUser
            if( Network::getHeaderParam('userid') ){
                $this->user = User::fromHeader();
            }
        }

        if( isset($this->user) ){

            $this->userData = [
                'isVerified' => $this->user->isVerified(),
                "userid"     => $this->user->userid,
            ];

            if( $this->user->isVerified() ){
                $this->userData['avatar'   ]  = $this->user->detail['avatar'] ?? getConfig('defaultAvatar','WEBSITE');
                $this->userData['username' ]  = $this->user->detail['username'];
                $this->userData['nickname' ]  = $this->user->detail['nickname'];
                $this->userData['groupid'  ]  = $this->user->getGroupId();
                $this->userData['character']  = $this->user->getGroupCharacter();
                $this->userData['level'    ]  = $this->user->getGroupLevel();
            }
        }
    }



    public function blendMenuAccess( array $menuAccessConfig, string $toSubDataKey = 'menu' ){

        $menuAccess = [];

        if( $this->user->isVerified() ){
            if( !empty($menuAccessConfig) ){

                foreach ( $menuAccessConfig as $key => $accessOptions ){

                    if(
                        # 超级管理员全开模式     Unlimited Mode by Super Admin
                        $this->user->getGroupCharacter() == 'super' ||

                        # 用户组授权模式       User Group MenuAccess Mode
                        in_array($key,$this->user->getMenuAccess()) ||

                        # 分段检测模式        Limit Check Mode
                        (
                            (!isset($accessOptions['groupid'])   || $this->user->getGroupId() == $accessOptions['groupid'] ) &&
                            (!isset($accessOptions['character']) || $this->user->getGroupCharacter() == $accessOptions['character'] ) &&
                            (!isset($accessOptions['level'])     || $this->user->getGroupLevel() >= $accessOptions['level'] )  &&
                            true
                        )
                    ){
                        $menuAccess[$key] = true;
                    }
                }
            }
        }
        $this->setSubData($toSubDataKey,$menuAccess);
    }


    public function blendMenuAccessByFile( string $absolutePathOfMenuAccessConfigFile, string $toSubDataKey = 'menu' ){

        $this->blendMenuAccess(file_exists($absolutePathOfMenuAccessConfigFile) ? include $absolutePathOfMenuAccessConfigFile : [],$toSubDataKey);
    }

    public function setMenuActive( array $actives ){
        $menuActives = [];
        foreach ( $actives as $i => $key ){
            $menuActives[$key] = ['active'=>'active','expended'=>'true','show'=>'show'];
        }
        $this->setSubData('menuActive',$menuActives);
    }


    /**
     * 生成翻页器结构
     * structPager
     * @version  1.1
     * @param    int                      $page           [当前页数 从1开始  Current page number ( Begin on 1 )]
     * @param    int                      $size           [单页长度 Length per page]
     * @param    int                      $total          [内容总数 Total content]
     * @param    array                    $params         [页面参数 Parameters of page]
     * @return   array | null
     *
     * EG:
     *	'max'=>3,
     *	'page'=>2,
     *	'size'=>12,
     *	'count'=>40,
     *	'list'=>[
     *		['page'=>1],['page'=>2,'active'=>1],['page'=>3]
     *	],
     *	'prev'=>1,
     *	'next'=>3,
     *	'first'=>?, # 当前页数距离首页超过 QueueLength/2 页
     *	'last'=>?,  # 当前页数距离末页超过 QueueLength/2 页
     *
     */
    public function structPager( int $page, int $size, int $total, array $params = [] ){

        $QueueLength = 16;

        if( $size<1 || $page<1 ){ return null; }
        if( $total==0 ){ return null; }

        $pager['max']    = (int)(($total-1)/$size) +1;
        $pager['page']   = $page;
        $pager['size']   = $size;
        $pager['total']  = $total;

        $pager['list'] = [];

        if($page>1){ $pager['prev'] = $page-1; }
        if($page<$pager['max']){ $pager['next'] = $page+1; }

        if($pager['max']<=$QueueLength){
            for ($i=1; $i <= $pager['max'] ; $i++) {
                $nav = array_merge($params,['page'=>$i]);
                if($page===$i){ $nav['current']=1; }
                $pager['list'][] = $nav;
            }
        }else{
            if($page>$QueueLength/2){
                $pager['first']= 1;
            }

            $begin = $page>$QueueLength/2 ? $page-$QueueLength/2 : 1;
            $end   = $page<$pager['max']-$QueueLength/2 ? $page+$QueueLength/2 : $pager['max'] ;

            for ($i=$begin; $i <= $end ; $i++) {
                $nav = array_merge($params,['page'=>$i]);
                if($page===$i){ $nav['current']=1; }
                $pager['list'][] = $nav;
            }

            if($pager['max']-$page>$QueueLength/2){
                $pager['last'] = $pager['max'];
            }
        }
        return $pager;

    }


    /**
     * 强制登录
     * requireUser
     * @param  string  $redirectTo
     */
    public function requireUser( string $redirectTo ){

        if( !$this->user ||  !$this->user->isVerified() ){
            $this->redirectTo($redirectTo);
        }
    }

    /**
     * 强制要求用户组
     * requireGroup
     * @param  string  $groupId
     * @param  string  $redirectTo
     */
    public function requireGroup( string $groupId, string $redirectTo ){
        $this->requireUser( $redirectTo );
        if( $this->userData['groupid'] !== $groupId ){
            $this->redirectTo($redirectTo);
        }
    }

    /**
     * 强制要求用户组级别
     * requireGroupLevel
     * @param  int     $level
     * @param  string  $redirectTo
     */
    public function requireGroupLevel( int $level, string $redirectTo ){
        $this->requireUser( $redirectTo );
        if( $this->userData['level'] < $level ){
            $this->redirectTo($redirectTo);
        }
    }

    /**
     * 强制要求用户角色
     * requireGroupCharacter
     * @param  string|array  $character
     * @param  string        $redirectTo
     */
    public function requireGroupCharacter( $character, string $redirectTo ){
        $this->requireUser( $redirectTo );
        if( gettype($character) == 'string' ){
            if( $this->userData['character']!=='super' && $this->userData['character'] !== $character ){
                $this->redirectTo($redirectTo);
            }
        }else if( gettype($character)=='array' ){
            if ( !in_array($this->userData['character'],$character) ){
                $this->redirectTo($redirectTo);
            }
        }
    }


    /**
     * 通过文件追加模板文件
     * appendMoudleByFile
     * @param  string  $absoluteFilePath    绝对路径
     */
    public function appendTemplateByFile(string $absoluteFilePath ){

        if( file_exists($absoluteFilePath) ){

            $this->appendTemplate( file_get_contents($absoluteFilePath) );
        }
    }

    /**
     * 设置数据
     * setData
     * 相同字段将会覆盖
     * The same field will be overwritten
     * @param  array  $data
     */
    public function setData( array $data ){
        $this->data = array_merge($this->data,$data);
    }

    /**
     * 设置子数据
     * setSubData
     * @param  string  $key
     * @param  mixed   $value
     */
    public function setSubData( string $key, $value ){
        $this->data[$key] = $value;
    }

    /**
     * 渲染 数据模板 到浏览器
     * rend data & template to browser
     */
    public function rend(){

        $this->setSubData('constants',$this->constants);
        $this->setSubData('userData',$this->userData);
        $this->result = ASResult::shared(0,'Website Rend',Mixer::mix($this->data,$this->html_template));
        $this->export();
    }


    /**
     * 跳转404页
     * to 404 page
     */
    public function to404(){

        $this->redirectTo("404/");

    }

    /**
     * 重定向到页面
     * redirectTo specific page
     * @param  string      $page    相对定位(不支持站外跳转)
     */
    public function redirectTo( string $page ){

        header("location:".$this->constants['SitePath'].$page );
        exit;
    }


    public function export(){

        parent::export(); // TODO: Change the autogenerated stub

    }
}