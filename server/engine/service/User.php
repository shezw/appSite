<?php

namespace  APS;

/**
 * 用户
 * User
 * @package APS\service\User
 */
class User extends ASModel {

    const alias     = 'user';

    const addFields = [
        # Account
        'uid','saasid','username','password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status',
        # Info
        'userid','vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'realstatus',
        # Pocket
        'point','balance','type',
    ];
    const updateFields = [
        # Account
        'password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid',
        # Info
        'vip','vipexpire',
        'gallery',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'status','realstatus',
        # Pocket
        'point','balance','type',
    ];

    const filterFields = [
        # Account
        'uid','username','email','mobile','saasid',
        'nickname','gender','groupid','areaid','status','createtime','lasttime',
        # Info
        'userid','vip','vipexpire',
        'realname','idnumber',
        'country','province','city','company',
        'wechatid','weiboid','qqid','appleUUID','deviceID',
        'realstatus',
        # Pocket
        'point','balance','type',
    ];

    const detailFields = [
        # Account
        'uid','saasid','username','password','email','mobile',
        'nickname','avatar','cover','description','introduce',
        'birthday','gender','groupid','areaid','status',
    ];

    const depthStruct = [
        'point'=>DBField_Int,
        'balance'=>DBField_Int,
        'birthday'=>DBField_TimeStamp,
        'vip'=>DBField_Int,
        'vipexpire'=>DBField_TimeStamp,
        'gallery'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        # UserAccount
        'uid'          =>['type'=>DBField_String,  'len'=>8,   'nullable'=>0,                     'cmt'=>'用户ID 唯一索引',     'idx'=>DBIndex_Unique, ],
        'groupid'      =>['type'=>DBField_String,  'len'=>8,   'nullable'=>0,  'dft'=>'100',      'cmt'=>'用户分组 参考user_group'],
        'areaid'       =>['type'=>DBField_String,  'len'=>8,   'nullable'=>0,  'dft'=>'1',        'cmt'=>'地区id',    'idx'=>DBIndex_Index,],
        'saasid'       =>['type'=>DBField_String,  'len'=>8,   'nullable'=>1,                     'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],

        'username'     =>['type'=>DBField_String,  'len'=>63,  'nullable'=>1,                     'cmt'=>'用户名 账号密码登陆用', 'idx'=>DBIndex_Unique, ],
        'password'     =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'密码 hash salt加密'],
        'email'        =>['type'=>DBField_String,  'len'=>63,  'nullable'=>1,                     'cmt'=>'邮箱 唯一',           'idx'=>DBIndex_Unique, ],
        'mobile'       =>['type'=>DBField_String,  'len'=>24,  'nullable'=>1,                     'cmt'=>'手机 唯一',           'idx'=>DBIndex_Unique, ],
        'nickname'     =>['type'=>DBField_String,  'len'=>63,  'nullable'=>1,                     'cmt'=>'昵称 30字以内'],
        'avatar'       =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'头像 url'],
        'cover'        =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'封面 url'],
        'description'  =>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,                     'cmt'=>'介绍 250字以内'],
        'introduce'    =>['type'=>DBField_RichText,'len'=>-1,  'nullable'=>1,                     'cmt'=>'简介 120字以内'],
        'birthday'     =>['type'=>DBField_Int,     'len'=>11,  'nullable'=>1,                     'cmt'=>'生日 时间戳'],
        'gender'       =>['type'=>DBField_String,  'len'=>16,  'nullable'=>0,  'dft'=>'private',  'cmt'=>'性别 female male private'],

        'status'       =>['type'=>DBField_String,  'len'=>12,  'nullable'=>0,  'dft'=>'enabled',  'cmt'=>'状态 enabled 可以 '],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,                     'cmt'=>'创建时间',   'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,                     'cmt'=>'上一次更新时间', ],

        # UserInfo
        'userid'    =>['type'=>DBField_String,  'len'=>8,    'nullable'=>0,                    'cmt'=>'用户ID 唯一索引','idx'=>DBIndex_Unique, ],
        'gallery'   =>['type'=>DBField_Json,    'len'=>-1,   'nullable'=>1,                    'cmt'=>'相册 JSON ARRAY'],

        'vip'       =>['type'=>DBField_Int,     'len'=>2,    'nullable'=>0,  'dft'=>0,         'cmt'=>'是否vip',      'idx'=>DBIndex_Index,],
        'vipexpire' =>['type'=>DBField_Int,     'len'=>11,   'nullable'=>0,  'dft'=>0,         'cmt'=>'vip过期时间',   'idx'=>DBIndex_Index,],

        'realname'  =>['type'=>DBField_String,  'len'=>63,   'nullable'=>1,                    'cmt'=>'真实姓名 30字以内'],
        'idnumber'  =>['type'=>DBField_String,  'len'=>63,   'nullable'=>1,                    'cmt'=>'身份证号 30字以内'],
        'country'   =>['type'=>DBField_String,  'len'=>24,   'nullable'=>1,                    'cmt'=>'国家 12字以内'],
        'province'  =>['type'=>DBField_String,  'len'=>24,   'nullable'=>1,                    'cmt'=>'省份 12字以内'],
        'city'      =>['type'=>DBField_String,  'len'=>24,   'nullable'=>1,                    'cmt'=>'城市 12字以内'],
        'company'   =>['type'=>DBField_String,  'len'=>63,   'nullable'=>1,                    'cmt'=>'公司 30字以内'],

        'wechatid'  =>['type'=>DBField_String,  'len'=>32,   'nullable'=>1,                    'cmt'=>'微信公众平台openid 默认获取 unionid', 'idx'=>DBIndex_Unique, ],
        'weiboid'   =>['type'=>DBField_String,  'len'=>63,   'nullable'=>1,                    'cmt'=>'微博ID'],
        'appleUUID' =>['type'=>DBField_String,  'len'=>64,   'nullable'=>1,                    'cmt'=>'苹果UUID'],
        'qqid'      =>['type'=>DBField_String,  'len'=>63,   'nullable'=>1,                    'cmt'=>'qqID'],
        'deviceid'  =>['type'=>DBField_String,  'len'=>64,   'nullable'=>1,                    'cmt'=>'Device ID'],
        'realstatus'=>['type'=>DBField_String,  'len'=>24,   'nullable'=>0, 'dft'=>Status_Default,  'cmt'=>'实名状态 '],

        'balance'=>['type'=>DBField_Int,     'len'=>13,   'nullable'=>0,  'dft'=>0,           'cmt'=>'余额 100倍 分为单位 RMB'],
        'point'  =>['type'=>DBField_Int,     'len'=>13,   'nullable'=>0,  'dft'=>0,           'cmt'=>'积分 带小数点'],
        'type'   =>['type'=>DBField_String,  'len'=>16,   'nullable'=>1,                      'cmt'=>'类型 暂时没用'],

    ];




    public $userid;
    public $token;
    public $scope;

    public $detail;

    private $inited = false;
    private $acquired = false;
    private $verified = false;

    private $securityId;

    /**
     * 用户权限
     * @var Access
     */
    public  $access;

    function __construct( string $userid = null, string $token = null, string $scope = null ){

        parent::__construct();

        $this->userid = $userid ?? GroupRole_Guest;
        $this->token  = $token ?? GroupRole_Guest;
        $this->scope  = $scope ?? AccessScope_Common;

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

        $user = new User( Network::getHeaderParam('uid') ?? Network::getHeaderParam('userid')  ,Network::getHeaderParam('token'),Network::getHeaderParam('scope') );
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
    public static function fromSession( string $prefix = WebsiteDefaultID, bool $setToGlobal = true ): User{
        if (!isset($_SESSION)) { session_start(); }
        $user = new User( $_SESSION["{$prefix}_userid"],$_SESSION["{$prefix}_token"],$_SESSION["{$prefix}_scope"] );
        if( $setToGlobal ){ $GLOBALS['User'] = $user; }
        return $user;
    }

    /**
     * 将当前用户缓存到Session中
     * Temp Current User to Session
     * @param  string  $prefix
     */
    public function toSession( string $prefix = WebsiteDefaultID ){
        if (!isset($_SESSION)) { session_start(); }
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
        if (!isset($_SESSION)) { session_start(); }
        $_SESSION["{$prefix}_userid"] = null;
        $_SESSION["{$prefix}_token"] = null;
        $_SESSION["{$prefix}_scope"] = null;
    }

    private function init(){

        if($this->inited){ return ;}

        $this->inited = true;
        $this->securityId = Encrypt::shortId(16);
        $this->access = new Access( $this );
    }

    public function getSecurityId(){
        return $this->securityId;
    }

    public function confirmVerify( string $securityId ){

        if( $securityId == $this->securityId ){
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
        return $this->userid === GroupRole_Guest || !isset($this->userid);
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

    public function systemAuthorize( string $userid, string $scope = AccessScope_Common): ASResult
    {
        $this->userid = $userid;
        $this->scope  = $scope;
        $Access = new Access($this);

        return $Access->authorize();
    }


    /**
     * 新建用户账户
     * add
     * @param DBValues $data
     * @return ASResult
     */
    public function add( DBValues $data ): ASResult
    {
        if ( !$data->has(UserAccount::primaryid) ){
            $uid = Encrypt::shortId(8);
            $data->set(UserAccount::primaryid)->string($uid);
        }else{
            $uid = $data->getValue(UserAccount::primaryid);
        }

        $accountData = $data->purifyCopy(UserAccount::addFields)->set(UserAccount::primaryid)->string($uid);
        $infoData    = $data->purifyCopy(UserInfo::addFields)->set(UserInfo::primaryid)->string($uid);
        $pocketData  = $data->purifyCopy(UserPocket::addFields)->set(UserPocket::primaryid)->string($uid);

        //检测不能同时为空的选项
        if(
            !$data->has('email') &&
            !$data->has('mobile') &&
            !$data->has('username') &&
            !$data->has('appleUUID') &&
            !$data->has('wechatid')
        ){
            return $this->take($data->toArray())->error(504,i18n('USR_UI_REQ'),'User->add');
        }

        if( $data->has('username') && static::exist('username',$data->getValue('username')) ){
            return $this->take($data->getValue('username'))->error(600,i18n('USR_UN_EXT'),'User->add');
        }
        if( $data->has('email') && static::exist('email',$data->getValue('email')) ){
            return $this->take($data->getValue('email'))->error(600,i18n('USR_EM_EXT'),'User->add');
        }
        if( $data->has('mobile') && static::exist('mobile',$data->getValue('mobile')) ){
            return $this->take($data->getValue('mobile'))->error(600,i18n('USR_MB_EXT'),'User->add');
        }
        if( $data->has('wechatid') && static::exist('mobile',$data->getValue('wechatid')) ){
            return $this->take($data->getValue('wechatid') )->error(600,i18n('USR_WE_EXT'),'User->add');
        }
        if( $data->has('appleUUID')  && static::exist('appleUUID',$data->getValue('appleUUID')) ){
            return $this->take($data->getValue('appleUUID') )->error(600,i18n('USR_AP_EXT'),'User->add');
        }

        $userAccount= new UserAccount();
        $addUser = $userAccount->add( $accountData );

        if(!$addUser->isSucceed()){ return $addUser; }

        // 初始化用户信息/钱包

        $userInfo   = new UserInfo( $uid );
        $userInfo->init( $infoData );

        $userPocket = new UserPocket( $uid );
        $userPocket->init( $pocketData );

        return $this->take($uid)->success(i18n('USR_ADD_SUC'),'User->add');
    }

    /**
     * @param DBValues $data
     * @param string $uid
     * @return ASResult
     */
    public function update( DBValues $data, string $uid ): ASResult
    {

        if( $data->has('username') && $this->isConflict('username',$data->getValue('username'),$uid) ){
            return $this->take($data->getValue('username'))->error(600,i18n('USR_UN_EXT'),'User->add');
        }
        if( $data->has('email') && $this->isConflict('email',$data->getValue('email'),$uid) ){
            return $this->take($data->getValue('email'))->error(600,i18n('USR_EM_EXT'),'User->add');
        }
        if( $data->has('mobile') && $this->isConflict('mobile',$data->getValue('mobile'),$uid) ){
            return $this->take($data->getValue('mobile'))->error(600,i18n('USR_MB_EXT'),'User->add');
        }
        if( $data->has('wechatid') && $this->isConflict('mobile',$data->getValue('wechatid'),$uid) ){
            return $this->take($data->getValue('wechatid') )->error(600,i18n('USR_WE_EXT'),'User->add');
        }
        if( $data->has('appleUUID')  && $this->isConflict('appleUUID',$data->getValue('appleUUID'),$uid) ){
            return $this->take($data->getValue('appleUUID') )->error(600,i18n('USR_AP_EXT'),'User->add');
        }

        if ( $data->hasKeyIn( UserAccount::updateFields ) ){
            $accountUpdate = true;
            $updateAccount = UserAccount::common()->update( $data, $uid );
        }
        if ( $data->hasKeyIn(UserInfo::updateFields ) ){
            $infoUpdate = true;
            $updateInfo    = UserInfo::common()->update($data, $uid);
        }

        if( !$accountUpdate && !$infoUpdate ){
            return $this->error(-10,'No legal parameters');
        }

        # 删除历史缓存
        if( ($accountUpdate && $updateAccount->isSucceed()) || ($infoUpdate && $updateInfo->isSucceed()) ){

            $this->getRedis()->isEnabled()
            && $this->getRedis()->clear('USER',$this->userid);
        }

        return $this->feedback();

    }


/* 获取数据部分 Get Data */


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

        $detail = UserAccount::common()->detail($userid);

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
//    public function checkAuth( string $status=null, int $level = 0 ): ASResult
//    {
//
////        if( !$this->access-> )
////        $CHECK = ACCESS::checkToken($userid,$token);
////        if ( !isset($userid) || $userid=='false' || !RESULT::isSucceed($CHECK) ) {
////            return RESULT::feedback($CHECK['status']==308 ? 9998 : 9999,['AUTH_VER_FAL'],$userid,'checkAuth');
////        }
//
//        // status check
//        if(!isset($status) && $level==0 ){ return $this->take($this->userid)->success(i18n('AUTH_SUC'),'User->checkAuth'); }
//        if ($this->detail['status']!=='enabled') {
//            return $this->error(9001,i18n('AUTH_BLOCK'),'User->checkAuth');
//        }
//
//        if ( $this->getUserInfo('level') < $level ) {
//            return $this->error(9900,i18n('AUTH_LEVEL_LOW'),'User->checkAuth');
//        }
//
//        return $this->take($this->userid)->success(i18n('AUTH_SUC'),'User->checkAuth');
//    }

    /**
     * 检测用户权限级别
     * @param int $level
     * @return ASResult
     */
//    public function checkLevel(int $level = 0 ): ASResult
//    {
//
//        if ( $this->getUserInfo('level') < $level ) {
//            return $this->error(9900,i18n('AUTH_LEVEL_LOW'),'User->checkLevel');
//        }
//
//        return $this->take($this->userid)->success(i18n('AUTH_SUC'),'User->checkLevel');
//    }


    /**
     * 通过信息查询用户
     * searchUseridByInfo
     * @param  string  $info
     * @return ASResult
     */
    public function searchUserByInfo( string $info ):ASResult{

        $searchUser = $this->getDB()->get(
            DBFields::init(UserAccount::table)->and('uid'),
            UserAccount::table,
            DBConditions::init()->where('username')->equal($info)->or('mobile')->equal($info)->or('email')->equal($info)
        );

        if ( !$searchUser->isSucceed() ) { return $this->error(400,i18n('USR_NON'),'User->searchUserByInfo'); }

        return $this->take($searchUser->getContent()[0]['uid'])->success();
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

        if( UserAccount::rds_auto_cache && $this->getRedis()->isEnabled() && $this->getRedis()->has($hashParams) ){
            return $this->getRedis()->read($hashParams);
        }else{

            if( $this->userid === GroupRole_Guest ){

                $Guest_Detail = [
                    'uid'=>GroupRole_Guest,
                    "info"=>[],
                    "group"=>[
                        "uid"=>Group_Guest,
                        "level"=>GroupLevel_Guest,
                        "type"=>GroupRole_Guest,
                        "groupname"=>GroupRole_Guest
                    ]
                ];

                return $this->take( $Guest_Detail )->success();
            }

            $primaryJoin = DBJoinParam::convincePrimaryForDetail(UserAccount::class, $this->userid );

            $infoParam   = DBJoinParam::convinceForDetail(UserInfo::class,UserAccount::primaryid,UserAccount::table )->asSub('info');
            $groupParam  = DBJoinParam::convinceForDetail(UserGroup::class, 'groupid', UserAccount::table )->asSub('group');
            $pocketParam = DBJoinParam::convinceForDetail(UserPocket::class,UserAccount::primaryid,UserAccount::table )->asSub('pocket');

            $joinParams  = DBJoinParams::init( $primaryJoin )->leftJoin($infoParam)->leftJoin($groupParam)->leftJoin($pocketParam);

            $fullDetail  = $this->getByJoin( $joinParams );

            if( $fullDetail->isSucceed() ){

                $fullDetail->setContent( $fullDetail->getContent()[0] );

                $this->getRedis()->isEnabled()
                && $this->getRedis()->cache($hashParams,$fullDetail,12*3600)
                && $this->getRedis()->track('USER',$this->userid,$hashParams);
            }
        }
        return $fullDetail;
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

        $inUserInfo = in_array($key,UserInfo::filterFields);
        $table = $inUserInfo ? UserInfo::table : UserAccount::table;

        $countUser = _ASDB()->count( $table,DBConditions::init( $table )->where($key)->equal($value) );
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

        if( in_array($key, UserInfo::addFields) ){
            $infoModel = new UserInfo();
            $LIST = $infoModel->list(DBConditions::init()->where($key)->equal($value),1,1);
        }else{
            $LIST = $this->list(DBConditions::init()->where($key)->equal($value),1,1);
        }

        if ( !$LIST->isSucceed()){ return $this->error(400,i18n('USR_NON'),'User->getUserid'); }

        return $this->take($LIST->getContent()[0]['uid'])->success(i18n('USR_GET_SUC'),'User->getUserid');
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

    public function belongGroups( array $groups ):bool{

        $this->acquire();

        return in_array($this->detail['group']['uid'],$groups);
    }


    /**
     * 查询是否超级管理员
     * isSuper
     * @return bool
     */
    public function isSuper(): bool
    {
        return $this->isInGroup(Group_SuperAdmin);
    }

    /**
     * 查询是否管理员
     * isAdmin
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isInGroup(Group_Admin);
    }

    // 查询是否网站编辑
    public function isEditor(): bool
    {
        return $this->isInGroup(Group_Editor);
    }

    // 查询是否专家/讲师
    public function isAuthor(): bool
    {
        return $this->belongGroups([Group_Author,Group_AuthorStandard,Group_AuthorPro,Group_AuthorExclusive]);
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
     * @param  int     $vipLevel
     * @return ASResult
     */
    public function setVip( int $duration, int $vipLevel=1 ): ASResult
    {
        $vipExpire = $this->getUserInfo('vipexpire');
        $vipExpire = $vipExpire>0 ? $vipExpire : time();
        return $this->update(DBValues::init('vip')->number($vipLevel)->set('vipexpire')->number($vipExpire+$duration),$this->userid);
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
     * @return ASResult
     */
    public function undoVip(): ASResult
    {
        return $this->updateUserInfo(DBValues::init(UserAccount::table)->set('vipexpire')->number(0));
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
     * @param DBValues $data
     * @return ASResult
     */
    public function updateUserInfo( DBValues $data ): ASResult
    {

        $infoModel = new UserInfo();
        return $infoModel->update( $data, $this->userid );
    }


}