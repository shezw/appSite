<?php

namespace APS;

use mysqli_result;

/**
 * MySQL数据库控制器
 * DBController for MySQL
 *
 * MYSQL >= 5.7.0
 * 服务器要开启ngram插件
 * Ngram plugin is Required
 *
 * @version:        1.2.0
 * @author:         Sprite
 * @package APS\core
 */
class ASDB extends ASObject{

    /**
     * 主机地址 host
     * @var string
     */
    private $host;

    /**
     * 用户名 username
     * @var string
     */
    private $user;

    /**
     * 密码 password
     * @var string
     */
    private $pass;

    /**
     * 数据库名 Database Name
     * @var string
     */
    private $base;

    /**
     * 数据库连接 Connection
     * @var \mysqli | null
     */
    private $connect;

    /**
     * 已连接 isConnected
     * @var bool
     */
    public $connected = false;

    public $valid = true;

    /**
     * 预定义查询标记
     *
     * Predefined query symbols
     */

    /**
     * 全局单例
     * @param  ASDB|null  $specificDB 指定数据库 更新到全局共享
     * @return ASDB
     */
    public static function shared( ASDB $specificDB = null  ):ASDB{

        if( isset($specificDB) ){
            $GLOBALS['ASDB'] = $specificDB;
        }
        if ( !isset($GLOBALS['ASDB']) ){
            $GLOBALS['ASDB'] = new ASDB();
        }
        return $GLOBALS['ASDB'];
    }

    /**
     * __construct 初始化
     *
     * 默认采用全局数据库配置
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-08-17T14:29:38+0800
     * @param    string|null              $host           地址
     * @param    string|null              $user           用户名
     * @param    string|null              $pass           密码
     * @param    string|null              $base           数据库
     *
     * EG: $DB = new SQL(['host'=>'localhost','user'=>'root','pass'=>'1234']);
     *     $DB = new SQL('localhost','root','1234','user_account');
     *
     * 1.2 默认不建立连接 第一次提交语句的时候会开始建立连接
     */
    function __construct( $host = null, string $user = null, string $pass = null , string $base = null ){

        parent::__construct();

        $this->host = $host ?? CONFIG['DB_HOST'] ?? null;
        $this->user = $user ?? CONFIG['DB_USER'] ?? null;
        $this->pass = $pass ?? CONFIG['DB_PASS'] ?? null;
        $this->base = $base ?? CONFIG['DB_BASE'] ?? null;

        if( !isset($this->host) ){
            $this->valid = false;
        }
    }

    /**
     * 建立连接
     * connect
     * @param    string|null              $base
     * @return   ASResult
     */
    private function connect( string $base = null ):ASResult{

        if( !isset($this->host) ){
            return $this->error(100,i18n('SQL_NO_CONF'));
        }

        $this->sign('ASDB->connect');
        $this->base = $base ?? $this->base ?? null;

        $conn = $this->base ?
                mysqli_connect($this->host,$this->user,$this->pass,$this->base) :
                mysqli_connect($this->host,$this->user,$this->pass);

        if(!$conn){ return $this->error(555,i18n('SQL_CONN_FAL'));}

        mysqli_set_charset($conn,'utf8mb4');

        $this->connect = $conn;
        $this->connected = true;
        return $this->success(i18n('SQL_CONN_SUC'));
    }

    /**
     * 测试连接是否通畅
     * Touch connection
     * @return   bool
     */
    public function touch(): bool
    {
        if( !$this->connected ){
            $this->connect();
        }
        $connected = $this->connected;
        $this->close();
        return $connected;
    }

    /**
     * 选择数据库
     * selectDB
     * @param    string                   $dbname         [数据库名]
     * @return   string                                   [数据库名]
     */
    public function selectDB( string $dbname ): string
    {
        $this->base = $dbname;
        return $this->base;
    }

    /**
     * 语句提交
     * query
     * @param    string                   $query          [description]
     * @return null|mysqli_result
     */
    public function query( String $query ){
        if( !$this->connected ){
            if(!$this->connect()->isSucceed()){
                return null;
            }
        }
        return mysqli_query($this->connect,$query);
    }

    /**
     * 查看版本号
     * getVersion
     * @return string | null
     */
    public function getVersion(){
        if( !$this->connected ){
            if(!$this->connect()->isSucceed()){
                return null;
            }
        }
        return mysqli_get_server_info( $this->connect );
    }

    /**
     * 关闭连接
     * close
     */
    public function close( ){

        if(!$this->connected){ return; }
        mysqli_close($this->connect);
        $this->connect = false;
        $this->connected = false;
    }

    /**  Basic # 基础操作  **/

    /**
     * 插入数据
     * insert
     * @param DBValues $data
     * @param string $table
     * @return ASResult
     * @example
     *           $DB->insert(['mobile'=>'13300001111'],'user_account');
     *           $DB->insert(['data'=>['mobile'=>'13300001111'],'table'=>'user_account']);
     */
    public function insert( DBValues $data , string $table ): ASResult
    {

        $this->sign('ASDB->insert');

        $t = time();
        $data->set('createtime')->number($t)->set('lasttime')->number($t);

        // Build query
        $query = "INSERT INTO {$table} SET ";

        $query.= $data->export();

        // Query mysql
        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed') :
            $this->take($table)->success(i18n('SYS_SQL_SUC'));
    }

    /**
     * 插入数据缩写
     * Alias Of Insert
     * @param DBValues $data
     * @param string $table
     * @return ASResult
     */
    public function add( DBValues $data, string $table ): ASResult
    {
        return $this->insert($data,$table);
    }

    /**
     * 获取数组key
     * getValidKeys
     * @param    array          $dataList           [数据数组]
     * @return   array                              [key数组]
     */
//    public function getValidKeys( array $dataList ):array {
//
//        $keys = [];
//
//        for ($i=0; $i < count($dataList); $i++) {
//
//            foreach ($dataList[$i] as $key => $value) {
//
//                if( !in_array($key, $keys)){ $keys[] = $key; }
//            }
//        }
//        return $keys;
//    }

