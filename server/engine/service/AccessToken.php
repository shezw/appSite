<?php

namespace APS;

/**
 * 权限令牌
 * AccessToken
 * @package APS\service\Access
 */
class AccessToken extends ASModel
{

    const table = 'access_token';
    const comment   = '权限令牌';

    const primaryid = 'uid';
    const addFields = ['uid','userid','saasid','token','scope','expire','createtime','lasttime'];
    const updateFields = ['uid','userid','saasid','token','scope','expire','createtime','lasttime'];
    const detailFields = ['uid','userid','saasid','token','scope','expire','createtime','lasttime'];
    const overviewFields = ['uid','userid','saasid','token','scope','expire','createtime','lasttime'];
    const listFields = ['uid','userid','saasid','token','scope','expire','createtime','lasttime'];
    const filterFields = ['uid','userid','saasid','scope','expire','createtime','lasttime'];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID',  'idx'=>DBIndex_Unique ],
        'userid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID',  'idx'=>DBIndex_Index ],
        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'token'=>    ['type'=>DBField_String,    'len'=>511, 'nullable'=>0,  'cmt'=>'token令牌'],
        'scope'=>    ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'权限作用域',  'idx'=>DBIndex_Index ],
        'expire'=>   ['type'=>DBField_TimeStamp, 'len'=>11,  'nullable'=>0,  'cmt'=>'过期时间 时间戳 必填',   'dft'=>0,  'idx'=>DBIndex_Index ],

        'createtime'=>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',   'idx'=>DBIndex_Index, ],
        'lasttime'  =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    const depthStruct    = [
        'expire'=>DBField_TimeStamp,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp
    ];


    /**
     * 注册新令牌
     * addToken
     * @param  string  $userid
     * @param  string  $scope
     * @param  int     $duration
     * @return ASResult
     */
    public function addToken( string $userid, string $scope = AccessScope_Common, int $duration = Time::THIRTY ):ASResult{

        $t        = time();
        $expire   = (int)$t+(int)$duration;
        $token    = Encrypt::minId(128);

        $data = AccessToken::initValuesFromArray([
            'userid'   => $userid,
            'scope'    => $scope,
            'token'    => $token,
            'expire'   => $expire,
            'saasid'   => saasId()
        ]);

        $DB = $this->add($data);

        if ( !$DB->isSucceed() ) { return $DB; }
        return $this->take(['token'=>$token,'scope'=>$scope,'expire'=>$expire,'userid'=>$userid])->success(i18n('SYS_ADD_SUC'),'ACCESS::addToken');
    }


    /**
     * 更新令牌 访问
     * updateToken
     * @param  string  $userid
     * @param  string  $token
     * @param  string  $scope
     * @param  int     $duration    有效时长 单位秒
     * @return ASResult
     */
    public function updateToken( string $userid, string $token, string $scope = 'common', int $duration = Time::THIRTY  ): ASResult
    {

        $CHECK = $this->checkToken($userid,$token,$scope);
        if(!$CHECK->isSucceed()){
            return $CHECK;
        }

        return $this->addToken($userid,$scope,$duration);
    }

    /**
     * 检测令牌
     * checkToken
     * @param  string  $userid
     * @param  string  $token
     * @param  string  $scope
     * @return ASResult
     */
    public function checkToken( string $userid, string $token, string $scope='common'):ASResult{

        $dbCheck = $this->getDB()->check($token,'token',static::table,
            DBConditions::init()->where('userid')->equal($userid)->and('scope')->equal($scope)->and('saasid')->equalIf(saasId())
                ->orderWith(' expire DESC, createtime DESC '));

        return $dbCheck->isSucceed() ?
            $this->take($token)->success(i18n('AUTH_SUC'),'ACCESS::checkToken') :
            $this->error($dbCheck->getStatus()==308?9998:9999,i18n('AUTH_VER_FAL'),'ACCESS::checkToken');
    }


