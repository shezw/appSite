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
     * 操作权限要求
     * @var string
     */
    const operationAccessRequirement = "";

    /**
     * 操作作用域
     * Operation Scope
     *
     * ( common, iOS, android, mini-program, website... )
     * @var string
     */
    const operationAccessScope = AccessScope_Common;

    /**
     * 用户组级别要求
     * @var int
     */
    const groupLevelRequirement = 0;

    /**
     * 用户身份要求
     * @var string | array
     */
    const groupCharacterRequirement = "";
    # CharacterTypeGuest        = 'guest';
    # CharacterTypeUser         = 'user';
    # CharacterTypeManager      = 'manager';
    # CharacterTypeSuper        = 'super';

    const scope = ASAPI_Scope_System;

    /**
     * API export mode
     * Valid modes: 'API','ASAPI,   'RAW',  'HTML',  'JSON','javascript'
     * @see "server/engine/constants.php"
     * @var string
     */
    const mode = ASAPI_Mode_ASAPI;

    /**
     * 当前接口访问者
     * @var User
     */
    protected $user;

    protected $params;

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
     * @param User|null  $user
     * @return ASAPI
     */
    public static function systemInit( string $className, $params = null, User $user = null ): ASAPI
    {

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
        if( static::operationAccessRequirement && !$this->user->access->checkOperation( static::operationAccessRequirement,static::operationAccessScope ) ){
            _ASRoute()->exit($this->error(9901,i18n('REQ_ACC_OPERATION'),'ASAPI->checkRequirement'));
        }

        # 检测用户组级别
        if( $this->user->getGroupLevel() < static::groupLevelRequirement ){
            _ASRoute()->exit($this->error(9910,i18n('REQ_ACC_GROUP_LEVEL'),'ASAPI->checkRequirement'));
        }

        # 检测用户组角色
        if( static::groupCharacterRequirement && (
            gettype(static::groupCharacterRequirement) == 'array' ?
            !in_array($this->user->getGroupCharacter(),static::groupCharacterRequirement) :
            $this->user->getGroupCharacter() != static::groupCharacterRequirement )
        ){
            _ASRoute()->exit($this->error(9911,i18n('REQ_ACC_GROUP_CHARACTER'),'ASAPI->checkRequirement'));
        }
    }

    public function runAPI():ASResult{
        if( static::scope != ASAPI_Scope_System ){
            return $this->run();
        }else{
            return $this->error(-1,i18n("SYS_API_NAL"),"ASAPI->runAPI");
        }
    }

    public function run():ASResult{
        return $this->error(-100,i18n('SYS_API_NOT_IMPLEMENT'),'ASAPI->run');
    }


}