    /**
     * update 更新数据
     * @param DBValues $data
     * @param string $table
     * @param DBConditions $conditions
     * @return ASResult
     */
    public function update( DBValues $data, string $table, DBConditions $conditions ): ASResult
    {

        $this->sign('ASDB->update');

        $t = time();
        $data->set('lasttime')->number($t);

        $query  = "UPDATE {$table} SET ";

        $query .= $data->export();
        $query .= $conditions->export();

        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed') :
            $this->take($table)->success(i18n('SYS_SQL_SUC'));
    }

    /**
     * 批量更新
     * updates
     * @param  array   $dataList
     * @param  string  $key
     * @param  string  $table
     * @param  DBConditions $conditions
     * @return ASResult
     */
//    public function updates( array $dataList, string $key, string $table, DBConditions $conditions ): ASResult
//    {
//
//        $this->sign("ASDB->updates");
//
//        $t = time();
//
//        $dataList = Filter::addslashesAll($dataList);
//
//        for ($i=0; $i < count($dataList); $i++) {
//
//            $dataList[$i]['lasttime']   = $dataList[$i]['lasttime'] ?? $t;
//        }
//
//        $query = "UPDATE {$table} SET ";
//        $query.= $this->spliceCaseSet($dataList,$key);
//
//        $query.= $conditions ? $conditions->export(true) : '';
//
//        $DB = $this->query($query);
//
//        return (!$DB) ?
//            $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed') :
//            $this->take($table)->success(i18n('SYS_UPD_SUC'));
//
//    }

    /**
     * 数值型字段自增
     * Increase for numberic field(int,float,double...)
     * @param  string  $field
     * @param  string  $table
     * @param  DBConditions $conditions
     * @param  float   $size
     * @return ASResult
     */
    public function increase( string $field, string $table, DBConditions $conditions, float $size = 1 ): ASResult
    {

        $query = "UPDATE {$table} SET {$field} = {$field}";
        $query.= $size > 0 ? " + {$size} " : " {$size} " ;
        $query.= $conditions->export();
        $DB   = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed') :
            $this->take($field)->success(i18n('SYS_UPD_SUC'));
    }

    /**
     * 数据增长 仅支持int float double 等数字类型
     * decrease
     * @param string $field
     * @param string $table
     * @param DBConditions $conditions
     * @param float $size
     * @return ASResult
     */
    public function decrease( string $field, string $table, DBConditions $conditions, float $size = 1  ): ASResult
    {
        return $this->increase( $field, $table, $conditions, 0 - $size );
    }

    /**
     * 数据减少 仅支持int float double 等数字类型
     * reduce
     * @param  string  $field
     * @param  string  $table
     * @param  DBConditions $conditions
     * @param  float   $size
     * @return ASResult
     */
    public function reduce( string $field, string $table, DBConditions $conditions, float $size = 1  ): ASResult
    {
        return $this->decrease( $field, $table, $conditions, $size );
    }

    /**
     * 从数据库中移除
     * remove row(s) from table
     * @param string $table
     * @param DBConditions $conditions
     * @return ASResult
     */
    public function remove( string $table, DBConditions $conditions ): ASResult
    {

        $this->sign('ASDB->remove');

        if ($this->count($table,$conditions)->getContent()===0) {
            return $this->error(10086,'Target not found.');
        }

        $query = "DELETE FROM {$table} ";
        $query.= $conditions->export();

        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed') :
            $this->take($table)->success(i18n('SQL_RM_SUC'));
    }

    /**
     * 通用行计数
     * count of row
     * @param string $table
     * @param DBConditions|null $conditions 筛选条件
     * @param string|null $distinct 排重字段
     * @return ASResult
     */
    public function count( string $table , DBConditions $conditions = null, string $distinct = null ): ASResult
    {

        $query = " SELECT COUNT( ".($distinct ? "DISTINCT($distinct)" : "id")." ) FROM ";
        $query.= $table;
        $query.= $conditions ? $conditions->export() : '';

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed' );}

        $result = mysqli_fetch_array($DB)[0];

        return $this->take((int)$result)->success(i18n('SQL_COUNT_SUC'));
    }

    /**
     * 获取数据
     * get data
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-08-17T01:30:42+0800
     * @param DBFields $fields 查询字段
     * @param string $table 表名
     * @param DBConditions|null $conditions 条件
     * @return ASResult
     * @version 1.5
     */
    public function get( DBFields $fields, string $table, DBConditions $conditions = null ): ASResult
    {
        $this->sign("ASDB->get");

        $query = " SELECT {$fields->export()} FROM {$table} ";

        $query .= $conditions ? $conditions->export() : '';
        return $this->processQueryResult($query);
    }

    public function processQueryResult( String $query ):ASResult{

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}
        if ( mysqli_num_rows($DB) === 0 ){ return $this->take(false)->error(400,i18n('SYS_GET_NON')); }

        $result = [];
        $re = mysqli_fetch_all($DB,true);

        foreach ( $re as $r ) { $result[] = $r; }

        return $this->take($result)->success(i18n('SYS_GET_SUC'));
    }

    /**
     * 批量获取数据
     * batchGet
     * @param array|string $fields  查询字段
     * @param string $table
     * @param string $key
     * @param array  $keyList
     * @return ASResult
     * @version  1.0
     */
