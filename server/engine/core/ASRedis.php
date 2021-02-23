<?php
/**
 * @name:           Redis io
 * @version:        1.1.0
 * @author:         Sprite
 * @copyright:      动息科技,DonseeTec
 * @website:        https://donsee.cn
 * @date:           2018.7.11
 */

namespace APS;

/**
 * 内存数据库 ASRedis
 *
 * 当前主要解决 数据库缓存 php-redis扩展 -> https://github.com/phpredis/phpredis
 * - REDIS for 持久化数据支持 可以将大量重复查询 缓存到REDIS服务器 降低数据库压力
 * - 根据请求的具体参数生成hashid 每个hash有效期 由服务器默认配置 可以针对每一个temp进行expire设置
 * - Cache是存储到Key String中, 而缓存跟踪是存储到Set中的
 *
 * @package APS\core
 */
class ASRedis extends ASObject{

	private $host;
	private $port;
	private $password;

	private $dbId = 0;

    /**
     * Redis链接
     * @var \Redis | null
     */
	private $connection;

	private $connected = false;
	private $enabled = 0;

    /**
     * 功能可开启
     * -1 无法开启 Not supported , 0 未开启 Not connect , 1 正常 Enabled
     * @var int
     */
    private $isEnabled;

    public static function shared():ASRedis{

        if ( !isset($GLOBALS['ASRedis']) ){
            $GLOBALS['ASRedis'] = new ASRedis();
        }
        return $GLOBALS['ASRedis'];
	}

    /**
     * 实例化并尝试连接
     * Construct with auto connect test
     * @param  string|null  $host
     * @param  int|null     $port
     * @param  string|null  $password
     * @param  int          $dbId
     */
	function __construct( string $host = null, int $port = null, string $password = null, int $dbId = 0 ){

	    parent::__construct();

	    $this->result->setSign("ASRedis");
        $this->host = $host ?? CONFIG['REDIS_HOST'] ?? '127.0.0.1';
        $this->port = $port ?? CONFIG['REDIS_PORT'] ?? 6379;
		$this->password = $password;
        $this->dbId = $dbId ?? CONFIG['REDIS_DB'] ?? 1;

		$this->connect();
	}

    /**
     * 切换Redis数据库
     * selectDB
     * @param  int  $dbId
     */
	public function selectDB( int $dbId = 0 ){
        $this->dbId = $dbId;
        if( $this->connected ){
            $this->connection->select($this->dbId);
        }
    }

	/**
     * 连接REDIS数据库
	 * connect to Redis
	 * @return   ASRedis ( 连接尝试后会修改 enabled属性 成功1 失败-1 默认0 )
	 */
	public function connect(): ASRedis
    {

		if(!class_exists('Redis')){
            _ASError()->add( $this->error(2000,'Redis Class not exist.','ASRedis->connect') );
			$this->enabled = -1;
			return $this;
		}

		if( !$this->connected ){
			$redis = new \Redis();
			try {
				$connect = $redis->connect($this->host,$this->port);
				if($this->password){ $redis->auth($this->password); }
				if($connect){
					$this->connection = $redis;
					$this->connected = true;
					$this->enabled = 1;
					if( $this->dbId ){ $this->connection->select($this->dbId); }
				}else{
					$this->enabled = -1;
				}
			} catch (\Exception $e) {
                _ASError()->add( $this->error(2001,'Connect to Redis Server failed.','ASRedis->connect') );
				$this->enabled = -1;
			}
		}
		return $this;
	}

	/**
     * 是否可用
	 * isEnabled
	 * @return   boolean
	 */
	public function isEnabled(): bool
    {

		$this->connect();

		return $this->enabled > 0;

	}

	/**
     * 关闭连接
	 * close
	 * @return   boolean
	 */
	public function close(): bool
    {

		if( $this->connected ){

			$close = $this->connection->close();
			if($close){
				$this->connection = null;
				$this->connected = false;
				$this->isEnabled = 0;
			}
			return $close;
		}
		return false;
	}

	/**
     * 缓存数据
	 * cache
	 * @param    mixed                 $params         操作参数(将自动转为hash)
	 * @param    mixed                 $value          值
	 * @param    int                   $expireDuration 有效时间
	 * @return   bool
	 */
	public function cache( $params, $value , int $expireDuration = 3600 ): bool
    {

		$this->sign('ASRedis->cache');

		$hashID = is_string($params) ? $params : Encrypt::hashID($params);

		return $this->set($hashID,Encrypt::ASJsonEncode($value),$expireDuration);

	}

	/**
     * 是否存在缓存
	 * Is a cache exist
	 * @param    mixed           $params         查询参数
	 * @return   boolean
	 */
	public function has( $params ): bool
    {

		$this->sign('ASRedis->has');
		$hashID = is_string($params) ? $params : Encrypt::hashID($params);
		return $this->isExist($hashID);
	}

