<?php

namespace  APS;

/**
 * 用户
 * User
 * @package APS\service\User
 */
class User extends ASModel{

    public $userid;
    public $token;
    public $scope;

    public $detail;

    private $inited = false;
    private $acquired = false;
    private $verified = false;

    private $securityid;

    protected static $rds_auto_cache = false;
    /**
     * 用户权限
     * @var \APS\Access
     */
    public  $access;

    function __construct( string $userid = null, string $token = null, string $scope = null ){

        parent::__construct();

        $this->userid = $userid ?? 'GUEST';
        $this->token  = $token ?? 'GUESTTOKEN';
        $this->scope  = $scope ?? 'common';

        $this->init();
    }

    /**
     * 全局单例用户
     * shared
     * @param  string  $userid
     * @param  string  $token
     * @param  string  $scope
     * @return User
     */
    public static function shared( string $userid = null, string $token = null, string $scope = null ): User{

        if ( !isset($GLOBALS['User']) ){
            $GLOBALS['User'] = new User( $userid,$token,$scope );
        }
        return $GLOBALS['User'];
    }

    /**
     * 从网络请求Header信息中初始化
     * Init User from header
     * @param  bool  $setToGlobal 设为全局用户
     * @return User
     */
    public static function fromHeader( bool $setToGlobal = true ): User{

        $user = new User( Network::getHeaderParam('userid'),Network::getHeaderParam('token'),Network::getHeaderParam('scope') );
        if( $setToGlobal ){ $GLOBALS['User'] = $user; }
        return $user;
    }

    /**
     * 从SESSION中初始化用户
     * Init User from session
     * @param  string  $prefix
     * @param  bool    $setToGlobal  设为全局用户
     * @return User
     */
    public static function fromSession( string $prefix = 'website', bool $setToGlobal = true ): User{
        session_start();
        $user = new User( $_SESSION["{$prefix}_userid"],$_SESSION["{$prefix}_token"],$_SESSION["{$prefix}_scope"] );
        if( $setToGlobal ){ $GLOBALS['User'] = $user; }
        return $user;
    }

    /**
     * 将当前用户缓存到Session中
     * Temp Current User to Session
     * @param  string  $prefix
     */
    public function toSession( string $prefix = 'website' ){
        session_start();
        $_SESSION["{$prefix}_userid"] = $this->userid;
        $_SESSION["{$prefix}_token"] = $this->token;
        $_SESSION["{$prefix}_scope"] = $this->scope;
    }

    /**
     * 从缓存中移除用户信息
     * removeFromSession
     * @param  string  $prefix
     */
    public function removeFromSession( string $prefix = 'website' ){
        session_start();
        $_SESSION["{$prefix}_userid"] = null;
        $_SESSION["{$prefix}_token"] = null;
        $_SESSION["{$prefix}_scope"] = null;
    }

    private function init(){

        if($this->inited){ return ;}

        $this->inited = true;
        $this->securityid = Encrypt::shortId(16);
        $this->access = new Access( $this );
    }

    public function getSecurityid(){
        return $this->securityid;
    }

    public function confirmVerify( string $securityid ){

        if( $securityid == $this->securityid ){
            $this->verified = true;
            $this->acquire();
        }
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }


    /**
     * 请求用户数据到对象中
     * acquire user information
     * @param  bool  $forcedRefresh  强制刷新
     * @return ASResult
     */
    private function acquire( bool $forcedRefresh = false ):ASResult{

        if( $this->acquired && !$forcedRefresh ){ return $this->success(); }

        $this->sign("USER->acquire");

        $userDetail = $this->fullDetail();

        if($userDetail->isSucceed()){
            $this->detail = $userDetail->getContent();

        }else{
            return $this->error(510,i18n('SYS_GET_FAL'));
        }
        $this->acquired = true;
        return $this->success(i18n('SYS_GET_SUC'));
    }


    public function isGuest(): bool
    {
        return $this->userid === 'GUEST' || !isset($this->userid);
    }

    public function identify(){

        if( !$this->verified ){
            _ASRoute()->exit(
                ASResult::shared(9999,i18n('AUTH_VER_FAL'),null,'User->identify')
            );
        }

    }