/* 第三方鉴权  ThirdParty Access */


    /**
     * 添加第三方token
     * @param  string    $unionId   用户ID
     * @param  string    $token     令牌
     * @param  string    $scope     作用域
     * @param  int       $duration  权限时长
     * @param  int|null  $expire    过期时间
     * @return ASResult
     */
    public function addUnionToken(string $unionId, string $token, string $scope, int $duration = 0, int $expire = null ):ASResult{

        $expire   = $expire ?? time()+(int)( $duration*0.9 );

        $data = static::initValuesFromArray([
            'userid'   => $unionId,
            'scope'    => $scope,
            'token'    => $token,
            'expire'   => $expire,
            'saasid'   => saasId()
        ]);

        $DB = $this->getDB()->add($data,static::table);

        if ( !$DB->isSucceed() ) { return $DB; }
        return $this->take($token)->success(i18n('SYS_ADD_SUC'),'ACCESS::addToken');

    }

    /**
     * 检测token是否有效
     * check Union token is valid
     * @param  string  $unionId
     * @param  string  $token
     * @param  string  $scope
     * @return boolean
     */
    public function isValidUnionToken(string $unionId, string $token, string $scope = 'access_token' ):bool{

        $DB = $this->getDB()->count( static::table,
            DBConditions::init()
                ->where('expire')->bigger(time())
                ->and('userid')->equal($unionId)
                ->and('token')->equal($token)
                ->and('scope')->equal($scope)
                ->and('saasid')->equalIf(saasId())
        );

        return $DB->getContent() > 0;
    }

    /**
     * 获取第三方token
     * @param  string $union 第三方标题
     * @param  string $scope 作用域
     * @return ASResult
     */
    public function getUnionToken( string $union, string $scope='access_token' ):ASResult
    {
        $DB = $this->getDB()->get(DBFields::init()->and('token'), static::table,
            DBConditions::init()
                ->where('expire')->bigger(time())
                ->and('userid')->equal($union)
                ->and('scope')->equal($scope)
                ->and('saasid')->equalIf(saasId())
        );

        if ( !$DB->isSucceed() ) { return $DB; }
        return $DB;
    }

    /**
     * 更新第三方token
     * @param  string    $unionId   第三方id
     * @param  string    $token     令牌
     * @param  string    $scope     作用域
     * @param  int       $duration  持续时长
     * @param  int|null  $expire
     * @return ASResult
     */
    public function refreshUnionToken(string $unionId, string $token, string $scope = 'access_token', int $duration = 0, int $expire = null ):ASResult{

        if($this->hasUnionToken($unionId,$scope)){

            $data = DBValues::init('token')->string($token)->set('expire')->number($expire ?? time()+(int)($duration*0.9));

            $conditions = DBConditions::init( static::table )
                ->and('userid')->equal($unionId)
                ->and('scope')->equal($scope)
                ->and('saasid')->equalIf(saasId())
                ->orderBy('createtime', DBOrder_DESC )
                ->limitWith();

            return $this->getDB()->update($data, static::table, $conditions);

        }else{

            return $this->addUnionToken($unionId,$token,$scope,$duration);
        }
    }


    /**
     * 是否存在该第三方token
     * hasUnionToken
     * @param  string  $unionId
     * @param  string  $scope
     * @return bool
     */
    public function hasUnionToken(string $unionId, string $scope='access_token' ):bool{

        $DB_COUNT = $this->getDB()->count(static::table,DBConditions::init()->where('scope')->equal($scope)->and('userid')->equal($unionId)->and('saasid')->equalIf(saasId()));
        return $DB_COUNT->getContent() > 0;

    }


    // 谨慎使用,将会清空所有过期(无效)token 同时会丢失token注册、更新记录
    // 为了防止出现意外，这里将清理保护设置为 3 * 24 * 3600 即 清除3天前过期的数据

    public function clearToken(): ASResult
    {
        $t  = time()- 3 * Time::DAY ;
        return $this->getDB()->remove(static::table,DBConditions::init()->where('expire')->less($t)->and('saasid')->equalIf(saasId()));
    }



}