	/**
     * 获取缓存
	 * read cache
	 * @param    mixed           $params     查询参数
	 * @return   ASResult
	 */
	public function read( $params ): ASResult
    {

		$this->sign('ASRedis->read');
		$hashID = is_string($params) ? $params : Encrypt::hashID($params);
		return $this->isExist($hashID) ?
				$this->take(Encrypt::ASJsonDecode($this->get($hashID)))->success('Cache read.') :
				$this->error(400,'Cache not exist.');

	}

	/**
     * 缓存追踪
	 * track cache
     * 基于Cache只能由cacheId索引、不具备关系数据库的结构，所以一个ID内容可能在多个缓存中被使用，更新内容时我们则应当通知删除或更新缓存
	 * 缓存追踪用于追踪一个相关的ID内容是否被缓存，分别在哪些缓存中
	 * setid用于标识uniqid所在的集合(数据表名称或是某个数据用法) 如 user:userid , media:imageid
	 * 这里的setid不应该过于细化、应当是多个相关表数据集合后的统称，否则追踪难度较高 容易在通知丢失目标、遗漏通知
	 * 基本原则是 相互独立的模块，独立追踪。 如:鉴权不会影响用户资料， 订单不会影响用户资料。
	 * 例: ACCESS:userid , USER:userid, ORDER:userid 可以分别用于鉴权模块、用户信息模块、订单模块的跟踪。 无需再进一步细分。
	 * @DateTime 2019-08-28T02:42:04+0800
	 * @param    string  $setid     集合ID
	 * @param    string  $uniqueId  表唯一ID
	 * @param    mixed   $hashID    缓存ID
	 * @return   boolean
	 *@version  1.0
	 */
	public function track(string $setid, string $uniqueId, $hashID ): bool
    {

		if( gettype($hashID) == 'array' ){ $hashID = is_string($hashID) ? $hashID : Encrypt::hashID($hashID); }
		return $this->connection->sAdd("$setid:$uniqueId",$hashID);
	}

	public function update( string $setid, string $uniqid  ){

	}

	/**
     * 缓存清理
	 * clear
	 * @param    string                   $setid          集合ID
	 * @param    string                   $uniqid         表唯一ID
	 * @return bool
	 */
	public function clear( string $setid, string $uniqid ): bool
    {

		$this->sign('ASRedis->clear');

		$setID = "$setid:$uniqid";

		if($this->connection->sCard($setID)==0){ return true; }
		
		$cacheIds = $this->connection->sMembers($setID);
		$this->connection->unlink($cacheIds);

		foreach ($cacheIds as $i => $cacheid) {
			 $this->connection->sRem($setID,$cacheid);
		}

		return $this->connection->sCard($setID)===0;
	}

	/**
     * 获取Key/String
	 * @param    string                   $key            查询key
	 * @return   string|false
	 */
	public function get( string $key ){
		return $this->connection->get($key);
	}

    /**
     * 存储Key/String
     * set key & value
     * @param  string       $key
     * @param  string       $value
     * @param  int          $expireDuration
     * @return bool
     */
	public function set( $key , $value , int $expireDuration = 0 ): bool
    {

		if( $expireDuration>0 ){

			return $this->connection->setEx( $key , $expireDuration , $value );

		}else{

			return $this->connection->set( $key , $value );
		}

	}

	public function increase( $key , $number = 1 ){

		return gettype($number)=='double' ? $this->connection->incrByFloat( $key , $number ) : $this->connection->incrBy( $key , $number );

	}

	public function remove( $key ): int
    {

		return $this->connection->del($key);

	}

	public function timeRemain($key){

		return $this->connection->ttl($key);

	}

	public function isExist( $key ){

		return $this->connection->exists($key);

	}

    /**
     * ping
     * @return string|null
     */
	public function ping(){

        try {
            return $this->connection->ping();
        } catch (\RedisException $e) {
            return null;
        }
    }

	public function analysisKeys(): array
    {

		$keys = $this->connection->keys('*');

		$analysis = [];

		foreach ($keys as $i => $key) {
			
			$analysis[] = ['key'=>$key,'len'=>strlen($key),'valuelen'=>$this->connection->strLen($key)];

		}

		return $analysis;
	}

	public function info( bool $listArray = false ){

		$info = $this->connection->info();

		if( $listArray ){
			$list = [];

			foreach ($info as $key => $value) {
				$list[] = ['key'=>$key,'value'=>$value];
			}
		}
		return $listArray ? $list : $info;

	}

	public function flush(): bool
    {

		return $this->connection->flushDb();

	}


}