//    public function batchGet( $fields, string $table, string $key, array $keyList ): ASResult
//    {
//
//        $this->sign("ASDB->batchGet");
//
//        $query = "SELECT ";
//
//        $query .= static::spliceFields($fields);
//
//        $query .= " FROM {$table}";
//        $query .= " WHERE {$key} IN ";
//
//        if (count($keyList)>=1) {
//            $i = 0;
//            $query .= "( ";
//            foreach ($keyList as $k => $v) {
//                $query .= ( $i>0 ? ' , ' : '' )." '$v' ";
//                $i++;
//            }
//            $query .= " )";
//        }
//
//        $query .= " ORDER BY FIELD ";
//
//        if (count($keyList)>=1) {
//            $i = 0;
//            $query .= "( {$key} ,";
//            foreach ($keyList as $k => $v) {
//                $query .= ( $i>0 ? ' , ' : '' )." '$v' ";
//                $i++;
//            }
//            $query .= " )";
//        }
//
//        return $this->processQueryResult($query);
//
//    }

    /**
     * 比对数据
     * check
     * @param mixed $value
     * @param string $field
     * @param string $table
     * @param DBConditions $conditions
     * @return ASResult
     */
    public function check( $value , string $field, string $table, DBConditions $conditions ): ASResult
    {
        $this->sign("ASDB->check");

        if( !$conditions->isOrdered() ){
            $conditions->orderBy('createtime');
        }
        $conditions->limitWith(0,1);

        $query  = "SELECT * FROM {$table} ";
        $query .= $conditions->export();

        $DB     = $this->query($query);
        $res    = $DB ? mysqli_fetch_array($DB) : false;

        if ( mysqli_num_rows($DB) === 0 ){ return $this->take(false)->error(400,i18n('SYS_GET_NON')); }

        // 使用了错误的表或字段数据
        if (!$res){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}

        if ($value !== $res[$field]){ return $this->take($value)->error(300,i18n('SYS_CHK_FAL')); }

        // 检查是否设置过期
        if (isset( $res['expire'])) {

            $t = time();
            $e = intval($res['expire']);

            if ($t>$e){ return $this->take($e.'-'.$t)->error(308,i18n('SYS_CHK_EXPIRE')); } //数据过期

        }

        return $this->take($value)->success(i18n('SYS_CHK_SUC'));
    }

    # Fusion Request 混合查询

    /**
     * 联合查询模式计数 joinCount
     * @param JoinPrimaryParams $primaryParams  [primaryParams]
     * @param JoinParams[] $joinParams     [array of joinParams]
     * @return   ASResult
     * @mark     优化条件说明 https://shezw.com/backend/113/MYSQL多表联合查询
     */
//    public function joinCount( JoinPrimaryParams $primaryParams, array $joinParams ): ASResult
//    {
//
//        $table      = $primaryParams->table;
//        $primaryKey = $primaryParams->key;
//        $countKey   = "";
//
//        # 判断优化条件
//        // $ignorePrimary = !$filters && !$conditions; # 主表无关
//        $groups = [];
//        if(isset($primaryParams['group'])){
//            $groups[$table] = $primaryParams['group'];
//        }
//
//        if(!empty($primaryParams->count)){
//            foreach ( $primaryParams->count as $i => $c ){
//                $countKey .= ($countKey ? "," : "") . $table.'.'.$c;
//            }
//        }
//
//        $query  = !empty($primaryParams->count) ? "SELECT COUNT({$primaryParams->table}.{$primaryParams->count[0]}) " : "SELECT COUNT(*)";
//
//        $query .= " FROM ".$table;
//
//        if( !empty($joinParams) ){
//            foreach ($joinParams as $i => $jParams) {
//
//                $query .= isset($jParams->conditions) ? " LEFT JOIN ".$jParams->table : "";
//
//                $key    = $jParams->key ?? $primaryParams->key;   # 默认为主表key
//                $bind   = $jParams->bind;         # 当该组数据并不是与主表key进行绑定时，可以进行自定义绑定
//
//                $query .= isset($jParams->conditions) ? (" ON " . ($bind ?? "{$table}.{$primaryKey}") . " = {$jParams->table}.{$key}") : '';
//
//            }
//        }
//
//        # 主表条件
//        $where = $primaryParams->conditions ? "WHERE".static::spliceCondition($primaryParams->conditions,null,$table) : "";
//
//        # 副表条件
//        foreach ($joinParams as $i => $jParams) {
//
//            $__ = (!$where && $i===0) ? " WHERE " : " AND ";
//            $where .= isset($jParams->conditions) ? $__.static::spliceCondition($jParams->conditions,null,$jParams->table) : "" ;
//        }
//
//        # 默认保留主表
//        $query .= " $where";
//        $query .= $countKey ? " GROUP BY {$countKey} " :'';
//        $query  = $countKey ? " SELECT COUNT(*) FROM ( $query ) AS TMP" : $query ;
//
//        $DB = $this->query($query);
//
//        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}
//
//        $result = mysqli_fetch_array($DB)[0];
//
//        return $this->take((int)$result)->success(0,i18n('SYS_ANA_SUC'));
//    }


    /**
     * 通过JOIN模式计数 (新)
     * @param DBJoinParams $joinParams
     * @return ASResult
     */
    public function countByJoin( DBJoinParams $joinParams ): ASResult
    {
        $query = " SELECT COUNT(*) FROM {$joinParams->exportCountJoinTables()} ";

        $query.= $joinParams->exportCondition();

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}

        $result = mysqli_fetch_array($DB)[0];

        return $this->take((int)($result))->success(0,i18n('SYS_ANA_SUC'));
    }

    /**
     * 通过JOIN模式联合查询 （新）
     * @param DBJoinParams $joinParams
     * @return ASResult
     */
    public function getByJoin( DBJoinParams $joinParams ): ASResult{

        $this->sign("ASDB->getByJoin");

        $query = " SELECT " . $joinParams->export();

        $DB = $this->query($query);
//var_dump($query);
        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}
        if ( mysqli_num_rows($DB) === 0 ){ return $this->take(false)->error(400,i18n('SYS_GET_NON')); }

        $re = mysqli_fetch_all($DB,true);

        for ( $i=0; $i<count($re); $i++ ){
            $joinParams->convertSubData($re[$i]);
        }

        return $this->take($re)->success(i18n('SYS_GET_SUC'));

    }


    /**
     * https://shezw.com/backend/113/MYSQL多表联合查询
     *
     * 联合模式查询
     * 支持多张表联合查询,取消JOIN方向,INNER使用句末WHERE方式取代.
     * 其中每一行可以定义 key|field(索引字段)
     * Get data by JOIN tables
     * @param  JoinPrimaryParams  $primaryParams    主表参数 $table 必填, $key|$field 必填, $fields 可选, filters,conditions,count 可选,
     * @param  int                $page
     * @param  int                $size
     * @param  string             $sort
     * @param  JoinParams[] $joinParams       副表参数(多个副表数组)] $table 必填, $key|$field 可选, $fields 可选, filters (表内过滤)可选,conditions (全局筛选)可选
     * @return ASResult
     * @mark   SELECT * FROM user_account
     *         LEFT JOIN user_info ON user_account.userid = user_info.userid
     *         LEFT JOIN user_pocket ON user_account.userid = user_pocket.userid AND user_pocket.point > 5000
     *         LEFT JOIN user_group ON user_account.groupid = user_group.groupid AND user_group.level > 100
     *         WHERE user_info.wechatid IS NOT null
     *
     * @mark   SELECT *,COUNT(relation_combine.relationid) as count_unit FROM relation_combine
     *         LEFT JOIN item_scene ON relation_combine.itemid = item_scene.sceneid
     *         GROUP BY relation_combine.itemid,relation_combine.itemtype AND relation_combine.itemtype='scene'
     */
