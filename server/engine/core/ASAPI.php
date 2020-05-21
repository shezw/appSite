<?php

namespace APS;

/**
 * 通用接口类
 * Abstract API class
 *
 * API接口允许3重限制模式，按照优先级 Operation > Group level > Group character 依次检查。 实现类中无具体值则跳过检查。
 *
 * @package APS\core
 */
abstract class ASAPI extends ASObject{

    /**
     * 当前接口访问者
     * @var \APS\User
     */
    protected $user;

    /**
     * 操作权限要求
     * @var string
     */
    protected static $operationAccessRequirement;

    /**
     * 操作作用域
     * Operation Scope
     *
     * ( common, iOS, android, mini-program, website... )
     * @var string
     */
    protected static $operationAccessScope = 'common';

    /**
     * 用户组级别要求
     * @var int
     */
    protected static $groupLevelRequirement;

    /**
     * 用户身份要求
     * @var string | array
     */
    protected static $groupCharacterRequirement;

    protected $scope = 'system';

    protected $params;

    public $mode = 'ASAPI';

    function __construct( $params = null, User $user = null ){

        parent::__construct();

        $this->params = $params;
        $this->user = $user ?? new User();

        $this->checkRequirement();
    }

    /**
     * 系统内调用 初始化
     * Init by system call
     * @param  string          $className
     * @param  null            $params
     * @param  \APS\User|null  $user
     * @return \APS\ASAPI
     */
    public static function systemInit( string $className, $params = null, User $user = null ){

        $route = explode( '\\', trim($className,'\\') );
        ASRoute::loadAPIFile( $route[0],$route[1] );

        return new $className( $params, $user );
    }


    /**
     * 检查权限
     * checkRequirement
     */
    public function checkRequirement(){

        # 检测操作权限
        if( isset(static::$operationAccessRequirement) && !$this->user->access->checkOperation( static::$operationAccessRequirement,static::$operationAccessScope ) ){
            _ASRoute()->exit($this->error(9901,i18n('REQ_ACC_OPERATION'),'ASAPI->checkRequirement'));
        }

        # 检测用户组级别
        if( isset(static::$groupLevelRequirement) && $this->user->getGroupLevel() < static::$groupLevelRequirement ){
            _ASRoute()->exit($this->error(9910,i18n('REQ_ACC_GROUP_LEVEL'),'ASAPI->checkRequirement'));
        }

        # 检测用户组角色
        if( isset(static::$groupCharacterRequirement)  && (
            gettype(static::$groupCharacterRequirement) == 'array' ?
            !in_array($this->user->getGroupCharacter(),static::$groupCharacterRequirement) :
            $this->user->getGroupCharacter() != static::$groupCharacterRequirement )
        ){
            _ASRoute()->exit($this->error(9911,i18n('REQ_ACC_GROUP_CHARACTER'),'ASAPI->checkRequirement'));
        }
    }

    public function runAPI():ASResult{
        if( $this->scope != 'system' ){
            return $this->run();
        }else{
            return $this->error(-1,i18n("SYS_API_NAL"),"ASAPI->runAPI");
        }
    }

    public function run():ASResult{
        return $this->error(-100,i18n('SYS_API_NOT_IMPLEMENT'),'ASAPI->run');
    }


}