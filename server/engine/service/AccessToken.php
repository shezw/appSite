<?php

namespace APS;

/**
 * 权限令牌
 * AccessToken
 * @package APS\service\Access
 */
class AccessToken extends ASModel
{


    /**
     * 数据表名
     * @var string
     */
    public static $table = 'access_token';

    /**
     * 主索引字段
     * @var string
     */
    public static $primaryid = 'NAN';

    /**
     * 添加支持字段
     * @var array [string]
     */
    protected static $addFields;

    /**
     * 更新支持字段
     * @var array [string]
     */
    protected static $updateFields;

    /**
     * 详情支持字段
     * @var array [string]
     */
    protected static $detailFields;

    /**
     * 外部接口详情支持字段
     * @var array [string]
     */
    protected static $publicDetailFields;

    /**
     * 概览支持字段
     * @var array [string]
     */
    protected static $overviewFields;

    /**
     * 列表支持字段
     * @var array [string]
     */
    protected static $listFields;

    /**
     * 外部接口列表支持字段
     * @var array [string]
     */
    protected static $publicListFields;

    /**
     * 计数查询支持筛选字段
     * @var array [string]
     */
    protected static $countFilters;

    /**
     * 多重数据结构
     * @var array [string=>any]
     */
    protected static $depthStruct    = null;

    /**
     * 搜索支持字段
     * @var array [string]
     */
    protected static $searchFilters  = null;


    /**
     * 注册新令牌
     * addToken
     * @param  string  $userid
     * @param  string  $scope
     * @param  int     $duration
     * @return ASResult
     */
    public function addToken( string $userid, string $scope = 'common', int $duration = Time::THIRTY ):ASResult{

        $t        = time();
        $expire   = (int)$t+(int)$duration;
        $token    = Encrypt::minId(64);

        $data = [
            'userid'   => $userid,
            'scope'    => $scope,
            'token'    => $token,
            'expire'   => $expire,
        ];

        $DB = $this->add($data);

        if ( !$DB->isSucceed() ) { return $DB; }
        return $this->take(['token'=>$token,'scope'=>$scope,'expire'=>$expire,'userid'=>$userid])->success(i18n('SYS_ADD_SUC'),'ACCESS::addToken');
    }

    public function beforeAdd(array &$data)
    {
        unset($data[static::$primaryid]);
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

    public function beforeUpdate(array &$data)
    {
        unset($data[static::$primaryid]);
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

        $dbCheck = $this->getDB()->check($token,'token',static::$table,['userid'=>$userid,'scope'=>$scope],' expire DESC, createtime DESC ');

        return $dbCheck->isSucceed() ?
            $this->take($token)->success(i18n('AUTH_SUC'),'ACCESS::checkToken') :
            $this->error($dbCheck->getStatus()==308?9998:9999,i18n('AUTH_VER_FAL'),'ACCESS::checkToken');
    }


/* 第三方鉴权  ThirdParty Access */


    /**
     * 添加第三方token
     * @param  string    $unionid   用户ID
     * @param  string    $token     令牌
     * @param  string    $scope     作用域
     * @param  int       $duration  权限时长
     * @param  int|null  $expire    过期时间
     * @return ASResult
     */
    public function addUnionToken( string $unionid, string $token, string $scope, int $duration = 0, int $expire = null ):ASResult{

        $expire   = $expire ?? time()+(int)( $duration*0.9 );

        $data = [
            'userid'   => $unionid,
            'scope'    => $scope,
            'token'    => $token,
            'expire'   => $expire,
        ];

        $DB = $this->getDB()->add($data,static::$table);

        if ( !$DB->isSucceed() ) { return $DB; }
        return $this->take($token)->success(i18n('SYS_ADD_SUC'),'ACCESS::addToken');

    }

    /**
     * 检测token是否有效
     * check Union token is valid
     * @param  string  $unionid
     * @param  string  $token
     * @param  string  $scope
     * @return boolean
     */
    public function isValidUnionToken( string $unionid, string $token, string $scope = 'access_token' ):bool{

        $now  = time();

        $DB = $this->getDB()->count( static::$table, "expire>{$now} AND userid='{$unionid}' AND token='{$token}' AND scope='{$scope}'");

        return $DB->getContent() > 0;
    }

    /**
     * 获取第三方token
     * @param  string $union 第三方标题
     * @param  string $scope 作用域
     * @return ASResult
     */
    public function getUnionToken( string $union, string $scope='access_token' ):ASResult{

        $now  = time();

        $DB = $this->getDB()->get('token', static::$table, "expire>{$now} AND userid='{$union}' AND scope='{$scope}'");

        if ( !$DB->isSucceed() ) { return $DB; }
        return $DB;
    }

    /**
     * 更新第三方token
     * @param  string    $unionid   第三方id
     * @param  string    $token     令牌
     * @param  string    $scope     作用域
     * @param  int       $duration  持续时长
     * @param  int|null  $expire
     * @return ASResult
     */
    public function refreshUnionToken( string $unionid, string $token, string $scope = 'access_token', int $duration = 0, int $expire = null ):ASResult{

        if($this->hasUnionToken($unionid,$scope)){

            $data = [
                'token'  =>$token,
                'expire' =>$expire ?? time()+(int)($duration*0.9),
            ];

            $conditions  = "userid='{$unionid}' AND scope='{$scope}' ORDER BY createtime DESC LIMIT 1 ";

            return $this->getDB()->update($data, static::$table, $conditions);

        }else{

            return $this->addUnionToken($unionid,$token,$scope,$duration);
        }
    }


    /**
     * 是否存在该第三方token
     * hasUnionToken
     * @param  string  $unionid
     * @param  string  $scope
     * @return bool
     */
    public function hasUnionToken( string $unionid, string $scope='access_token' ):bool{

        $DB_COUNT = $this->getDB()->count(static::$table,"scope='{$scope}' AND userid='{$unionid}'");
        return $DB_COUNT->getContent() > 0;

    }


    // 谨慎使用,将会清空所有过期(无效)token 同时会丢失token注册、更新记录
    // 为了防止出现意外，这里将清理保护设置为 3 * 24 * 3600 即 清除3天前过期的数据

    public function clearToken(): ASResult
    {

        $t  = time()- 3 * Time::DAY ;
        return $this->getDB()->remove(static::$table,"expire<{$t}");
    }



}