    public function refreshToken(): Access
    {

        $this->identify();

        return $this->access;

    }

    public function systemAuthorize( string $userid, string $scope = 'common'): ASResult
    {

        $this->userid = $userid;
        $this->scope  = $scope;
        $Access = new Access($this);

        return $Access->authorize();
    }



    /**
     * 新建用户账户
     * add
     * @param  array  $params
     * @return ASResult
     */
    public function add( array $params ): ASResult
    {

        $params[static::$primaryid] = isset($params[static::$primaryid]) ? $params[static::$primaryid] : Encrypt::shortId(8);

        $accountParams = Filter::purify($params,static::$addFields); // 使用字段数据进行过滤

        $accountParams['status'] = $accountParams['status'] ?? 'enabled';

        if(isset($accountParams['password'])){
            $ENCR = new Encrypt(4);
            $accountParams['password'] = $ENCR->hashPassword($accountParams['password']);
        }

        //检测不能同时为空的选项
        if(
            !isset($accountParams['email'])&&
            !isset($accountParams['mobile'])&&
            !isset($accountParams['username'])&&
            !isset($params['appleUUID'])&&
            !isset($params['wechatid'])
        ){
            return $this->take($params)->error(504,i18n('USR_UI_REQ'),'User->add');
        }

        if( isset($accountParams['username'])  && ( static::exist('username',$accountParams['username']))){
            return $this->take($accountParams['username'])->error(600,i18n('USR_UN_EXT'),'User->add');
        }
        if( isset($accountParams['email'])     && ( static::exist('email',$accountParams['email']))){
            return $this->take($accountParams['email'])->error(600,i18n('USR_EM_EXT'),'User->add');
        }
        if( isset($accountParams['mobile'])    && ( static::exist('mobile',$accountParams['mobile']))){
            return $this->take($accountParams['mobile'])->error(600,i18n('USR_MB_EXT'),'User->add');
        }
        if( isset($params['wechatid'])  && ( static::exist('wechatid',$params['wechatid']))){
            return $this->take($params['wechatid'])->error(600,i18n('USR_WE_EXT'),'User->add');
        }
        if( isset($params['appleUUID'])  && ( static::exist('appleUUID',$params['appleUUID']))){
            return $this->take($params['appleUUID'])->error(600,i18n('USR_AP_EXT'),'User->add');
        }

        $this->DbAdd($accountParams);
        $this->setId($accountParams[static::$primaryid]);
        $this->record('USER_ADD','User->add');

        if(!$this->result->isSucceed()){ return $this->feedback(); }

        // 初始化用户信息/钱包

        $userInfo   = new UserInfo( $accountParams['userid'] );
        $userPocket = new UserPocket( $accountParams['userid'] );

        $userInfo->init( $params );
        $userPocket->init( $params );

        return $this->take($accountParams['userid'])->success(i18n('USR_ADD_SUC'),'User->add');

    }

