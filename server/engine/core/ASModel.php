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
     * @var \APS\ASRedis
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
     * @return \APS\ASResult
     */
	public function add( array $data )
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
     * @param  \APS\ASResult  $result  即将返回的结果
     * @param  array          $data    插入数据
     */
	public function beforeAddReturn( ASResult &$result, array $data ){  }


    /**
     * 批量添加
     * adds
     * @param  array  $list
     * @return \APS\ASResult
     */
	public function adds( array $list ){

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
     * update data with itemid
     * @param  array   $data
     * @param  string  $itemid
     * @return \APS\ASResult
     */
	public function update( array $data , string $itemid ){

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
		    return $this->take($itemid)->error(603,i18n('SYS_PARA_REQ'),static::$table.'->update');
		}

		$conditions = static::$primaryid."='{$itemid}'";

		$this->DBUpdate($data,$conditions);
		$this->setId($itemid);
		$this->record('ITEM_UPDATE',static::$table.'->update');

		$this->beforeUpdateReturn( $this->result,$itemid );

        static::$rds_auto_cache
        && $this->_clearSet(static::class,'list')
        && $this->getRedis()->remove([$itemid,true])
        && $this->getRedis()->remove([$itemid,false]);

		return $this->feedback();
	}

	public function beforeUpdate( array &$data ){}
	public function beforeUpdateReturn( ASResult &$result,string $itemid ){}

	public function publicUpdate( array $data, string $itemid, string $userid ){

		$DETAIL = $this->detail($itemid)->getContent();

		$authorId = $DETAIL['authorid'] ?? $DETAIL['userid'] ?? $DETAIL['receiveid'];

		if( $authorId!==$userid ){
		    return $this->take($userid)->error(9990,i18n('ACC_PROS_CHK_FAL'),"ITEM::publicRemove");
		}

		return $this->update($data,$itemid);
	}

    /**
     * 移除
     * remove
     * @param  string  $itemid
     * @return \APS\ASResult
     */
	public function remove( string $itemid ){

		$this->beforeRemove($itemid);

		$conditions = static::$primaryid."='$itemid'";

        $this->setId($itemid);
		$this->DBRemove($conditions);
		$this->record('ITEM_REMOVE',static::$table.'->remove');

		$this->beforeRemoveReturn($this->result,$itemid);

        static::$rds_auto_cache
        && $this->_clearSet(static::class,'list')
        && $this->_clearSet(static::class,'count')
        && $this->getRedis()->remove([$itemid,true])
        && $this->getRedis()->remove([$itemid,false]);

		return $this->feedback();
	}

	public function beforeRemove( string &$itemid ){}
	public function beforeRemoveReturn( ASResult &$result,string $itemid ){}

    /**
     * 开放接口移除
     * publicRemove
     * @param  string  $itemid
     * @param  string  $userid
     * @return \APS\ASResult
     */
	public function publicRemove( string $itemid, string $userid ){

		$DETAIL = $this->detail($itemid)->getContent();

		$authorId = $DETAIL['authorid'] ?? $DETAIL['userid'] ?? $DETAIL['receiveid'];

		if( $authorId!==$userid ){
		    return $this->take($userid)->error(9990,i18n('ACC_PROS_CHK_FAL'),"ITEM::publicRemove");
		}

		return $this->remove($itemid);
	}

    /**
     * 查询唯一数据详情
     * Get detail by itemid
     * @param  string  $itemid
     * @param  bool    $public
     * @return \APS\ASResult
     */
	public function detail( string $itemid , $public = false ){

        $this->RedisHash = [$itemid,$public];

        if( static::$rds_auto_cache && $this->_hasCache() ){
            return $this->_getCache();
        }

		$this->beforeDetail($itemid);

		$params = [ 
			'fields'     => ($public === true && isset($this->publicDetailFields) ) ? $this->publicDetailFields : static::$detailFields ,
			'table'      => static::$table,
			'conditions' => static::$primaryid."='{$itemid}'"
		];

		$this->DBGet($params['fields'],$params['conditions'],1,1);
		$this->setId($itemid);

		if($this->result->isSucceed()){
		    $this->result->setContent($this->convert($this->result->getContent()[0]));
		}

		$this->beforeDetailReturn( $this->result,$itemid );

		static::$rds_auto_cache
        && $this->result->isSucceed()
        && $this->_cache();

		return $this->feedback();
	}

	public function beforeDetail( string &$itemid ){}
	public function beforeDetailReturn( ASResult &$result, string $itemid ){}


    /**
     * 获取公开接口详情
     * Detail for with public fields
     * @param  string  $itemid
     * @return \APS\ASResult
     */
	public function publicDetail( string $itemid ){

		return $this->detail($itemid,true);
	}	


    /**
     * 通过ID获取对应字段值
     * get value at $key field by itemid
     * @param  string  $key
     * @param  string  $itemid
     * @return \APS\ASResult
     */
	public function get( string $key , string $itemid ){

		$this->beforeGet($key,$itemid);

		$this->DBGet($key, [static::$primaryid=>$itemid]);
		$this->setId($itemid);

        if($this->result->isSucceed()){ $this->result->setContent($this->convert($this->result->getContent()[0][$key])) ; }

		$this->beforeGetReturn( $this->result, $itemid );

		return $this->feedback();
	}

	public function beforeGet( string &$key, string &$itemid ){}
	public function beforeGetReturn( ASResult &$result, string $itemid ){}


    /**
     * 获取概览
     * overview
     * @param  string  $itemid
     * @return \APS\ASResult
     */
	public function overview( string $itemid )
	{

		$this->DBGet(static::$overviewFields ?? static::$detailFields ?? '*', [static::$primaryid=>$itemid] );
		$this->setId($itemid);

        if($this->result->isSucceed()){
            $this->result->setContent($this->convert($this->result->getContent()[0]));
        }

		return $this->feedback();
	}


    /**
     * 统计特定数量
     * count
     * @param  array  $filters
     * @return \APS\ASResult
     */
	public function count( array $filters ){

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
     * @return \APS\ASResult
     */
	public function countContent( array $filters , string $type ){

 		return $this->countContentInTable($filters,"item_{$type}");
	}

    /**
     * 查询指定表中所对应的行数
     * Count contents in specific table
     * @param  array   $filters
     * @param  string  $table
     * @return \APS\ASResult
     */
	public function countContentInTable( array $filters, string $table ){

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
	 * @return   \APS\ASResult                            结果对象
	 */
	public function list( array $filters , int $page=1, int $size=25, string $sort = null , $public = false ){
		
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
	public function publicList( array $filters , int $page=1, int $size=25, string $sort = null ){

		return $this->list($filters,$page,$size,$sort,true);
	}

	/**
     * 多表联合统计
	 * joinCount
	 * @param    array                    $filters        [主表条件]
     * @param  JoinParams[]|array|string[]   $mergeJoins
     * @param  JoinParams[]|array|string[]   $subJoins
	 * @return   \APS\ASResult
	 */
	public function joinCount(  array $filters = null, array $mergeJoins = null, array $subJoins = null ){

	    $primaryParams = JoinPrimaryParams::common(__CLASS__);
	    if( isset($filters) ){ $primaryParams->withResultFilter($filters); }
		return $this->advancedJoinCount( $primaryParams ,$mergeJoins,$subJoins );
	}

    /**
     * 多表联合查询统计 完整模式
     * advancedJoinCount
     * @param  JoinPrimaryParams $primaryParams
     * @param  JoinParams[]|array|string[]   $mergeJoins
     * @param  JoinParams[]|array|string[]   $subJoins
     * @return \APS\ASResult
     */
	public function advancedJoinCount( JoinPrimaryParams $primaryParams = null, array $mergeJoins = null, array $subJoins = null ){

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
	 * @param    \APS\JoinParams[]|null   $joins          合并查询表
	 * @return   boolean
	 */
	public function joinHas(  array $filters = null, array $joins = null ){

	    $primaryParams = JoinPrimaryParams::common( __CLASS__ );
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
	 * @return   \APS\ASResult
	 */
	public function joinList( array $filters = null, array $mergeJoins = null, array $subJoins = null, int $page = 1, int $size = 20, string $sort = null ){

        $primaryParams = JoinPrimaryParams::common(__CLASS__);
        if( isset($filters) ){ $primaryParams->withResultFilter($filters); }

		return $this->advancedJoinList($primaryParams,$mergeJoins,$subJoins,$page,$size,$sort);
	}

    /**
     * Description
     * advancedJoinList
     * @param  \APS\JoinPrimaryParams|null  $primaryParams
     * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
     * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
     * @param  int                          $page
     * @param  int                          $size
     * @param  string|null                  $sort
     * @return \APS\ASResult|mixed
     */
	public function advancedJoinList( JoinPrimaryParams $primaryParams = null, array $mergeJoins = null, array $subJoins = null, int $page =1, int $size = 20, string $sort = null ){
		
		$this->RedisHash = [$primaryParams,$mergeJoins,$subJoins,$page,$size,$sort];
		
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
     * @param  string      $itemid      索引ID
     * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
     * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
     * @return   \APS\ASResult
     */
	public function joinDetail( string $itemid, array $mergeJoins = null, array $subJoins = null ){
		
		$this->RedisHash = [$itemid,$mergeJoins,$subJoins];
		
		if( static::$rds_auto_cache && $this->_hasCache() ){ return $this->_getCache(); }

		$this->beforeJoinDetail($itemid,$mergeJoins,$subJoins);

		$primaryParams = JoinPrimaryParams::common(static::class)->get(static::$detailFields)->withResultFilter([static::$primaryid=>$itemid]);

		$joinParams = array_merge( static::fillJoinParams($mergeJoins),static::fillJoinParams($subJoins,true) );
		
		$this->DBJoinGet( $primaryParams, $joinParams, 1, 1 );
		$this->setId($itemid);

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
     * @param  string      $itemid
     * @param    JoinParams[]|array|string[]  $mergeJoins     合并查询表
     * @param    JoinParams[]|array|string[]  $subJoins       合并查询表(子集)
     */
	public function beforeJoinDetail( string &$itemid, array &$mergeJoins = null, array &$subJoins = null ){  }
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
	public function fillJoinParams( array $joins = null , bool $isSubJoin = false ){

		$joinParamsArray = [];

		if( !isset($joins) ){ return $joinParamsArray; }

		foreach ($joins as $k => $v) {

		    # 提供三种模式 Quick, k-v, Full

            if( is_integer($k) && is_string($v) ){

                # Quick Mode 快速模式
                $joinParam = JoinParams::common( $v );

                if( $isSubJoin ){
                    $joinParam->asSubData( $v );
                }

            }else if( is_string($k) && is_string($v) ){

                # K-v Mode
                $joinParam = JoinParams::common( $v );

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
    public function _isCacheEnabled(){

        return $this->getRedis()->isEnabled();
    }

    /**
     * 是否存在对应缓存数据
     * _hasCache
     * @param  string|null  $hash  缓存哈希
     * @return bool
     */
    public function _hasCache( $hash = null ){

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
     * @param  \APS\ASResult    $result
     * @param  int    $expireDuration
     * @return bool
     */
    public function _cache( $hash = null, ASResult $result = null, int $expireDuration = 3600  ){
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
    public function _trackCache( $set,$id,$hashID ){

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
    public function _clearSet( $set,$id ){
        if(!$this->getRedis()->isEnabled()){ return false; }
        return $this->getRedis()->clear($set,$id);
    }


	// 搜索
	public function search( string $keyword, array $filters = null , int $page=1, int $size=25, string $sort = null ){

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
	public function convert( array $data , array $struct = null ){

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
     * @param  string  $itemid
     * @param  string  $status
     * @return \APS\ASResult
     */
	public function status( string $itemid, string $status ){

		$this->beforeStatus($itemid,$status);

		return $this->update(['status'=>$status],$itemid);
	}

	public function beforeStatus( string &$itemid, string &$status ){  }

	public function block(   string $itemid ){ return $this->status($itemid,'disabled'); }	// 禁用
	public function trash(   string $itemid ){ return $this->status($itemid,'trash');    }	// 垃圾桶
	public function sketch(  string $itemid ){ return $this->status($itemid,'sketch');   } // 草稿箱
	public function offline( string $itemid ){ return $this->status($itemid,'offline');  }	// 下线
	public function online(  string $itemid ){ return $this->status($itemid,'enabled');  }	// 上线
	public function recover( string $itemid ){ return $this->status($itemid,'enabled');  }	// 恢复
	public function done(    string $itemid ){ return $this->status($itemid,'done');     }	// 完成
	public function expire(  string $itemid ){ return $this->status($itemid,'expired');  }	// 过期
    public function pending( string $itemid ){ return $this->status($itemid,'pending');  }	// 等待中
    public function pedding( string $itemid ){ return $this->status($itemid,'pedding');  }	// 等待中?

	// 设为精选
	public function setFeature( string $itemid, int $featured=1 ){

		return $this->update(['featured'=>$featured],$itemid);

	}

	// 取消精选
	public function cancelFeature( string $itemid ){

		return $this->update(['featured'=>0],$itemid);

	}

	// 调整排序
	public function setSort( string $itemid, int $sort=0 ){

		return $this->update(['sort'=>$sort],$itemid);

	}

	public function increaseSort( string $itemid, int $size = 1 ){

		return $this->increase( 'sort', static::$primaryid."='{$itemid}'" , $size );

	}

	public function decreaseSort( string $itemid, int $size = 1 ){

		return $this->increase( 'sort', static::$primaryid."='{$itemid}'" , 0 - $size );

	}

    // view 被查看一次
    public function view( string $itemid , int $size = 1 ){

		return $this->increase( 'viewtimes', static::$primaryid."='{$itemid}'" , $size );

    }

    // 字段增长
    public function increase( string $field, $conditions = null , float $size = 1 ){

        return $this->getDB()->increase($field,static::$table,$conditions,$size);
    }

    // 字段减少
    public function decrease( string $field, $conditions = null , float $size = 1 ){

        return $this->getDB()->increase($field,static::$table,$conditions,0 -$size);
    }

	// 检测是否当前状态
	public function isStatus( string $itemid , string $status ){

		return $this->count([static::$primaryid=>$itemid,'status'=>$status])->getContent() == 1;

	}

	// 检测当前ID是否存在数据库中
	public function isExist( string $itemid ){

		return $this->count([static::$primaryid=>$itemid])->getContent() > 0;
	}

}


