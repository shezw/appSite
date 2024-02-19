<?php

namespace APS;

/**
 * 基础模型组件
 * ASModel
 *
 * 模型抽象类、集成了数据库查询、数据转换、缓存、Redis访问等
 * Abstract class, integrated database query, data conversion, cache, Redis access, etc.
 *
 * @package APS\core
 */
abstract class ASModel extends ASBase{

    /**
     * @var array (name=>[properties])
     */
    const tableStruct = [];

    /**
     * @var string 数据表名
     */
	const table = "ASModel";

	const comment = "抽象模型";

    /**
     * @var string 主索引字段
     */
	const primaryid = 'uid';

    /**
     * 添加支持字段
     * @var array [string]
     */
    const addFields = null;

    /**
     * 更新支持字段
     * @var array [string]
     */
    const updateFields = null;

    /**
     * 详情支持字段
     * @var array [string]
     */
    const detailFields = null;

    /**
     * 外部接口详情支持字段
     * @var array [string]
     */
    const publicDetailFields = null;

    /**
     * 概览支持字段
     * @var array [string]
     */
    const overviewFields = null;

    /**
     * 列表支持字段
     * @var array [string]
     */
    const listFields = null;

    /**
     * 外部接口列表支持字段
     * @var array [string]
     */
    const publicListFields = null;

    /**
     * 计数、查询筛选 支持字段 ( 原countFilters )
     * @var array [string]
     */
    const filterFields = null;

    /**
     * 数据转换规则
     * @var array
     */
    const depthStruct = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    /**
     * 搜索支持字段
     * @var array [string]
     */
    const searchFilters  = NULL;

    /**
     * 开启日志
     * @var bool
     */
	const record_enabled = true;

    /**
     * 自动使用REDIS缓存
     * @var bool
     */
    const rds_auto_cache = false;

    /**
     * Redis缓存
     * @var ASRedis
     */
	protected $Redis;

    /**
     * 缓存hash
     * @var mixed
     */
    protected $RedisHash;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 通用单例
     * common
     * @return static
     */
    public static function common(): ASModel
    {
        return new static();
    }

    protected function getRedis():ASRedis{
        if( !isset($this->Redis) ){
            $this->Redis = _ASRedis();
        }
        return $this->Redis;
    }

    /**
     * 添加数据
     * add data to Database
     * @param DBValues $data
     * @return ASResult
     */
	public function add( DBValues $data ): ASResult
    {
        $uid = Encrypt::shortId(8);

        if ( !$data->has(static::primaryid) && isset( static::tableStruct[ static::primaryid ] ) ){
            $data->set(static::primaryid)->string( $uid );
        }
        if ( in_array('saasid',static::addFields) ){
            $data->set('saasid')->stringIf(saasId());
        }

        $this->beforeAdd($data);

		$this->DBAdd($data);
		$this->setId($data->getValue(static::primaryid) ?? 'NAN_ID' );
		$this->record('ITEM_ADD',static::table.'->add');

		if($this->result->isSucceed()){ $this->result->setContent($uid); } // 成功返回单位的索引ID

		$this->beforeAddReturn( $this->result, $data );

        if(static::rds_auto_cache){
            $this->_clearSet(static::class,'list');
            $this->_clearSet(static::class,'count');
        }
		return $this->feedback();
	}

    /**
     * 通过数组自动添加
     * @param array $arrayData
     * @return ASResult
     */
	public function addByArray( array $arrayData ): ASResult{
	    return $this->add( static::initValuesFromArray($arrayData) );
    }

    /**
     * 函数注入(插入数据之前)
     * Do something before add
     * @param DBValues $data
     */
	public function beforeAdd( DBValues &$data ){  }

    /**
     * 函数注入(插入数据返回结果之前)
     * Do something before add result returning
     * @param ASResult $result 即将返回的结果
     * @param DBValues $data 插入数据
     */
	public function beforeAddReturn( ASResult &$result, DBValues $data ){  }