    /**
     * @param array $params
     * @param string $uid
     * @return ASResult
     */
    public function update(array $params, string $uid ): ASResult
    {

        $accountParams = Filter::purify($params,static::$updateFields);
        $infoParams = Filter::purify($params,UserInfo::$updateFields);

        $pass = $params['password'] ?? null;

        if($pass){
            $ENC = new Encrypt(4);
            $accountParams['password'] = $ENC->hashPassword($pass);
        }

        if( isset($accountParams['username'])  && ( $this->isConflict('username',$accountParams['username'],$uid))){
            return $this->take($accountParams['username'])->error(600,i18n('USR_UN_EXT'),'User->add');
        }
        if( isset($accountParams['email'])     && ( $this->isConflict('email',$accountParams['email'],$uid))){
            return $this->take($accountParams['email'])->error(600,i18n('USR_EM_EXT'),'User->add');
        }
        if( isset($accountParams['mobile'])    && ( $this->isConflict('mobile',$accountParams['mobile'],$uid))){
            return $this->take($accountParams['mobile'])->error(600,i18n('USR_MB_EXT'),'User->add');
        }
        if( isset($params['wechatid']) && ( $this->isConflict('wechatid',$params['wechatid'],$uid))){
            return $this->take($params['wechatid'])->error(600,i18n('USR_WE_EXT'),'User->add');
        }
        if( isset($params['appleUUID']) && ( $this->isConflict('appleUUID',$params['appleUUID'],$uid))){
            return $this->take($params['appleUUID'])->error(600,i18n('USR_AP_EXT'),'User->add');
        }

        if (count($accountParams)<1 && count($infoParams)<1) { return $this->take($params)->error(603,i18n('SYS_PARA_REQ'),'ITEM::update'); }

        $conditions = static::$primaryid."='$uid'";

        $this->DbUpdate($accountParams,$conditions);
        $this->setId($uid);
        $this->record('ITEM_UPDATE','ITEM::update');

        if (!empty($infoParams)) {
            $userInfo = new UserInfo( $uid );
            $userInfo->update( $infoParams, $uid );
        }

        # 删除历史缓存
        if( $this->result->isSucceed() ){

            $this->getRedis()->isEnabled()
            && $this->getRedis()->clear('USER',$this->userid);
        }

        return $this->feedback();

    }


/* 获取数据部分 Get Data */


    /**
     * 统计vip用户数
     * countVip
     * @param  array  $filters  筛选条件
     * @return ASResult
     */
    public function countVip( array $filters = [] ): ASResult
    {

        $filters['vip'] = $filters['vip'] ?? 1;

        return $this->count($filters);

    }

    /**
     * 获取vip列表
     * listVip
     * @param  array   $filters   筛选条件
     * @param  int     $page
     * @param  int     $size
     * @param  string  $sort
     * @return ASResult
     */
    public function listVip( array $filters = [], int $page=1, int $size=20, string $sort = 'vip DESC, vipexpire DESC, createtime DESC' ): ASResult
    {

        $filters['vip'] = $filters['vip'] ?? 1;

        if ($this->countVip($filters)->getContent()==0){
            return $this->error(400,i18n('USR_NON'),'User->list');
        }

        return $this->list($filters,$page,$size,$sort);
    }

    /**
     * 验证密码
     * checkPassword
     * @param  string  $password
     * @param  string  $userid
     * @return ASResult
     */
    public function checkPassword( string $password , string $userid ): ASResult
    {

        $this->sign('User->checkPassword');

        $detail = static::common()->detail($userid);

        if( !$detail->isSucceed() ){ return $this->error( 401, i18n('USR_NOT_EXISTS') ); }

        $userDetail = $detail->getContent();

        if( $userDetail['status'] == 'block' ){ return $this->error(9001,'AUTH_BLOCK'); }

        $ENCRYPT = new Encrypt(4);
        if( !$ENCRYPT->checkPassword( $password,$userDetail['password'] ) ){
            return $this->error(1,i18n('SYS_PAS_FAL'));
        }
        return $this->success();
    }


    // 检测用户授权是否正确
    public function checkAuth( string $status=null, int $level = 0 ): ASResult
    {

//        if( !$this->access-> )
//        $CHECK = ACCESS::checkToken($userid,$token);
//        if ( !isset($userid) || $userid=='false' || !RESULT::isSucceed($CHECK) ) {
//            return RESULT::feedback($CHECK['status']==308 ? 9998 : 9999,['AUTH_VER_FAL'],$userid,'checkAuth');
//        }

        // status check
        if(!isset($status) && $level==0 ){ return $this->take($this->userid)->success(i18n('AUTH_SUC'),'User->checkAuth'); }
        if ($this->detail['status']!=='enabled') {
            return $this->error(9001,i18n('AUTH_BLOCK'),'User->checkAuth');
        }

        if ( $this->getUserInfo('level') < $level ) {
            return $this->error(9900,i18n('AUTH_LEVEL_LOW'),'User->checkAuth');
        }

        return $this->take($this->userid)->success(i18n('AUTH_SUC'),'User->checkAuth');
    }

