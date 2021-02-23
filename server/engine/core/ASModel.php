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
abstract class ASModel extends ASBase {

    /**
     * 数据表名
     * @var string
     */
	public static $table;

    /**
     * 主索引字段
     * @var string
     */
	public static $primaryid;

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
     * 开启日志
     * @var bool
     */
	protected static $record_enabled = false;

    /**
     * 自动使用REDIS缓存
     * @var bool
     */
    protected static $rds_auto_cache = false;

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

    function __construct(bool $enableRecord = true)
    {
        parent::__construct($enableRecord);
    }

    /**
     * 通用单例
     * common
     * @return static
     */
    public static function common(){
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
     * @param  array  $data
     * @return ASResult
     */
	public function add( array $data ): ASResult
    {
		$data = Filter::purify($data,static::$addFields,static::$depthStruct); // 使用字段数据进行过滤
        $data = Filter::removeInvalid($data);

		if (count($data)<1) { return $this->error(603,i18n('SYS_PARA_REQ'),static::$table.'->add'); }

		$data[static::$primaryid] = isset($data[static::$primaryid]) ? $data[static::$primaryid] : Encrypt::shortId(8);

		if( isset(static::$depthStruct) && in_array('ASJson',array_values(static::$depthStruct))  ){
		    foreach ( static::$depthStruct as $key => $type ){
		        if( $type == 'ASJson' ){
		            $data[$key] = Encrypt::ASJsonEncode( $data[$key] );
                }
            }
        }

        $this->beforeAdd($data);

		$this->DBAdd($data);
		$this->setId($data[static::$primaryid] ?? 'NAN_ID' );
		$this->record('ITEM_ADD',static::$table.'->add');

		if($this->result->isSucceed()){ $this->result->setContent($data[static::$primaryid]); } // 成功返回单位的索引ID

		$this->beforeAddReturn( $this->result, $data );

        static::$rds_auto_cache
        && $this->_clearSet(static::class,'list')
        && $this->_clearSet(static::class,'count');

		return $this->feedback();
	}

	/**
     * 函数注入(插入数据之前)
	 * Do something before add
	 * @param    array (key-value)   &$data          插入数据(指针)
	 */
	public function beforeAdd( array &$data ){  }

    /**
     * 函数注入(插入数据返回结果之前)
     * Do something before add result returning
     * @param ASResult $result  即将返回的结果
     * @param  array          $data    插入数据
     */
	public function beforeAddReturn( ASResult &$result, array $data ){  }


    /**
     * 批量添加
     * adds
     * @param  array  $list
     * @return ASResult
     */
	public function adds( array $list ): ASResult
    {

		$this->beforeAdds($list);

		for ($i=0; $i < count($list); $i++) { 
			if(count($list[$i])>=1){

				$data = Filter::purify($list[$i],static::$addFields); // 使用字段数据进行过滤
                $data = Filter::removeInvalid($data);
				$data[static::$primaryid] = isset($list[$i][static::$primaryid]) ? $list[$i][static::$primaryid] : Encrypt::shortId(8);
				$data['status'] = $list[$i]['status'] ? $list[$i]['status'] : 'enabled';
				$dataList[] = $data;
			}
		}
		if (count($dataList)<1) {
		    return $this->take($list)->error(603,i18n('SYS_PARA_REQ'),static::$table.'->adds');
		}

		$this->DBAdds($dataList);
		$this->setId('IDLIST');
		$this->record('ITEM_ADDS',static::$table.'->adds');

		$this->beforeAddsReturn($this->result, $list);

        static::$rds_auto_cache
        && $this->_clearSet(static::class,'list')
        && $this->_clearSet(static::class,'count');

		return $this->feedback();
	}

	public function beforeAdds( array &$list ){}
	public function beforeAddsReturn( ASResult &$result, array $list ){}

    /**
     * 更新单行数据
     * update data with uid
     * @param  array   $data
     * @param  string  $uid
     * @return ASResult
     */
	public function update( array $data , string $uid ): ASResult
    {

        if( isset(static::$depthStruct) && in_array('ASJson',array_values(static::$depthStruct))  ){
            foreach ( static::$depthStruct as $key => $type ){
                if( $type == 'ASJson' ){
                    $data[$key] = Encrypt::ASJsonEncode( $data[$key] );
                }
            }
        }

		$this->beforeUpdate($data);

		$data = Filter::purify($data,static::$updateFields);

		if (count($data)<1) {
		    return $this->take($uid)->error(603,i18n('SYS_PARA_REQ'),static::$table.'->update');
		}

		$conditions = static::$primaryid."='{$uid}'";

		$this->DBUpdate($data,$conditions);
		$this->setId($uid);
		$this->record('ITEM_UPDATE',static::$table.'->update');

		$this->beforeUpdateReturn( $this->result,$uid );

        static::$rds_auto_cache
        && $this->_clearSet(static::class,'list')
        && $this->getRedis()->remove([$uid,true])
        && $this->getRedis()->remove([$uid,false]);

		return $this->feedback();
	}

	public function beforeUpdate( array &$data ){}
	public function beforeUpdateReturn( ASResult &$result,string $uid ){}

	public function publicUpdate(array $data, string $uid, string $userid ): ASResult
    {

		$DETAIL = $this->detail($uid)->getContent();

		$authorId = $DETAIL['authorid'] ?? $DETAIL['userid'] ?? $DETAIL['receiveid'];

		if( $authorId!==$userid ){
		    return $this->take($userid)->error(9990,i18n('ACC_PROS_CHK_FAL'),"ITEM::publicRemove");
		}

		return $this->update($data,$uid);
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

		$conditions = static::$primaryid."='$uid'";

        $this->setId($uid);
		$this->DBRemove($conditions);
		$this->record('ITEM_REMOVE',static::$table.'->remove');

		$this->beforeRemoveReturn($this->result,$uid);

        static::$rds_auto_cache
        && $this->_clearSet(static::class,'list')
        && $this->_clearSet(static::class,'count')
        && $this->getRedis()->remove([$uid,true])
        && $this->getRedis()->remove([$uid,false]);

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
	public function publicRemove(string $uid, string $userid ): ASResult
    {

		$DETAIL = $this->detail($uid)->getContent();

		$authorId = $DETAIL['authorid'] ?? $DETAIL['userid'] ?? $DETAIL['receiveid'];

		if( $authorId!==$userid ){
		    return $this->take($userid)->error(9990,i18n('ACC_PROS_CHK_FAL'),"ITEM::publicRemove");
		}

		return $this->remove($uid);
	}

    /**
     * 查询唯一数据详情
     * Get detail by itemid
     * @param  string  $uid
     * @param  bool    $public
     * @return ASResult
     */
	public function detail(string $uid , $public = false ): ASResult
    {

        $this->RedisHash = [$uid,$public];

        if( static::$rds_auto_cache && $this->_hasCache() ){
            return $this->_getCache();
        }

		$this->beforeDetail($uid);

		$params = [ 
			'fields'     => ($public === true && isset($this->publicDetailFields) ) ? $this->publicDetailFields : static::$detailFields ,
			'table'      => static::$table,
			'conditions' => static::$primaryid."='{$uid}'"
		];

		$this->DBGet($params['fields'],$params['conditions'],1,1);
		$this->setId($uid);

		if($this->result->isSucceed()){
		    $this->result->setContent($this->convert($this->result->getContent()[0]));
		}

		$this->beforeDetailReturn( $this->result,$uid );

		static::$rds_auto_cache
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

		$this->DBGet($key, [static::$primaryid=>$uid]);
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

		$this->DBGet(static::$overviewFields ?? static::$detailFields ?? '*', [static::$primaryid=>$uid] );
		$this->setId($uid);

        if($this->result->isSucceed()){
            $this->result->setContent($this->convert($this->result->getContent()[0]));
        }

		return $this->feedback();
	}


    /**
     * 统计特定数量
     * count
     * @param  array  $filters
     * @return ASResult
     */
	public function count( array $filters ): ASResult
    {

        $this->RedisHash = $filters;

        if( static::$rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

        $this->beforeCount($filters);

		$conditions = ASDB::spliceCondition(Filter::purify($filters,static::$countFilters));
		$this->DBCount($conditions);

		$this->beforeCountReturn($this->result);

        static::$rds_auto_cache && $this->_cache() && $this->_trackCache(static::class, 'count', $this->RedisHash);

		return $this->feedback();
	}

    /**
     * 查询是否存在对应数据
     * Check data is exist by filters
     * @param  array  $filters
     * @return bool
     */
	public function has( array $filters ):bool{

	    return $this->count($filters)->getContent() > 0;
    }

	public function beforeCount( array &$filters ){  }
	public function beforeCountReturn( ASResult &$result ){  }

    /**
     * 查询对应item表所对应的行数
     * Count contents in item table
     * @param  array   $filters
     * @param  string  $type
     * @return ASResult
     */
	public function countContent( array $filters , string $type ): ASResult
    {

 		return $this->countContentInTable($filters,"item_{$type}");
	}

    /**
     * 查询指定表中所对应的行数
     * Count contents in specific table
     * @param  array   $filters
     * @param  string  $table
     * @return ASResult
     */
	public function countContentInTable( array $filters, string $table ): ASResult
    {

	    $this->setTable($table);

        $conditions = ASDB::spliceCondition($filters);
        $this->DBCount($conditions);

        return $this->feedback();
    }


	/**
     * 获取列表
	 * Get list
	 * @param    array                    $filters        筛选条件
	 * @param    int|integer              $page           翻页-页数
	 * @param    int|integer              $size           翻页-页长
	 * @param    string|null              $sort           排序字段
	 * @param    boolean                  $public         是否公开列表
	 * @return   ASResult                            结果对象
	 */
	public function list( array $filters , int $page=1, int $size=25, string $sort = null , $public = false ): ASResult
    {
		
		$this->RedisHash = [$filters,$page,$size,$sort,$public];

		if( static::$rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

		$this->beforeList( $filters, $page,$size,$sort,$public );

		$conditions = ASDB::spliceCondition(Filter::purify($filters,static::$countFilters));

		$this->DBGet(($public === true && isset(static::$publicListFields) ) ? static::$publicListFields : static::$listFields , $conditions,$page,$size,$sort ?? ' createtime DESC' );

		if($this->result->isSucceed()) {

            $list = $this->result->getContent();

            for ($i = 0; $i < count($list); $i++) {

                $list[$i] = $this->convert($list[$i]);
            }
            $this->result->setContent($list);

            static::$rds_auto_cache && $this->_cache() && $this->_trackCache(static::class, 'list', $this->RedisHash);
        }

		$this->beforeListReturn($this->result);

		return $this->feedback();
	}

	public function beforeList( array &$filters, int &$page=1, int &$size=25, string &$sort = null , &$public = false ){  }
	public function beforeListReturn( ASResult &$result ){  }

	// 获取公开接口列表
	public function publicList( array $filters , int $page=1, int $size=25, string $sort = null ): ASResult
    {

		return $this->list($filters,$page,$size,$sort,true);
	}

	/**
     * 多表联合统计
	 * joinCount
	 * @param    array                    $filters        [主表条件]
     * @param  JoinParams[]|array|string[]   $mergeJoins
     * @param  JoinParams[]|array|string[]   $subJoins
	 * @return   ASResult
	 */
	public function joinCount(  array $filters = null, array $mergeJoins = null, array $subJoins = null ): ASResult
    {

	    $primaryParams = JoinPrimaryParams::common(static::class);
	    if( isset($filters) ){ $primaryParams->withResultFilter($filters); }
		return $this->advancedJoinCount( $primaryParams ,$mergeJoins,$subJoins );
	}

    /**
     * 多表联合查询统计 完整模式
     * advancedJoinCount
     * @param  JoinPrimaryParams $primaryParams
     * @param  JoinParams[]|array|string[]   $mergeJoins
     * @param  JoinParams[]|array|string[]   $subJoins
     * @return ASResult
     */
	public function advancedJoinCount( JoinPrimaryParams $primaryParams = null, array $mergeJoins = null, array $subJoins = null ): ASResult
    {

		$this->beforeJoinCount( $primaryParams,$mergeJoins,$subJoins );

		$joinParams = array_merge( static::fillJoinParams($mergeJoins),static::fillJoinParams($subJoins,true) );
		
		$this->DBJoinCount( $primaryParams, $joinParams );

		$this->beforeJoinCountReturn( $this->result );

		return $this->feedback();

	}

	public function beforeJoinCount( JoinPrimaryParams &$filters = null, array &$mergeJoins = null, array &$subJoins = null ){  }
	public function beforeJoinCountReturn( ASResult &$result ){  }


	/**
     * 多表联合检测是否存在
	 * joinHas
	 * @param    array|null               $filters        主表条件
	 * @param    JoinParams[]|null   $joins          合并查询表
	 * @return   boolean
	 */
	public function joinHas(  array $filters = null, array $joins = null ): bool
    {

	    $primaryParams = JoinPrimaryParams::common( static::class );
	    $primaryParams->withResultFilter($filters);

	    $joinParams = [];

		foreach ($joins as $key => $jParams) {

		    if( isset($jParams->conditions) ){
		        $joinParams[] = $jParams;
            }
		}

		$this->DBJoinCount( $primaryParams, $joinParams );

		return $this->result->getContent() > 0;
		
	}

	/**
     * 多表联合查询
	 * joinList
	 * @param    array|null               $filters        主表条件
	 * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
	 * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
	 * @param    int|integer              $page           页
	 * @param    int|integer              $size           大小
	 * @param    string|null              $sort           排序
	 * @return   ASResult
	 */
	public function joinList( array $filters = null, array $mergeJoins = null, array $subJoins = null, int $page = 1, int $size = 20, string $sort = null ): ASResult
    {

        $primaryParams = JoinPrimaryParams::common(static::class);
        if( isset($filters) ){ $primaryParams->withResultFilter($filters); }

		return $this->advancedJoinList($primaryParams,$mergeJoins,$subJoins,$page,$size,$sort);
	}

    /**
     * Description
     * advancedJoinList
     * @param JoinPrimaryParams|null  $primaryParams
     * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
     * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
     * @param  int                          $page
     * @param  int                          $size
     * @param  string|null                  $sort
     * @return ASResult|mixed
     */
	public function advancedJoinList( JoinPrimaryParams $primaryParams = null, array $mergeJoins = null, array $subJoins = null, int $page =1, int $size = 20, string $sort = null ): ASResult
    {
		
		$this->RedisHash = [$primaryParams->toArray(),JoinParams::listToArrayList($mergeJoins),JoinParams::listToArrayList($subJoins),$page,$size,$sort];
		
		if( static::$rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

		$this->beforeJoinList($primaryParams,$mergeJoins,$subJoins,$page,$size,$sort);

		$joinParams = array_merge( static::fillJoinParams($mergeJoins),static::fillJoinParams($subJoins,true) );
		
		$this->DBJoinGet( $primaryParams, $joinParams, $page, $size, $sort );

		if($this->result->isSucceed()){

            $list = $this->result->getContent();

            for ($i=0; $i < count($list); $i++) {

                $list[$i] = $this->convert($list[$i]);

                if(isset($subJoins)){
                    foreach ($subJoins as $key => $jParams) {

                        $CLASS = $jParams->modelClass;
                        $list[$i][$jParams->alias] = $this->convert($list[$i][$jParams->alias],$CLASS::$depthStruct);
                    }
                }
            }
            $this->result->setContent($list);

			$this->_cache();
		}

		$this->beforeJoinListReturn($this->result);

		return $this->feedback();

	}

	public function beforeJoinList( JoinPrimaryParams &$primaryParams = null, array &$mergeJoins = null, array &$subJoins = null, int &$page=1, int &$size=25, string &$sort = null ){  }
	public function beforeJoinListReturn( ASResult &$result ){  }


    /**
     * 多表联合详情
     * joinDetail
     * @param  string      $uid      索引ID
     * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
     * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
     * @return   ASResult
     */
	public function joinDetail(string $uid, array $mergeJoins = null, array $subJoins = null ): ASResult
    {
		
		$this->RedisHash = [$uid,JoinParams::listToArrayList($mergeJoins),JoinParams::listToArrayList($subJoins)];
		
		if( static::$rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

		$this->beforeJoinDetail($uid,$mergeJoins,$subJoins);

		$primaryParams = JoinPrimaryParams::common(static::class)->get(static::$detailFields)->withResultFilter([static::$primaryid=>$uid]);

		$joinParams = array_merge( static::fillJoinParams($mergeJoins),static::fillJoinParams($subJoins,true) );
		
		$this->DBJoinGet( $primaryParams, $joinParams, 1, 1 );
		$this->setId($uid);

		if($this->result->isSucceed()){

			$detail = $this->convert($this->result->getContent()[0]);

            if(isset($subJoins)){
                foreach ($subJoins as $key => $jParams) {

                    $CLASS = $jParams->modelClass;
                    $detail[$jParams->alias] = $this->convert($detail[$jParams->alias],$CLASS::$depthStruct);
                }
            }

			$this->result->setContent( $detail );
			static::$rds_auto_cache && $this->_cache();
		}

		$this->beforeJoinDetailReturn($this->result);

		return $this->feedback();
	}

    /**
     * 查询前参数处理
     * beforeJoinDetail
     * @param  string      $uid
     * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
     * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
     */
	public function beforeJoinDetail(string &$uid, array &$mergeJoins = null, array &$subJoins = null ){  }
	public function beforeJoinDetailReturn( ASResult &$result ){  }


	/**
     * 自动完善JOIN参数
	 * fillJoinParams
     * @param    JoinParams[]|array|string[]  $joins      参数
     *                                                    Quick Mode  ['APS\UserInfo','APS\UserGroup']
     *                                                    k-v   Mode  ['info'=>'APS\UserInfo','group'=>'APS\UserGroup']
     *                                                    Full  Mode  [ JoinParams,JoinParams... ]
	 * @param    bool|boolean             $isSubJoin      是否作为子集结果
	 * @return   array                                    结果参数
	 */
	public function fillJoinParams( array $joins = null , bool $isSubJoin = false ): array
    {

		$joinParamsArray = [];

		if( !isset($joins) ){ return $joinParamsArray; }

		foreach ($joins as $k => $v) {

		    # 提供三种模式 Quick, k-v, Full

            if( is_integer($k) && is_string($v) ){

                # Quick Mode 快速模式
                $joinParam = JoinParams::init( $v );

                if( $isSubJoin ){
                    $joinParam->asSubData( $v );
                }

            }else if( is_string($k) && is_string($v) ){

                # K-v Mode
                $joinParam = JoinParams::init( $v );

                if( $isSubJoin ){
                    $joinParam->asSubData( $k );
                }
            }else if( is_integer($k) && gettype($v)=='object' && get_class($v)=='APS\JoinParams' ){

                # Full mode
                $joinParam = $v;
                if( $isSubJoin ){
                    $joinParam->asSubData( $joinParam->alias ?? $joinParam->modelClass );
                }
            }else{
                _ASError()->add(ASResult::shared(800,'Not valid params',$v));
            }

			$joinParamsArray[] = $joinParam;
		}
		return $joinParamsArray;
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
     * @param  string|null  $hash  缓存哈希
     * @return bool
     */
    public function _hasCache( $hash = null ): bool
    {

        return $this->_isCacheEnabled() && $this->getRedis()->has( $hash ?? $this->RedisHash );
    }

    /**
     * 取出缓存
     * Get cache from Redis server
     * @param  string|null  $hash
     * @return mixed
     */
    public function _getCache( $hash = null ){
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


	// 搜索
	public function search( string $keyword, array $filters = null , int $page=1, int $size=25, string $sort = null ): ASResult
    {

		$filters = $filters ? $filters : [];
		$filters['KEYWORD'] = $keyword;

		return $this->list($filters,$page,$size,$sort);

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

		if( !$struct && !static::$depthStruct ){ return $data; }

		$struct = $struct ?? static::$depthStruct;

		foreach ( $struct as $key => $value ) {
		
			if(isset($data[$key])){

				if(gettype($value)=="array"){

					$data[$key] = gettype($struct[$key])=='array' ? $struct[$key] : json_decode($struct[$key],true);
					$data[$key] = $this->convert($data[$key],$value);

				}else{

					switch ($value) {
						case 'int':
						$data[$key] = (int)$data[$key];
						break;
						case 'double':
						$data[$key] = (double)$data[$key];
						break;
						case 'json':
						$data[$key] = json_decode($data[$key],true);
						break;
						case 'ASjson':
						case 'ASJson':
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

		return $this->update(['status'=>$status],$uid);
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
    public function pedding( string $uid ):ASResult { return $this->status($uid,'pedding');  }	// 等待中?

	// 设为精选
	public function setFeature( string $uid, int $featured=1 ): ASResult
    {

		return $this->update(['featured'=>$featured],$uid);

	}

	// 取消精选
	public function cancelFeature( string $uid ): ASResult
    {

		return $this->update(['featured'=>0],$uid);

	}

	// 调整排序
	public function setSort( string $uid, int $sort=0 ): ASResult
    {

		return $this->update(['sort'=>$sort],$uid);

	}

	public function increaseSort( string $uid, int $size = 1 ): ASResult
    {

		return $this->increase( 'sort', static::$primaryid."='{$uid}'" , $size );

	}

	public function decreaseSort( string $uid, int $size = 1 ): ASResult
    {

		return $this->increase( 'sort', static::$primaryid."='{$uid}'" , 0 - $size );

	}

    // view 被查看一次
    public function view( string $uid , int $size = 1 ): ASResult
    {

		return $this->increase( 'viewtimes', static::$primaryid."='{$uid}'" , $size );

    }

    // 字段增长
    public function increase( string $field, $conditions = null , float $size = 1 ): ASResult
    {

        return $this->getDB()->increase($field,static::$table,$conditions,$size);
    }

    // 字段减少
    public function decrease( string $field, $conditions = null , float $size = 1 ): ASResult
    {

        return $this->getDB()->increase($field,static::$table,$conditions,0 -$size);
    }

	// 检测是否当前状态
	public function isStatus( string $uid , string $status ): bool
    {

		return $this->count([static::$primaryid=>$uid,'status'=>$status])->getContent() == 1;

	}

	// 检测当前ID是否存在数据库中
	public function isExist( string $uid ): bool
    {

		return $this->count([static::$primaryid=>$uid])->getContent() > 0;
	}

}