    /**
     * 更新单行数据
     * update data with uid
     * @param DBValues $data
     * @param string $uid
     * @return ASResult
     */
	public function update( DBValues $data , string $uid ): ASResult
    {
        $data->purify(static::updateFields);

//        if( isset(static::depthStruct) && in_array('ASJson',array_values(static::depthStruct))  ){
//            foreach ( static::depthStruct as $key => $type ){
//                if( $type == 'ASJson' ){
//                    $data[$key] = Encrypt::ASJsonEncode( $data[$key] );
//                }
//            }
//        }

		$this->beforeUpdate($data);

//		$data = Filter::purify($data,static::updateFields);
//
//		if (count($data)<1) {
//		    return $this->take($uid)->error(603,i18n('SYS_PARA_REQ'),static::table.'->update');
//		}

		$conditions = DBConditions::init(static::table)->where(static::primaryid)->equal($uid);

		$this->DBUpdate($data,$conditions);
		$this->setId($uid);
		$this->record('ITEM_UPDATE',static::table.'->update');

		$this->beforeUpdateReturn( $this->result,$uid );

        if( static::rds_auto_cache ) {
            $this->_clearSet(static::class, 'list');
            $this->getRedis()->remove([$uid, true]);
            $this->getRedis()->remove([$uid, false]);
        }
		return $this->feedback();
	}

	public function beforeUpdate( DBValues &$data ){}
	public function beforeUpdateReturn( ASResult &$result,string $uid ){}

//	public function publicUpdate(DBValues $data, string $uid, string $userid ): ASResult
//    {
//
//		$DETAIL = $this->detail($uid)->getContent();
//
//		$authorId = $DETAIL['authorid'] ?? $DETAIL['userid'] ?? $DETAIL['receiveid'];
//
//		if( $authorId!==$userid ){
//		    return $this->take($userid)->error(9990,i18n('ACC_PROS_CHK_FAL'),"ITEM::publicRemove");
//		}
//
//		return $this->update($data,$uid);
//	}

    /**
     * 通过数组数据更新
     * @param array $data
     * @param string $uid
     * @return ASResult
     */
    public function updateByArray( array $data, string $uid ): ASResult
    {
	    return $this->update( static::initValuesFromArray($data), $uid );
    }


    /**
     * 移除
     * remove
     * @param  string  $uid
     * @return ASResult
     */
	public function remove( string $uid ): ASResult
    {
		$this->beforeRemove($uid);

        $conditions = DBConditions::init(static::table)->where(static::primaryid)->equal($uid);

        $this->setId($uid);
		$this->DBRemove($conditions);
		$this->record('ITEM_REMOVE',static::table.'->remove');

		$this->beforeRemoveReturn($this->result,$uid);

		if( static::rds_auto_cache ){
            $this->_clearSet(static::class,'list');
            $this->_clearSet(static::class,'count');
            $this->getRedis()->remove([$uid,true]);
            $this->getRedis()->remove([$uid,false]);
        }
		return $this->feedback();
	}

	public function beforeRemove( string &$uid ){}
	public function beforeRemoveReturn( ASResult &$result,string $uid ){}

    /**
     * 开放接口移除
     * publicRemove
     * @param  string  $uid
     * @param  string  $userid
     * @return ASResult
     */
//	public function publicRemove(string $uid, string $userid ): ASResult
//    {
//
//		$DETAIL = $this->detail($uid)->getContent();
//
//		$authorId = $DETAIL['authorid'] ?? $DETAIL['userid'] ?? $DETAIL['receiveid'];
//
//		if( $authorId!==$userid ){
//		    return $this->take($userid)->error(9990,i18n('ACC_PROS_CHK_FAL'),"ITEM::publicRemove");
//		}
//
//		return $this->remove($uid);
//	}

    /**
     * 查询唯一数据详情
     * Get detail by itemid
     * @param  string  $uid
     * @param bool $public
     * @return ASResult
     */
	public function detail(string $uid , bool $public = false ): ASResult
    {
        $this->RedisHash = [$uid,$public];

        if( static::rds_auto_cache && $this->_hasCache() ){
            return $this->_getCache();
        }

		$this->beforeDetail($uid);

        $fields     = DBFields::initBySimpleList( ($public && !empty(static::publicDetailFields)) ? static::publicDetailFields : static::detailFields );
        $conditions = DBConditions::init(static::table)->where(static::primaryid)->equal($uid)->limitWith(0,1);

		$this->DBGet($fields,$conditions);
		$this->setId($uid);

		if($this->result->isSucceed()){
		    $this->result->setContent($this->convert($this->result->getContent()[0]));
		}

		$this->beforeDetailReturn( $this->result,$uid );

		static::rds_auto_cache
        && $this->result->isSucceed()
        && $this->_cache();

		return $this->feedback();
	}