//    public function joinGet( JoinPrimaryParams $primaryParams, array $joinParams, int $page = 1 , int $size = 25 , string $sort = null ):ASResult{
//
//        if( count($joinParams) == 0 ){
//            return $this->error(600,'joinParams is empty');
//        }
//
//        $this->sign("ASDB->joinGet");
//        $filtered = 0;
//
//        $alias = [];
//
//        $query = "SELECT ";
//        $query.= static::spliceFields($primaryParams->fields,$primaryParams->table);
//
//        $table = $primaryParams->table;
//        $primaryKey = $primaryParams->key;
//
//        $groups = []; # GROUP BY FILTER
//        if( !empty($primaryParams->group) ){
//            $groups[$primaryParams->table] = $primaryParams->group;
//        }
//
//        $counts = []; # COUNT( FIELD )
//        if(!empty($primaryParams->count)){
//            $counts[$primaryParams->table] = $primaryParams->count;
//        }
//
//        $sums = []; # SUM( FIELD ) AS SUM_FIELD
//        if(!empty($primaryParams->sum)){
//            $sums[$primaryParams->table] = $primaryParams->sum;
//        }
//
//        foreach ($joinParams as $i => $jParams ) {
//
//            if(isset($jParams->alias)){ $alias[] = $jParams->alias; }
//            if(!empty($jParams->sum)){
//                $sums[$jParams->table] = [];
//                foreach ( $jParams->sum as $k => $s ){
//                    $sums[$jParams->table][] = [$s,$jParams->sumAs[$k]];
//                }
//            }
//            if(!empty($jParams->count)){
//                $counts[$jParams->table] = [];
//                foreach ( $jParams->count as $k => $s ){
//                    $counts[$jParams->table][] = [$s,$jParams->countAs[$k]];
//                }
//            }
//            if(!empty($jParams->groupConditions)){
//                $groups[$jParams->table] = $jParams->groupConditions;
//            }
//
//            $selections = static::spliceFields($jParams->fields,$jParams->table,null,$jParams->alias);
//            $query .= ', '.$selections;
//        }
//
//        if( !empty($counts) ){
//
//            $countSelect = "";
//
//            foreach ($counts as $_table => $key) {
//
//                foreach ($key as $k => $g) {
//
//                    $countSelect .= ", COUNT({$_table}.{$g[0]}) AS {$g[1]}";
//                }
//            }
//            $query.= $countSelect;
//        }
//
//
//        if( !empty($sums) ){
//
//            $sumSelect = "";
//
//            foreach ($sums as $_table => $key) {
//
//                foreach ($key as $k => $g) {
//
//                    $sumSelect .= ", SUM({$_table}.{$g[0]}) AS {$g[1]}";
//                }
//            }
//            $query.= $sumSelect;
//        }
//
//        $query.= " FROM ".$table;
//
//        foreach ($joinParams as $i => $jParams) {
//
//            $query .= " LEFT JOIN ".$jParams->table;
//
//            $key    = $jParams->key  ?? $primaryKey; # 默认为主表key
//            $bind   = $jParams->bind ?? null;       # 当该组数据并不是与主表key进行绑定时，可以进行自定义绑定
//
//            $query .= " ON " . ($bind ?? "{$table}.{$primaryKey}") . " = {$jParams->table}.{$key}";
//
//            $query .= $jParams->filters ? " AND ".static::spliceCondition($jParams->filters,null,$jParams->table) : '';
//
//            if( !empty($jParams->group) ){
//
//                $groupby = "";
//
//                foreach ($jParams->group as $k => $g) {
//
//                    $groupby .= $groupby ? ',' : '';
//                    $groupby .= " {$jParams->table}.{$g} ";
//                }
//                $query .= " GROUP BY $groupby ";
//                $filtered ++;
//            }
//        }
//
//        # 主表条件
//        $where  = $primaryParams->conditions ?
//            ( $filtered ? " AND " : " WHERE " ).static::spliceCondition($primaryParams->conditions,null,$primaryParams->table) : "";
//
//        # 副表条件
//        foreach ($joinParams as $i => $jParams) {
//
//            $__ = (!$where && $i===0) ? " WHERE " : " AND ";
//            $where .= isset($jParams->conditions) ? $__.static::spliceCondition($jParams->conditions,null,$jParams->table) : "" ;
//        }
//
//        $query .= $where;
//
//        if( !empty($groups) ){
//
//            $groupby = "";
//
//            foreach ($groups as $g_table => $group) {
//
//                foreach ($group as $k => $g) {
//
//                    $groupby .= $groupby ? ',' : '';
//                    $groupby .= " $g_table.$g ";
//                }
//            }
//            $query .= " GROUP BY $groupby ";
//        }
//
//        $start  = ($page-1)*$size;
//
//        $size   = $size>1000 ? 1000 : $size;
//        $start  = $start<0  ? 0   : $start;  // 开始不能小于0
//
//        $query .= $sort  ?  " ORDER BY $sort" : '';
//        $query .= ' LIMIT '.$start.','.$size;
//
//        $DB = $this->query($query);
//
//        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}
//        if ( mysqli_num_rows($DB) === 0 ){ return $this->take(false)->error(400,i18n('SYS_GET_NON')); }
//
//        $result = [];
//        $re = mysqli_fetch_all($DB,true);
//
//        foreach ( $re as $r ) {
//
//            if(!empty($alias)){
//
//                foreach ($alias as $i => $a) {
//
//                    $aliasData = [];
//
//                    foreach ($r as $key => $value) {
//
//                        if(strstr($key, $a."$")){
//                            unset($r[$key]);
//                            $aliasData[str_replace($a."$", "", $key)] = $value;
//                        }
//                    }
//                    $r[$a] = $aliasData;
//                }
//            }
//
//            $result[] = $r;
//        }
//
//        return $this->take($result)->success(i18n('SYS_GET_SUC'));
//
//    }


    /* Database io 表相关操作 */

    /**
     * 查询全部表
     * @param string|null $base
     * @return ASResult
     */
    public function showTables( string $base = null ): ASResult
    {

        $this->sign('ASDB->showTables');

        $base = $base ?? $this->base ?? getConfig('base');

        $query = "SELECT * FROM information_schema.tables WHERE table_schema='{$base}'";

        return $this->processQueryResult($query);

    }

    /**
     * 查询 表/列 结构
     * @param $table
     * @return ASResult
     */
    public function showColumns( $table ): ASResult
    {

        $this->sign('ASDB->showColumns');

        $query = "SELECT COLUMNS.*, STATISTICS.INDEX_NAME, STATISTICS.INDEX_TYPE, STATISTICS.CARDINALITY, STATISTICS.COLLATION "
            . "FROM INFORMATION_SCHEMA.COLUMNS LEFT JOIN INFORMATION_SCHEMA.STATISTICS "
            . "ON COLUMNS.COLUMN_NAME=STATISTICS.COLUMN_NAME AND COLUMNS.TABLE_NAME = STATISTICS.TABLE_NAME AND  STATISTICS.TABLE_SCHEMA = '{$this->base}' AND STATISTICS.TABLE_NAME = '$table' "
            . "WHERE COLUMNS.TABLE_SCHEMA = '{$this->base}' AND COLUMNS.TABLE_NAME = '{$table}' ";

        return $this->processQueryResult($query);

    }

    /**
     * 查询是否存在表
     * @param $table
     * @return ASResult|bool
     */
    public function exist( $table ){

        $query = "show tables like '{$table}'";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}

        return mysqli_num_rows($DB)>0;
    }

    /**
     * 创建表
     * @param DBTableStruct $tableStruct
     * @param bool $autoRemoveExists
     * @return ASResult
     */
    public function newTable( DBTableStruct $tableStruct, bool $autoRemoveExists = true ): ASResult
    {
        $this->sign("ASDB->newTable");

        if( $autoRemoveExists ){
            $this->query("DROP TABLE IF EXISTS `{$tableStruct->name}`;");
        }

        $query = "CREATE TABLE IF NOT EXISTS {$tableStruct->export()};";

        $DB     = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}

        return $this->take($tableStruct->name)->success(i18n('SYS_SQL_SUC'));

    }

    /**
     * 更新表结构
     * @param string $table
     * @param string $query
     * @return ASResult
     */
    private function updateTable( string $table, string $query ): ASResult
    {
        $DB     = $this->query("ALTER TABLE `{$table}` ".$query);

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}

        return $this->take($query)->success(i18n('SYS_SQL_SUC'));
    }

    /**
     * 追加字段 (表结构)
     * @param string $table
     * @param DBFieldStruct $fieldStruct
     * @param string|null $after
     * @return ASResult
     */
    public function addField( string $table, DBFieldStruct $fieldStruct, string $after = NULL ): ASResult
    {
        $query = " ADD ";
        $query .= $fieldStruct->export();
        $query .= $after ? " AFTER {$after} " : "";
        return $this->updateTable( $table, $query );
    }

    /**
     * 追加多个字段 (表结构)
     * @param string $table
     * @param array $fieldStructs
     * @param string|null $after
     * @return ASResult
     */
    public function addFields( string $table, array $fieldStructs, string $after = NULL ): ASResult{

        $query = "";
        for ( $i=0; $i<count($fieldStructs); $i++ ){
            $query .= $i>0 ? ", " : "";
            $query .= " ADD {$fieldStructs[$i]->export()} ";

            if( $after ){
                $query .= $i==0 ? " AFTER {$after} " : " AFTER {$fieldStructs[$i-1]->name} ";
            }
        }
        return $this->updateTable( $table, $query);
    }

    /**
     * 移除字段 (表结构)
     * @param string $table
     * @param string $field
     * @return ASResult
     */
    public function removeField( string $table, string $field ): ASResult
    {
        return $this->updateTable($table, " DROP {$field}" );
    }

    /**
     * 移除多个字段 (表结构)
     * @param string $table
     * @param array[string] $fields
     * @return ASResult
     */
    public function removeFields( string $table, array $fields ): ASResult
    {
        $query = "";
        for ( $i=0; $i<count($fields); $i++ ){
            $query .= $i>0 ? ", " : "";
            $query .= " DROP {$fields[$i]} ";
        }
        return $this->updateTable( $table, $query);
    }

    /**
     * 更新字段(表结构)
     * @param string $table
     * @param DBFieldStruct $fieldStruct
     * @return ASResult
     */
    public function updateField( string $table, DBFieldStruct $fieldStruct ): ASResult
    {
        return $this->updateTable( $table, " CHANGE {$fieldStruct->export()}" );
    }

    /**
     * 更新多个字段(表结构)
     * @param string $table
     * @param array $fieldStructs
     * @return ASResult
     */
    public function updateFields( string $table, array $fieldStructs ): ASResult
    {
        $query = "";
        for ( $i=0; $i<count($fieldStructs); $i++ ){
            $query .= $i>0 ? ", " : "";
            $query .= " CHANGE {$fieldStructs[$i]->export()} ";
        }
        return $this->updateTable( $table, $query);
    }


    /**
     * 建立索引
     * @param string $table
     * @param DBFieldStruct $field
     * @return ASResult
     */
    public function index( string $table, DBFieldStruct $field ): ASResult
    {
        $this->sign("ASDB->index");

        $query = "ALTER TABLE {$table} ADD {$field->exportIndex()}";

        // 连接数据库
        $DB = $this->query($query);

        return $this->autoTake( !!$DB, $table, $query )->autoFeedback(!!$DB, i18n('SYS_DFT_SUC'),550,mysqli_error($this->connect) ?? 'SQL Connect failed' );
    }

    /**
     * 移除字段索引
     * @param string $table
     * @param string $indexName
     * @return ASResult
     */
    public function removeIndex( string $table, string $indexName ){

        $this->sign("ASDB->removeIndex");

        $query = "ALTER TABLE {$table} DROP INDEX {$indexName}";

        // 连接数据库
        $DB = $this->query($query);

        return $this->autoTake( !!$DB, $table, $query )->autoFeedback(!!$DB, i18n('SYS_DFT_SUC'),550,mysqli_error($this->connect) ?? 'SQL Connect failed' );
    }

    /**
     * 清空数据表
     * @param string $table
     * @return ASResult
     */
    public function truncate( string $table ): ASResult
    {

        $this->sign("ASDB->truncate");

        $query = "TRUNCATE $table";

        $DB = $this->query($query);
        return $this->autoTake( !!$DB, $table, $query )->autoFeedback(!!$DB, i18n('SYS_DFT_SUC'),550,mysqli_error($this->connect) ?? 'SQL Connect failed' );
    }

    /**
     * 移除数据表
     * @param string $table
     * @return ASResult
     */
    public function dropTable( string $table ): ASResult
    {

        $this->sign("ASDB->dropTable");

        $query = "DROP TABLE IF EXISTS {$table} ";

        $DB = $this->query($query);
        return $this->autoTake( !!$DB, $table, $query )->autoFeedback(!!$DB, i18n('SYS_DFT_SUC'),550,mysqli_error($this->connect) ?? 'SQL Connect failed' );
    }

    // 库相关操作
    // !!! 需要数据库Root权限 !!!
    public function base( string $operation = 'CREATE', string $baseName = NULL ): ASResult
    {
        $this->sign("ASDB->base");

        $base    = $baseName ?? getConfig('DB_NAME') ;

        $query = "{$operation} DATABASE {$base} ";

        $DB = $this->query($query);
        return $this->autoTake( !!$DB, $baseName, $query )->autoFeedback(!!$DB, i18n('SYS_DFT_SUC'),550,mysqli_error($this->connect) ?? 'SQL Connect failed' );
    }

    /**
     * 新数据库
     * newBase
     * @param  string  $base
     * @return ASResult
     */
    public function newBase( string $base ): ASResult
    {
        return $this->base('CREATE',$base);
    }

    /**
     * 移除数据库
     * dropBase
     * @param  string  $base
     * @return ASResult
     */
    public function dropBase( string $base ): ASResult
    {
        return $this->base('DROP', $base);
    }

    /**
     * 查询是否存在数据库
     * hasBase
     * @param string $baseName
     * @return bool
     */
    public function hasBase( string $baseName ): bool
    {
        $tmpBase = $this->base;
        $this->base = 'information_schema';
        $count = $this->count('SCHEMATA', DBConditions::init()->where('SCHEMA_NAME')->equal($baseName))->getContent();
        $this->base = $tmpBase;
        return $count>0;
    }

    /**
     * 查询是否存在数据库下对应表
     * hasTableInBase
     * @param string $table
     * @param string $baseName
     * @return bool
     */
    public function hasTableInBase( string $table, string $baseName ): bool
    {

        if (!$this->hasBase($baseName)) {
            return false;
        }
        $tmpBase = $this->base;
        $count = $this->count('TABLES',DBConditions::init()->where('TABLE_SCHEMA')->equal($baseName)->and('TABLE_NAME')->equal($table))->getContent();
        $this->base = $tmpBase;
        return $count>0;
    }


    /**
     * 根据结构化数据自动填充至数据库
     * autoInsertData
     * @param  array  $dataWithStruct
     * @param  string $base
     * @return ASResult
     */
