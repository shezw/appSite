<?php

namespace APS;

/**
 * 权限控制
 * Access
 * @package APS\service\Access
 */
class Access extends ASModel{

    private $user;

    private $operations;  # 操作权限数据
    private $permissions; # 所属权数据

    public  $operationsAcquired;
    public  $permissionsAcquired;

    private $securityid;
    private $inited = false;

    /**
     * 实例化
     * _construct
     *
     * 基于userid来实例化权限管理  游客默认无权限
     *
     * @param  \APS\User|null  $user
     */
    function __construct( User $user = null ){

        parent::__construct(true);

        $this->user  = $user;
        $this->securityid = $user->getSecurityid();

        $this->init();
    }

    # 初始化权限
    private function init(){

        if($this->inited){ return ;}

        if( isset($this->user) && $this->user->userid != 'GUEST' ){

            if( $this->checkAuthorize() ){

                $this->user->confirmVerify( $this->securityid );
                // $this->acquire('operations');
                // $this->acquire('permissions');
            }
        }
        $this->inited = true;
    }

//    # 请求权限数据
//    private function acquireOperations(){
//
//        $this->operationsAcquired = true;
//    }
//
//    # 请求物品权限
//    private function acquirePermisions(){
//
//        $this->permissionsAcquired = true;
//    }

    /**
     * 检查是否具有对应操作权限
     * checkOperation
     * @param  string  $requiredOperation
     * @param  string  $scope
     * @return bool
     */
    public function checkOperation( string $requiredOperation, string $scope = 'common'):bool{

        return AccessOperation::common()->canBy( $this->user->getGroupId(),$requiredOperation,$scope );
    }


    /**
     * 授权 颁发token
     * authorize and return token
     * @param  int|null  $duration
     * @return ASResult
     */
    public function authorize( int $duration = Time::THIRTY ):ASResult{

        $hashParams = ['ACCESS_CHECK_TOKEN',$this->user->userid,$this->user->scope];

        $addToken = AccessToken::common()->addToken($this->user->userid,$this->user->scope,$duration);

        if($addToken->isSucceed()){
            $this->_clearSet('ACCESS',$this->user->userid);
            $this->_cache($hashParams,$addToken,Time::DAY);
            $this->_trackCache('ACCESS',$this->user->userid,$hashParams);
        }
        return $addToken;
    }


    /**
     * 更新令牌
     * updateAuthorize
     * @param  int  $duration
     * @return ASResult
     */
    public function updateAuthorize( int $duration = Time::THIRTY ): ASResult
    {

        if( !$this->checkAuthorize() ){
            $this->error(9999,i18n('AUTH_VER_FAL'));
        }
        return $this->authorize($duration);
    }

    /**
     * 查验令牌
     * checkAuthorize
     * @return bool
     */
    private function checkAuthorize(): bool
    {

        $hashParams = ['ACCESS_CHECK_TOKEN',$this->user->userid,$this->user->scope];

        if( $this->_hasCache($hashParams) ){

            if( $this->user->token == $this->_getCache($hashParams)->getContent()){
                return true;
            }
        }

        $checkToken = AccessToken::common()->checkToken($this->user->userid,$this->user->token,$this->user->scope);

        if($checkToken->isSucceed()){

            $this->_cache($hashParams,$checkToken,Time::DAY);
            $this->_trackCache('ACCESS',$this->user->userid,$hashParams);
        }
        return $checkToken->isSucceed();
    }

    # 验证流程开始
    public static function start( string $origin, string $scope, int $duration ){

    }
    # 验证安全防护
    public static function gruad( string $origin, string $scope ){

    }
    # 验证查验
    public static function verify( string $origin, string $scope, string $code ){

    }





}