	public function beforeDetail( string &$uid ){}
	public function beforeDetailReturn( ASResult &$result, string $uid ){}


    /**
     * 获取公开接口详情
     * Detail for with public fields
     * @param  string  $uid
     * @return ASResult
     */
	public function publicDetail( string $uid ): ASResult
    {

		return $this->detail($uid,true);
	}	

	public static function uidCondition( string $uid ): DBConditions
    {
        return DBConditions::init(static::table)->where(static::primaryid)->equal($uid);
    }

    public static function emptyCondition(): DBConditions{
	    return DBConditions::init(static::table);
    }


    /**
     * 通过ID获取对应字段值
     * get value at $key field by itemid
     * @param  string  $key
     * @param  string  $uid
     * @return ASResult
     */
	public function get( string $key , string $uid ): ASResult
    {

		$this->beforeGet($key,$uid);

		$this->DBGet(DBFields::init(static::table)->and($key), static::uidCondition($uid));
		$this->setId($uid);

        if($this->result->isSucceed()){ $this->result->setContent($this->convert($this->result->getContent()[0][$key])) ; }

		$this->beforeGetReturn( $this->result, $uid );

		return $this->feedback();
	}

	public function beforeGet( string &$key, string &$uid ){}
	public function beforeGetReturn( ASResult &$result, string $uid ){}


    /**
     * 获取概览
     * overview
     * @param  string  $uid
     * @return ASResult
     */
	public function overview( string $uid ): ASResult
    {
		$this->DBGet(DBFields::initBySimpleList(static::overviewFields ?? static::detailFields), static::uidCondition($uid) );
		$this->setId($uid);

        if($this->result->isSucceed()){
            $this->result->setContent($this->convert($this->result->getContent()[0]));
        }
		return $this->feedback();
	}


    /**
     * 统计特定数量
     * count
     * @param DBConditions $DBConditions
     * @return ASResult
     */
	public function count( DBConditions $DBConditions ): ASResult
    {
        $this->RedisHash = $DBConditions->toArray();

        if( static::rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }
        $DBConditions->and('saasid')->equalIf(saasId());

        $this->beforeCount($DBConditions);
		$this->DBCount($DBConditions);
		$this->beforeCountReturn($this->result);

        static::rds_auto_cache && $this->_cache() && $this->_trackCache(static::class, 'count', $this->RedisHash);

		return $this->feedback();
	}

	public function countByArray( array $filter ): ASResult
    {
        return $this->count( static::initConditionFromArray($filter) );
    }

    /**
     * 查询是否存在对应数据
     * Check data is exist by filters
     * @param DBConditions $filters
     * @return bool
     */
	public function has( DBConditions $filters ):bool{

	    return $this->count($filters)->getContent() > 0;
    }

	public function beforeCount( DBConditions &$filters ){  }
	public function beforeCountReturn( ASResult &$result ){  }