//    public function autoInsertData( array $dataWithStruct, string $base = null ): ASResult
//    {
//
//        if( isset($base) ){ $this->selectDB($base); }
//        $addResult = [];
//
//        foreach ( $dataWithStruct as $class => $dataList ){
//
//            foreach ( $dataList as $i => $data ){
//
//                $addResult[] = $class::common()->add( $data );
//            }
//        }
//        return $this->take($addResult)->feedback();
//    }


    /**
     * 分词搜索计数 (自然语言)  需要fulltext索引
     * natureCount Require fulltext index
     * @param          $target
     * @param  string  $value
     * @param  string  $table
     * @param  null    $conditions
     * @return ASResult
     */
//    public function natureCount( $target, string $value, string $table, $conditions = null): ASResult
//    {
//
//        return $this->natureSearch($target,$value,$table,$conditions,null,true);
//    }

    /**
     * 分词搜索 (自然语言)  需要fulltext索引
     * natureSearch Require fulltext index
     * @param  array   $target      具有分词索引的目标字段. The target fields has fulltext index
     * @param  string  $value
     * @param  string  $table
     * @param  string|array|null    $conditions
     * @param  string|array|null    $fields
     * @param  bool    $isCounting
     * @return ASResult
     * @mark   SELECT id,namecn FROM customer WHERE MATCH (namecn,nameen) AGAINST ('测试' IN NATURAL LANGUAGE MODE)
     */