    /**
     * 检测用户权限级别
     * @param int $level
     * @return ASResult
     */
    public function checkLevel(int $level = 0 ): ASResult
    {

        if ( $this->getUserInfo('level') < $level ) {
            return $this->error(9900,i18n('AUTH_LEVEL_LOW'),'User->checkLevel');
        }

        return $this->take($this->userid)->success(i18n('AUTH_SUC'),'User->checkLevel');
    }


    /**
     * 通过信息查询用户
     * searchUseridByInfo
     * @param  string  $info
     * @return ASResult
     */
    public function searchUserByInfo( string $info ):ASResult{

        $searchUser = $this->getDB()->maybeSearch(['username'=>$info,'mobile'=>$info,'email'=>$info], static::$table,
            static::$detailFields);

        if ( !$searchUser->isSucceed() ) { return $this->error(400,i18n('USR_NON'),'User->searchUserByInfo'); }

        return $this->take($searchUser->getContent()[0]['userid'])->success();
    }

    /**
     * 查询组ID
     * getGroupId
     * @return string|null
     */
    public function getGroupId(): string {

        $this->acquire();
        return $this->detail['group']['uid'];
    }

    /**
     * 查询用户组级别
     * getGroupLevel
     * @return int
     */
    public function getGroupLevel():int{
        $this->acquire();
        return $this->detail['group']['level'] ?? 0;
    }

    /**
     * 查询用户组角色分类
     * getGroupCharacter
     * @return string|null
     */
    public function getGroupCharacter(): string {
        $this->acquire();
        return $this->detail['group']['type'];
    }

    /**
     * 查询用户组所有的菜单访问权限
     * getMenuAccess
     * @return array
     */
    public function getMenuAccess():array{
        $this->acquire();
        return $this->detail['group']['menuaccess'] ?? [];
    }

    /**
     * 联合查询用户信息
     * fullDetail
     * @return ASResult
     */
    public function fullDetail(): ASResult
    {

        $hashParams = ['USER',$this->userid];

        if( static::$rds_auto_cache && $this->getRedis()->isEnabled() && $this->getRedis()->has($hashParams) ){
            $DETAIL = $this->getRedis()->read($hashParams);
        }else{

            $joinParamsArray = [
                JoinParams::init('APS\UserInfo')->get(UserInfo::$detailFields)->asSubData('info'),
                JoinParams::init('APS\UserPocket')->get(UserPocket::$detailFields)->asSubData('pocket'),
                JoinParams::init('APS\UserGroup')->get(UserGroup::$detailFields)->equalTo('user_account.groupid')->asSubData('group'),
            ];
            $DETAIL = $this->joinDetail($this->userid,null,$joinParamsArray);
            if( $DETAIL->isSucceed() ){

                $this->getRedis()->isEnabled()
                && $this->getRedis()->cache($hashParams,$DETAIL,12*3600)
                && $this->getRedis()->track('USER',$this->userid,$hashParams);
            }
        }
        return $DETAIL;
    }

    /** 获取数据END **/


    /** 状态查询 **/

    /**
     * 是否存在对应用户
     * exist
     * @param  string  $key
     * @param  mixed   $value
     * @return bool
     */
    public static function exist( string $key, $value ):bool{

        $countUser = _ASDB()->count(in_array($key,UserInfo::$countFilters) ? UserInfo::$table : User::$table,[$key=>$value]);
        return $countUser->getContent() > 0;
    }


    /**
     * 是否和其他用户信息冲突
     * isConflict to other user
     * @param    string                   $key            字段
     * @param    mixed                    $value          值
     * @param    string                   $userid         当前用户ID
     * @return   boolean
     */
    public function isConflict( string $key, $value, string $userid ):bool{

        $USERID = $this->getUserid($key,$value);

        if(!$USERID->isSucceed()){ return false; }

        if($USERID->getContent()==$userid){ return false; }

        return true;
    }


    /**
     * 通过信息查询相关用户id
     * getUserid by specific key & value
     * @param  string  $key
     * @param  mixed   $value
     * @return ASResult
     */
    public function getUserid( string $key, $value ):ASResult{

        if( in_array($key, USERINFO::$addFields) ){
            $infoModel = new UserInfo();
            $LIST = $infoModel->list([$key=>$value],1,1);
        }else{
            $LIST = $this->list([$key=>$value],1,1);
        }

        if ( !$LIST->isSucceed()){ return $this->error(400,i18n('USR_NON'),'User->getUserid'); }

        return $this->take($LIST->getContent()[0]['userid'])->success(i18n('USR_GET_SUC'),'User->getUserid');
    }