	/**
     * 获取列表
	 * Get list
	 * @param    DBConditions             $filters        筛选条件
	 * @param    int                      $page           翻页-页数
	 * @param    int                      $size           翻页-页长
	 * @param    string|null              $sort           排序字段
	 * @param    boolean                  $public         是否公开列表
	 * @return   ASResult                            结果对象
	 */
	public function list( DBConditions $filters , int $page=1, int $size=25, string $sort = null , $public = false ): ASResult
    {
		$this->RedisHash = [$filters->toArray(),$page,$size,$sort,$public];

		if( static::rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

		$this->beforeList( $filters, $page,$size,$sort,$public );

		$filters->purify( $public && !empty(static::publicListFields) ? static::publicListFields : static::listFields );
		$filters->and('saasid')->equalIf(saasId());
		$filters->limitWith( $size * ($page - 1), $size );
		$filters->orderWith( $sort ?? 'createtime DESC' );

		$this->DBGet(DBFields::initBySimpleList($public === true && !empty(static::publicListFields) ? static::publicListFields : static::listFields ), $filters );

		if($this->result->isSucceed()) {

            $list = $this->result->getContent();

            for ($i = 0; $i < count($list); $i++) {

                $list[$i] = $this->convert($list[$i]);
            }
            $this->result->setContent($list);

            static::rds_auto_cache && $this->_cache() && $this->_trackCache(static::class, 'list', $this->RedisHash);
        }

		$this->beforeListReturn($this->result);

		return $this->feedback();
	}

	public function listByArray( array $filter , int $page=1, int $size=25, string $sort = null , $public = false ): ASResult
    {
        return $this->list( static::initConditionFromArray($filter),$page,$size,$sort,$public );
    }


	public function beforeList( DBConditions &$filters, int &$page=1, int &$size=25, string &$sort = null , &$public = false ){  }
	public function beforeListReturn( ASResult &$result ){  }

	// 获取公开接口列表
	public function publicList( DBConditions $filters , int $page=1, int $size=25, string $sort = null ): ASResult
    {
		return $this->list($filters,$page,$size,$sort,true);
	}

	public function getByJoin(DBJoinParams $joinParams): ASResult
    {
        parent::getByJoin($joinParams); // TODO: Change the autogenerated stub

        return $this->feedback();
    }

    /**
     * @param DBConditions $filters  Conditions/Filters for primary table
     * @param DBJoinParam[] $joins   List of DBJoinParams
     * @param int $page
     * @param int $size
     * @param string|null $sort
     * @param bool $public
     * @return ASResult
     */
	public function listWithJoin(DBConditions $filters, array $joins, int $page =1, int $size=20, string $sort = null, bool $public = false ): ASResult{

        $filters->purify( $public && !empty(static::publicListFields) ? static::publicListFields : static::listFields );
        $filters->and('saasid')->equalIf(saasId());
	    $filters->limitWith($size * ($page - 1),$size);
        if( $sort ){
            $filters->orderWith($sort);
        }

        $primaryJoin = DBJoinParam::convincePrimaryForList(static::class, $filters );

        $joinParams = DBJoinParams::init( $primaryJoin );

        for ($i=0; $i<count($joins); $i++ ){
            $joinParams->leftJoin( $joins[$i] );
        }

        $this->RedisHash = [$joinParams->toArray(),$page,$size,$sort];

        if( static::rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

        $this->result = $this->getByJoin( $joinParams );

        if($this->result->isSucceed()) {

            $list = $this->result->getContent();

            for ($i = 0; $i < count($list); $i++) {

                $list[$i] = $this->convert($list[$i]);
            }
            $this->result->setContent($list);

            static::rds_auto_cache && $this->_cache() && $this->_trackCache(static::class, 'listWithJoin', $this->RedisHash);
        }

        $this->beforeListReturn($this->result);

        return $this->feedback();
    }

    /**
     * @param array $filters
     * @param DBJoinParam[] $joinParamArray
     * @param int $page
     * @param int $size
     * @param string|null $sort
     * @return ASResult
     */
    public function listWithJoinByArray( array $filters, array $joinParamArray, int $page=1, int $size=20, string $sort = null):ASResult{

        $conditions = static::initConditionFromArray($filters);

        return $this->listWithJoin( $conditions, $joinParamArray, $page,$size,$sort );
    }

    /**
     * @param string $uid
     * @param DBJoinParam[] $joins   List of DBJoinParams
     * @param bool $public
     * @return ASResult
     */
    public function detailWithJoin( string $uid, array $joins, bool $public = false ): ASResult
    {
        $primaryJoin = DBJoinParam::convincePrimaryForDetail(static::class, $uid);

        $joinParams = DBJoinParams::init( $primaryJoin );

        for ($i=0; $i<count($joins); $i++ ){
            $joinParams->leftJoin( $joins[$i] );
        }

        $this->RedisHash = [$joinParams->toArray()];

        if( static::rds_auto_cache && $this->_hasCache() ) return $this->_getCache();

        $this->result = $this->getByJoin($joinParams);
        $this->setId($uid);

        if($this->result->isSucceed()){
            $this->result->setContent($this->convert($this->result->getContent()[0]));
        }

        static::rds_auto_cache
        && $this->result->isSucceed()
        && $this->_cache();

        return $this->feedback();

    }


    /**
     * 是否支持缓存(Redis)
     * Is cache(Redis) supported in environment
     * @return bool
     */
    public function _isCacheEnabled(): bool
    {
        return $this->getRedis()->isEnabled();
    }

    /**
     * 是否存在对应缓存数据
     * _hasCache
     * @param string|null $hash  缓存哈希
     * @return bool
     */
    public function _hasCache(string $hash = null ): bool
    {
        return $this->_isCacheEnabled() && $this->getRedis()->has( $hash ?? $this->RedisHash );
    }

    /**
     * 取出缓存
     * Get cache from Redis server
     * @param string|null $hash
     * @return ASResult
     */
    public function _getCache(string $hash = null ): ASResult
    {
        return ASResult::fromArray($this->getRedis()->read( $hash ?? $this->RedisHash )->getContent());
    }

    /**
     * 缓存数据
     * Cache data to Redis server
     * @param  mixed    $hash
     * @param ASResult $result
     * @param  int    $expireDuration
     * @return bool
     */
    public function _cache( $hash = null, ASResult $result = null, int $expireDuration = 3600  ): bool
    {
        return $this->_isCacheEnabled() && $this->getRedis()->cache( $hash ?? $this->RedisHash, ($result ?? $this->result)->toArray(),$expireDuration);
    }

    /**
     * 跟踪缓存
     * trackCache
     * 将对应id的缓存加入到Set中，清空的时候可以将相关缓存一并清理，防止出现关联缓存不同步的问题
     * Add the cache of the corresponding id to the Set, For to ensure that related caches can be cleared together when emptying.
     * @param $set
     * @param $id
     * @param $hashID
     * @return bool
     */
    public function _trackCache( $set,$id,$hashID ): bool
    {
        if(!$this->getRedis()->isEnabled()){ return false; }
        return $this->getRedis()->track($set,$id,$hashID);
    }

    /**
     * 清空Set下的缓存
     * clear Cache in set
     * @param $set
     * @param $id
     * @return bool
     */
    public function _clearSet( $set,$id ): bool
    {
        if(!$this->getRedis()->isEnabled()){ return false; }
        return $this->getRedis()->clear($set,$id);
    }

    /**
     * 根据定义的数据结构 转化数据
     * convert string array to array of specific struct
     * @param  array       $data
     * @param  array|null  $struct
     * @return array
     */
	public function convert( array $data , array $struct = null ): array
    {
		if( !$struct && !static::depthStruct ){ return $data; }

		$struct = $struct ?? static::depthStruct;

		foreach ( $struct as $key => $value ) {
		
			if(isset($data[$key])){

				if(gettype($value)=="array"){

					$data[$key] = gettype($struct[$key])=='array' ? $struct[$key] : json_decode($struct[$key],true);
					$data[$key] = $this->convert($data[$key],$value);

				}else{

					switch ($value) {
                        case DBField_Boolean:
                        $data[$key] = $data[$key] ? true : false;
                        break;
						case 'int':
                        case DBField_Int:
                        case DBField_TimeStamp:
						$data[$key] = (int)$data[$key];
						break;
						case 'double':
                        case DBField_Double:
                        case DBField_Decimal:
                        case DBField_Float:
						$data[$key] = (double)$data[$key];
						break;
						case 'json':
                        case DBField_Json:
						$data[$key] = json_decode($data[$key],true);
						break;
						case 'ASjson':
						case 'ASJson':
                        case DBField_ASJson:
						$data[$key] = Encrypt::ASJsonDecode($data[$key]);
						break;
						default:
						break;
					}

				}
			}
		}
		return $data;
	}


    /**
     * 设置状态
     * status
     * @param  string  $uid
     * @param  string  $status
     * @return ASResult
     */
	public function status(string $uid, string $status ): ASResult
    {
		$this->beforeStatus($uid,$status);
		return $this->update(DBValues::init('status')->string($status),$uid);
	}

	public function beforeStatus( string &$uid, string &$status ){  }

	public function block(   string $uid ):ASResult { return $this->status($uid,'disabled'); }	// 禁用
	public function trash(   string $uid ):ASResult { return $this->status($uid,'trash');    }	// 垃圾桶
	public function sketch(  string $uid ):ASResult { return $this->status($uid,'sketch');   }    // 草稿箱
	public function offline( string $uid ):ASResult { return $this->status($uid,'offline');  }	// 下线
	public function online(  string $uid ):ASResult { return $this->status($uid,'enabled');  }	// 上线
	public function recover( string $uid ):ASResult { return $this->status($uid,'enabled');  }	// 恢复
	public function done(    string $uid ):ASResult { return $this->status($uid,'done');     }	// 完成
	public function expire(  string $uid ):ASResult { return $this->status($uid,'expired');  }	// 过期
    public function pending( string $uid ):ASResult { return $this->status($uid,'pending');  }	// 等待中

	// 设为精选
	public function setFeature( string $uid, bool $featured = true ): ASResult
    {
		return $this->update(DBValues::init('featured')->bool($featured),$uid);
	}

	// 取消精选
	public function cancelFeature( string $uid ): ASResult
    {
		return $this->setFeature($uid,false);
	}

	// 调整排序
	public function setSort( string $uid, int $sort=0 ): ASResult
    {
		return $this->update(DBValues::init('sort')->number($sort),$uid);
	}

	public function increaseSort( string $uid, int $size = 1 ): ASResult
    {
		return $this->increase( 'sort', static::uidCondition($uid) , $size );
	}

	public function decreaseSort( string $uid, int $size = 1 ): ASResult
    {
		return $this->increase( 'sort', static::uidCondition($uid) , 0 - $size );
	}

    /** 被查看一次 Increase viewtimes */
    public function viewed( string $uid , int $size = 1 ): ASResult
    {
		return $this->increase( 'viewtimes', static::uidCondition($uid) , $size );
    }

    /** 字段增长 Increase number at field */
    public function increase( string $field, DBConditions $conditions = null , float $size = 1 ): ASResult
    {
        return $this->getDB()->increase($field,static::table,$conditions,$size);
    }

    /** 减少字段数值 Decrease number at field */
    public function decrease( string $field, DBConditions $conditions = null , float $size = 1 ): ASResult
    {
        return $this->getDB()->increase($field,static::table,$conditions,0 -$size);
    }

    /** 检测是否当前状态 */
	public function isStatus( string $uid , string $status ): bool
    {
		return $this->count(
		    DBConditions::init(static::table)
                ->where(static::primaryid)->equal($uid)
                ->and('status')->equal($status)
            )->getContent() == 1;
	}

	/** 检测当前ID是否存在数据库中 **/
	public function isExist( string $uid ): bool
    {
		return $this->count(static::uidCondition($uid))->getContent() > 0;
	}

    /**
     * 以数组建立查询条件
     * @param array $filter
     * @return DBConditions
     */
	public static function initConditionFromArray( array $filter ): DBConditions
    {
        $conditions = DBConditions::init(static::table);

        foreach ($filter as $key => $v) {

            if ( in_array($key,static::filterFields) ) {

                switch ( static::tableStruct[ $key ]['type'] ){

                    case DBField_Boolean  :
                        $conditions->and($key)->bool(!!$v);
                        break;
                    case DBField_Int  :
                    case DBField_Float  :
                    case DBField_Double  :
                    case DBField_Decimal  :
                    case DBField_TimeStamp  :

                        $symbol = static::execSymbol($v);
                        if( $symbol === QuerySymbol_None ){
                            $conditions->and($key)->equalIf($v);
                        }else{

                            switch ( $symbol ){

                                case QuerySymbol_In:
                                    $conditions->and($key)->belongTo( $v );
                                    break;
                                case QuerySymbol_Between:
                                    $conditions->and($key)->between( $v[0],$v[1] );
                                    break;
                                case QuerySymbol_Bigger:
                                    $conditions->and($key)->bigger( $v );
                                    break;
                                case QuerySymbol_BiggerAnd:
                                    $conditions->and($key)->biggerAnd( $v );
                                    break;
                                case QuerySymbol_Less:
                                    $conditions->and($key)->less( $v );
                                    break;
                                case QuerySymbol_LessAnd:
                                    $conditions->and($key)->lessAnd( $v );
                                    break;
                                case QuerySymbol_NotEqual:
                                    $conditions->and($key)->notEqual( $v );
                                    break;
                            }

                        }

                        break;
                    case DBField_String  :

                        $symbol = static::execSymbol($v);
                        if( $symbol === QuerySymbol_None ){
                            $conditions->and($key)->equalIf($v);
                        }else {

                            switch ( $symbol ) {
                                case QuerySymbol_Null:
                                    $conditions->and($key)->isNull();
                                    break;
                                case QuerySymbol_NotNull:
                                    $conditions->and($key)->isNotNull();
                                    break;
                                case QuerySymbol_NotEqual:
                                    $conditions->and( $key )->notEqual( $v );
                                    break;
                            }
                        }
                }
            }

        }
        return $conditions;
    }

    /**
     * 处理预定义符号
     * @param $value
     * @return string
     */
    public static function execSymbol( &$value ): string{

        $symbol = QuerySymbol_None;
        if( gettype($value) !=='string' ){ return $symbol; }

        foreach (QuerySymbols as $i => $sym) {

            if( strstr($value, $sym) ){

                $symbol = $sym;

                $value = str_replace($symbol,'',$value);
                switch ( $sym ){
                    case QuerySymbol_In:
                        $value = explode(',',$value);
                        break;
                    case QuerySymbol_Between:
                        $value = explode(',',$value);
                        $value[0] = floatval($value[0]);
                        $value[1] = floatval($value[1]);
                        break;
                    case QuerySymbol_Bigger:
                    case QuerySymbol_BiggerAnd:
                    case QuerySymbol_Less:
                    case QuerySymbol_LessAnd:
                        $value = floatval($value);
                        break;
                    case QuerySymbol_NotEqual:
                        break;
                    case QuerySymbol_Null:
                    case QuerySymbol_NotNull:
                        $value = null;
                        break;
                }
            }
        }
        return $symbol;
    }

    /**
     * 以数组建立数据存储
     * @param array $data
     * @return DBValues
     */
	public static function initValuesFromArray( array $data ): DBValues
    {
        $values = new DBValues();

        foreach ( $data as $key => $v ){

            if ( isset(static::tableStruct[$key]) ){

                if ( isset($v) ){

                    $dbValue = DBValue::init( $key );

                    if( static::tableStruct[$key]['nullable'] ){

                        $symbol = static::execSymbol($v);

                        if ($symbol === QuerySymbol_Null){
                            $dbValue->setNull();

                            $values->add($dbValue);
                            continue;
                        }
                    }

                    switch ( static::tableStruct[$key]['type'] ){

                        case DBField_Boolean  :
                            $dbValue->bool( !!$v );
                            break;
                        case DBField_Int  :
                        case DBField_Float  :
                        case DBField_Double  :
                        case DBField_Decimal  :
                        case DBField_TimeStamp  :
                            $dbValue->equal($v);
                            break;
                        case DBField_String  :
                            $dbValue->string($v);
                            break;
                        case DBField_RichText :
                            $dbValue->richText($v);
                            break;
                        case DBField_Json     :
                            $dbValue->json($v);
                            break;
                        case DBField_ASJson   :
                            $dbValue->ASJson($v);
                            break;
                        case DBField_Location :
                            if( gettype($v)=='string' ){
                                $v = explode(',',$v);
                            }

                            $autoFill = isset(static::tableStruct['lng']);

                            $dbValue->location( floatval( $v['lng'] ?? $v[0] ), floatval($v['lat'] ?? $v[1] ), $autoFill );
                            break;
                    }

                    $values->add($dbValue);
                }
            }
        }
        return $values;
    }



}