//    public function natureSearch( $target, string $value, string $table, $conditions = null, $fields = null, bool $isCounting = false  ): ASResult
//    {
//
//        $this->sign($isCounting ? "ASDB->natureCount" : "ASDB->natureSearch");
//
//        if (count($target)>5){ return $this->take($target)->error(8000,'Too many arguments! Limited with 5.');}
//
//        $query = ' (';
//        for ($i=0;$i<count($target);$i++) {
//            $name = $target[$i];
//            $query.= $i>0 ? " OR " : '' ;
//            $query.=" MATCH ( {$name} ) AGAINST ('{$value}' IN NATURAL LANGUAGE MODE) ";
//        }
//        $query.= ') ';
//
//        $conditions = ($conditions ? " $conditions AND " : "").$query;
//
//        return $isCounting ? $this->count($table,$conditions) : $this->get($fields,$table,$conditions);
//
//    }


    /**
     * 分词搜索 计数(布尔)
     * booleanCount
     * @param  array   $target
     * @param  array   $valueList
     * @param  string  $table
     * @return ASResult
     * @mark   valueList ['+value','-value','~value','>value','<value']
     */
//    public function booleanCount(array $target, array $valueList, string $table ): ASResult
//    {
//
//        return $this->booleanSearch($target,$valueList,$table,null,true);
//    }


    /**
     * 分词搜索 (布尔)
     * booleanSearch
     * @param  array   $target
     * @param  array   $valueList
     * @param  string  $table
     * @param  null    $fields
     * @param  bool    $isCounting
     * @return ASResult
     * @mark   valueList ['+value','-value','~value','>value','<value']
     */