    /**
     * 用户是否属于分组
     * isInGroup
     * @param  string  $groupid  分组id
     * @return bool
     */
    public function isInGroup( string $groupid ) :bool {

        $this->acquire();

        return $this->detail['group']['uid'] == $groupid;
    }


    /**
     * 查询是否超级管理员
     * isSuper
     * @param  string  $userid
     * @return bool
     */
    public function isSuper( string $userid ): bool
    {

        return $this->isInGroup('900');
    }

    /**
     * 查询是否管理员
     * isAdmin
     * @param  string  $userid
     * @return bool
     */
    public function isAdmin( string $userid ): bool
    {

        return $this->isInGroup('800');
    }

    // 查询是否网站编辑
    public function isEditor( string $userid ): bool
    {

        return $this->isInGroup('400');
    }

    // 查询是否专家/讲师
    public function isAuthor( string $userid ): bool
    {

        return $this->isInGroup('300') || $this->isInGroup('301') || $this->isInGroup('302') || $this->isInGroup('303');
    }

    /**
     * 查询是否vip
     * isVip
     * @param  int  $vip
     * @return bool
     */
    public function isVip( int $vip = 1 ):bool{

        return $this->getUserInfo('vip') >= $vip ;
    }

    /**
     * 设置vip
     * setVip
     *
     * 重复设置默认追加有效期
     *
     * @param  int     $duration
     * @param  int     $viplevel
     * @return ASResult
     */
    public function setVip( int $duration, int $viplevel=1 ): ASResult
    {

        $vipexpire = $this->getUserInfo('vipexpire');
        $vipexpire = $vipexpire>0 ? $vipexpire : time();
        return $this->update(['vip'=>$viplevel,'vipexpire'=>$vipexpire+$duration],$this->userid);
    }

    /**
     * 获取vip过期时间
     * getVipExpire
     * @return mixed
     */
    public function getVipExpire(){
        return $this->getUserInfo('vipexpire');
    }

    /**
     * 取消vip身份
     * undoVip
     * @param  int  $time
     * @return ASResult
     */
    public function undoVip( int $time = 0 ): ASResult
    {

        return $this->updateUserInfo(['vipexpire'=>$time]);
    }

    /**
     * 获取用户信息
     * getUserInfo
     * @param  string  $key
     * @return mixed
     */
    public function getUserInfo( string $key ){

        $this->acquire();
        return $this->detail['userinfo'][$key];
    }

    /**
     * 更新用户信息表
     * updateUserInfo
     * @param  array  $data
     * @return ASResult
     */
    public function updateUserInfo( array $data ): ASResult
    {

        $infoModel = new UserInfo();
        return $infoModel->update( $data, $this->userid );
    }




    public static $table     = "user_account";
    public static $primaryid = "userid";
    public static $addFields = [
        'userid','username','password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status',
    ];
    public static $updateFields = [
        'password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status',
    ];
    public static $detailFields = [
        'userid','username','email','mobile','password',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status','createtime','lasttime'
    ];
    public static $publicDetailFields = [
        'userid','username','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status','createtime','lasttime'
    ];
    public static $overviewFields = [
        'userid','username',
        'nickname','avatar','description','introduce',
        'groupid','areaid','createtime','lasttime'
    ];
    public static $listFields = [
        'userid','username','email','mobile',
        'nickname','avatar','cover','description',
        'groupid','gender','areaid','status','createtime','lasttime'
    ];
    public static $publicListFields = [
        'userid','username',
        'nickname','avatar','description',
        'groupid','gender','areaid','status','createtime','lasttime'
    ];
    public static $countFilters = [
        'userid','username','email','mobile',
        'nickname','gender','groupid','areaid','status','createtime','lasttime'
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'birthday'=>'int',
    ];



}