//    public function booleanSearch(array $target, array $valueList, string $table ,$fields = null, bool $isCounting = false ): ASResult
//    {
//
//        $this->sign($isCounting ? "ASDB->booleanCount" : "ASDB->booleanSearch" );
//
//        if (count($target)>3) {
//            return $this->take($target)->error(8000,'Too many arguments! Limited with 3.');
//        }
//
//        if (count($valueList)>5) {
//            return $this->take($valueList)->error(8000,'Too many arguments! Limited with 5.');
//        }
//
//        $conditions='MATCH (';
//
//        for ($i=0;$i<count($target);$i++) {
//            $name = $target[$i];
//            $conditions.= $i>0 ? ", " : '' ;
//            $conditions.= "$name";
//        }
//
//        $conditions.=") AGAINST ( '";
//
//        for ($j=0;$j<count($valueList);$j++) {
//            $conditions.= ' '.$valueList[$j];
//        }
//
//        $conditions.="' IN BOOLEAN MODE)";
//
//        return $isCounting ? $this->count($table,$conditions) : $this->get($fields,$table,$conditions);
//
//    }


## Static functions
## 静态方法

    /**
     * 拼接字段 spliceFields
     * @param  array|string|null  $fields    待拼接字段 fields
     * @param  string|null        $table     表名 table name
     * @param  string|null        $distinct  排重字段 Distinct field
     * @param  string|null        $alias
     * @return string                        语句 Query String
     */
    public static function spliceFields( $fields = null, string $table = null, string $distinct = null, string $alias = null ){

        $query = $distinct ? " DISTINCT( $distinct ) as $distinct, " :" ";
        $fields = $fields ?? "*";

        if (gettype($fields)=="array") {

            if (count($fields)>1){
                //多条数据

                for ($i=0; $i <count($fields) ; $i++) {
                    if ($i>0) {
                        $query .= ", ";
                    }
                    $query .= $table ? "$table." :"";
                    $query .= $fields[$i];

                    $query .= $alias ? " AS '$alias\$".$fields[$i]."'" : '';
                }

            }else if(count($fields)===1){
                //单条数据
                $query .= $table ? "$table." :"";
                $query .= $fields[0];

                $query .= $alias ? " AS '$alias\$".$fields[0]."'" : '';

            }else{
                return false;
            }
        }else if(gettype($fields)=="string"){ //单条数据
            $query = ( $table ? " $table." : " " ).$fields;
            $query = $fields === 'COUNT' ? ($table? " COUNT($table.id) as count_$table " : 'COUNT(id)') : $query;
        }
        return $query;
    }

    /**
     * 拼接条件语句
     * Splice K-v condition to SQL QUERY String condition
     * @param    array|string|null        $params         数据 data
     * @param    array|null               $filter         过滤 Filter
     * @param    string|null              $table          表名 Table name
     * @return   string                                   语句 Query String
     *
     * table 用于join模式 处理JOIN关系,会在字段前添加表前缀
     */
//    public static function spliceCondition( $params = null , array $filter = null, string $table = null ){
//
//        if(!$params){ return ""; }
//        if (gettype($params) == 'string' ){ return $params; }
//
//        $condition = '';
//        $count     = 0;
//
//        $params = Filter::addslashesAll($params);
//        if ($filter) { $params = Filter::purify($params,$filter); }
//
//        $params = Filter::removeInvalid($params);
//
//        foreach ($params as $key => $value) {
//
//            if (isset($value)) {
//
//                $condition .= $count>0 ? ' AND ' : ' ';
//
//                if($key==="keyword"||$key==="KEYWORD"){
//
//                    if(isset($value['value'])){
//
//                        $val = $value['value'];
//
//                        $condition .= ' (';
//                        for ($i=0;$i<count($value['target']);$i++) {
//                            $name = $value['target'][$i];
//                            $condition .= $i>0 ? " OR " : '' ;
//                            $condition .=' MATCH (';
//                            $condition .= $table ? " $table." : ' ';
//                            $condition .= "$name";
//                            $condition .= ") AGAINST ('$val' IN NATURAL LANGUAGE MODE) ";
//                        }
//                        $condition .= ') ';
//
//                    }else{
//
//                        $condition .= ' MATCH( ';
//                        $condition .= $table ? " $table." : ' ';
//                        $condition .= 'title';
//                        $condition .= ") AGAINST ('$value' IN NATURAL LANGUAGE MODE) ";
//                    }
//
//                }else if(gettype($value)=='array' && count($value) == count($value,1)){
//
//                    $condition .= " (";
//                    for ($i=0; $i <count($value) ; $i++) {
//
//                        $condition .= ($i!==0?' OR ':'');
//                        $condition .= $table ? " $table." : ' ';
//                        $condition .= "$key";
//
//                        if(static::hasSymbol($value[$i])){
//                            $symbol_   = static::getSymbol($value[$i],true);
//                            $symbol    = static::getSymbol($value[$i],false);
//                            $value[$i] = str_replace($symbol_, '', $value[$i]);
//                            $condition.= $symbol;
//                        }else{
//                            $condition.= "=";
//                        }
//
//                        $condition .= static::generateQueryValue($value[$i]);
//                    }
//                    $condition .= ") ";
//
//                }else if(static::hasSymbol($value)){
//
//                    $symbol_ = static::getSymbol($value,true);
//                    $symbol  = static::getSymbol($value,false);
//                    $value   = str_replace($symbol_, '', $value);
//
//                    if( $symbol_ != '[[FIND]]' && $symbol_ != '[[QUERY]]' ){
//
//                        $condition .= $table ? " $table." : ' ';
//                    }
//
//                    if($symbol_=='[[IN]]'){
//                        $values = explode(',', $value);
//                        $V = '';
//                        for ($i=0; $i < count($values); $i++) {
//
//                            $V .= $i==0 ? '' : ',';
//                            $V .= Encrypt::isNumber($values[$i]) ? $values[$i] : "'{$values[$i]}'";
//
//                        }
//                        $condition .= "{$key} {$symbol} ({$V}) " ;
//
//                    }else if( $symbol_ == '[[BETWEEN]]' ){
//
//                        $value = str_replace(",", " AND ", $value);
//                        $condition .= $key.$symbol.$value;
//
//                    }else if( $symbol_ == '[[FIND]]' ){
//
//                        $condition .= " {$symbol}( '{$value}' ,";
//                        $condition .= $table ? " $table." : ' ';
//                        $condition .= " {$key} )";
//
//                    }else if( $symbol_ == '[[QUERY]]' ){
//
//                        $condition .= " {$key} {$value} ";
//
//                    }else if( strstr($symbol_,'>') || strstr($symbol_, '<') ){
//
//                        $condition .=  "{$key} {$symbol} {$value} " ;
//
//                    }else{
//
//                        $condition .= (Encrypt::isNumber($value) && $value!==0 ) || $value==='null' ? "{$key} {$symbol} {$value} " : "{$key} {$symbol} '{$value}' " ;
//                    }
//
//                }else if( $value === 'IS_NULL' ){
//
//                    $condition .= $table ? " {$table}." : ' ';
//                    $condition .= "{$key} IS NULL " ;
//
//                }else{
//
//                    $condition .= $table ? " $table." : ' ';
//                    $condition .= !is_string($value) && (Encrypt::isNumber($value) && $value!==0 ) ? "{$key}={$value} " : "{$key}='{$value}' " ;
//
//                }
//                $count++;
//            }
//        }
//
//        return $condition;
//    }

    /**
     * 生成赋值语句
     * generateQueryValue
     * @param    array|string|number      $input          输入
     * @return   string                                   赋值语句 Set value query string
     */
    public static function generateQueryValue( $input ){

        $output = $input;

        if(gettype($input)=='array'){

            $output = "'".json_encode($input,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK)."'";

        }else if(is_numeric($input)){

            $output = " $input ";

        }else if($input==='SET_NULL'){

            $output = " NULL ";

        }else if($input==='null'){
            $output = " NULL ";
        }else if(gettype($input)=='string'){

            $output = " '$input' ";

        }
        return $output;
    }

    /**
     * 生成定位插入值
     * generateLocationValue
     * @param  String  $value
     * @return string
     */
    public static function generateLocationValue( String $value ): string
    {

        $location = explode(',', $value);
        $lng = $location[0];
        $lat = $location[1];
        return " GeomFromWKB(POINT({$value})), lng={$lng}, lat={$lat}";
    }


    /**
     * 拼接CASE SET 用于批量更新 spliceCaseSet
     * @param array $kvArrayList [description]
     * @param string $key [description]
     * @return string [type]                                   [description]
     */
//    public function spliceCaseSet( array $kvArrayList , string $key ): string
//    {
//
//        $query = "";
//        $keys  = $this->getValidKeys( $kvArrayList );
//
//        $c = 0;
//
//        for ($i=0; $i < count($keys); $i++) {
//
//            if( $keys[$i] != $key ){
//
//                $query .= $c > 0 ? " , " : "";
//                $query .= $keys[$i]. " = CASE $key ";
//
//                for ($j=0; $j < count($kvArrayList) ; $j++) {
//
//                    if(isset($kvArrayList[$j][$keys[$i]]) && isset($kvArrayList[$j][$key]) ){
//
//                        $k = $kvArrayList[$j][$key];
//                        $v = $kvArrayList[$j][$keys[$i]];
//
//                        $query .= " WHEN ".(static::generateQueryValue($k))." THEN ";
//
//                        if($keys[$i]=='location'){
//                            $query .= static::generateLocationValue($v);
//                        }else if($v==='SET_NULL'){
//                            $query .= " NULL " ;
//                        }else{
//                            $query .= static::generateQueryValue($v);
//                        }
//                    }
//                }
//                $query .= " END ";
//
//                $c ++;
//            }
//        }
//
//        // 因为SQL 使用CASE WHEN THAN语句的时候 会对当前整个表进行更新  所以需要注意先强制做结果筛选 否则可能会污染数据库 或更新失败
//        // 默认使用 WHERE KEY IN (1,2,3)的方式约束
//        $query .= " WHERE $key IN (";
//
//        for ($i=0; $i < count($kvArrayList) ; $i++) {
//
//            if(isset($kvArrayList[$i][$keys[$i]]) && isset($kvArrayList[$i][$key]) ){
//
//                $k = $kvArrayList[$i][$key];
//
//                $query .= $i>0 ? "," : "";
//                $query .= static::generateQueryValue($k);
//            }
//        }
//
//        $query .= ") ";
//
//        return $query;
//
//    }

    /**
     * 获取特殊操作符号 getSymbol
     * @param    string                     $input          [输入]
     * @param    bool                       $returnKey      [是否输出源符号]
     * @return   string                                   [操作符号]
     */
//    private static function getSymbol( string $input, bool $returnKey = false ): string
//    {
//
//        $symbol = '';
//        foreach (static::symbols as $key => $value) {
//            $symbol = strstr($input, $key) ? ($returnKey?$key:$value) : $symbol ;
//        }
//
//        return $symbol;
//    }

    /**
     * 检测是否含有操作符 hasSymbol
     * @param    string                   $input          [输入]
     * @return   bool                                  [是否]
     */
//    private static function hasSymbol( string $input ):bool{
//        $c = 0;
//        foreach (static::symbols as $key => $value) {
//            $c += strstr($input, $key) ? 1 : 0;
//        }
//        return $c>0;
//    }

}
