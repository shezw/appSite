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
    private $connected = false;

    /**
     * 预定义查询标记
     * Predefined query symbols
     */
    const symbols = [
        '[[>]]'   => ' > ' ,
        '[[<]]'   => ' < ' ,
        '[[>=]]'  => ' >= ' ,
        '[[<=]]'  => ' <= ' ,
        '[[!=]]'  => ' != ' ,
        '[[IN]]'  => ' IN ' ,
        '[[IS]]'  => ' IS ' ,
        '[[NOT]]' => ' IS NOT ' ,
        '[[BETWEEN]]' => ' BETWEEN ' ,
        '[[FIND]]'=> ' LOCATE ',
        '[[QUERY]]'=>' ',
    ];

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

        $this->host = $host ?? CONFIG['DB_HOST'];
        $this->user = $user ?? CONFIG['DB_USER'];
        $this->pass = $pass ?? CONFIG['DB_PASS'];
        $this->base = $base ?? CONFIG['DB_BASE'];

    }

    /**
     * 建立连接
     * connect
     * @param    string|null              $base
     * @return   ASResult
     */
    private function connect( string $base = null ):ASResult{

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
     * @return null|\mysqli_result
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
     * @param  array        $data
     * @param  string       $table
     * @return ASResult
     * @example
     *           $DB->insert(['mobile'=>'13300001111'],'user_account');
     *           $DB->insert(['data'=>['mobile'=>'13300001111'],'table'=>'user_account']);
     */
    public function insert( array $data , string $table ): ASResult
    {

        $this->sign('ASDB->insert');

        $t = time();
        $data['createtime'] = $data['createtime'] ?? $t;
        $data['lasttime']   = $data['lasttime'] ?? $t;

        $data = Filter::addslashesAll($data);

        // Build query
        $query = "INSERT INTO {$table} SET ";

        $i = 0;
        foreach ($data as $key => $value) {

            $query .= $i>0 ? ' , ':'';
            $query .= "$key = ";

            if($key=='location'){
                $query .= static::generateLocationValue($value);
            }else{
                $query .= static::generateQueryValue($value);
            }

            $i ++;
        }

        // Query mysql
        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,i18n('SYS_SQL_ERROR')) :
            $this->take($table)->success(i18n('SYS_SQL_SUC'));
    }

    /**
     * 插入数据缩写
     * Alias Of Insert
     * @param  array   $data
     * @param  string  $table
     * @return ASResult
     */
    public function add( array $data, string $table ): ASResult
    {
        return $this->insert($data,$table);
    }

    /**
     * 批量添加
     * inserts
     * @param  array   $dataList
     * @param  string  $table
     * @return ASResult
     * @mark
     * 批量添加注意: List中可以存在数据字段不一致的情况,其他元素中含有字段,但另外一些元素中又没有该字段的值时将会自动默认填充为 '' SQL空值, 如果为空字段是不能为空类型，如数字，将会插入失败。
     * 如果希望保持空白值为null 可以使用 字符值 "SET_NULL", 将自动转化为 null进行填充
     * @example
     * $DB->inserts([["mobile"=>"13300001111"],["email"=>"a@b.com"],["email"=>"c@d.cn"]],"user_account");
     * $DB->inserts(["list"=>[["mobile"=>"13300001111"],["email"=>"a@b.com"],["email"=>"c@d.cn"]],"table"=>"user_account"])
     */
    public function inserts( array $dataList, string $table ): ASResult
    {

        $this->sign('ASDB->inserts');

        if( gettype($dataList) != 'array' ){ return $this->error(802,'List must be array'); }

        $t = time();

        $dataList = Filter::addslashesAll($dataList);

        for ($i=0; $i < count($dataList); $i++) {

            $dataList[$i]['createtime'] = $dataList[$i]['createtime'] ?? $t;
            $dataList[$i]['lasttime']   = $dataList[$i]['lasttime'] ?? $t;

        }

        $keys = $this->getValidKeys($dataList);

        // Build query
        $query = 'INSERT INTO ';
        $query.= $table.' (';

        for ($j=0; $j < count($keys); $j++) {

            $query .= $j>0 ? ' , ':'';
            $query .= $keys[$j];
        }

        $query .= ') VALUES ';

        for ($k=0; $k < count($dataList); $k++) {

            $query .= $k>0 ? ',' : '';
            $query .= '( ';

            for ($l=0; $l < count($keys); $l++) {

                $query .= $l>0 ? ' , ':'';

                if(!isset($dataList[$k][$keys[$l]])){
                    $query .= "''";
                }else if($keys[$l]=='location'){
                    $query .= static::generateLocationValue($dataList[$k][$keys[$l]]);
                }else{
                    $query .= static::generateQueryValue($dataList[$k][$keys[$l]]);
                }
            }

            $query .= ') ';
        }

        // Query mysql
        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,i18n('SYS_SQL_ERROR')) :
            $this->take($table)->success(i18n('SYS_SQL_SUC'));
    }

    /**
     * 批量插入数据缩写
     * Alias Of Inserts
     * @param  array        $dataList
     * @param  string|null  $table
     * @return ASResult
     */
    public function adds( array $dataList, string $table = null ): ASResult
    {
        return $this->inserts($dataList,$table);
    }

    /**
     * 获取数组key
     * getValidKeys
     * @param    array          $dataList           [数据数组]
     * @return   array                              [key数组]
     */
    public function getValidKeys( array $dataList ):array {

        $keys = [];

        for ($i=0; $i < count($dataList); $i++) {

            foreach ($dataList[$i] as $key => $value) {

                if( !in_array($key, $keys)){ $keys[] = $key; }
            }
        }
        return $keys;
    }

    /**
     * update 更新数据
     * @param  array   $data
     * @param  string  $table
     * @param  array | string    $conditions
     * @return   ASResult
     */
    public function update( array $data, string $table, $conditions ): ASResult
    {

        $this->sign('ASDB->update');

        $t = time();
        $data['lasttime'] = $t;

        $data = Filter::addslashesAll($data);

        $query = "UPDATE {$table} SET ";

        for ($i=0; $i < count($data) ; $i++) {

            $query .= ($i!==0?' , ':'') .array_keys($data)[$i].' = ';

            if(array_keys($data)[$i]=='location'){
                $query .= static::generateLocationValue(array_values($data)[$i]);
            }else{
                $query .= static::generateQueryValue(array_values($data)[$i]);
            }
        }

        $query .= ' WHERE '.static::spliceCondition($conditions);
        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,i18n('SYS_SQL_ERROR')) :
            $this->take($table)->success(i18n('SYS_SQL_SUC'));

    }

    /**
     * 批量更新
     * updates
     * @param  array   $dataList
     * @param  string  $key
     * @param  string  $table
     * @param          $conditions
     * @return ASResult
     */
    public function updates( array $dataList, string $key, string $table, $conditions ): ASResult
    {

        $this->sign("ASDB->updates");

        $t = time();

        $dataList = Filter::addslashesAll($dataList);

        for ($i=0; $i < count($dataList); $i++) {

            $dataList[$i]['lasttime']   = $dataList[$i]['lasttime'] ?? $t;
        }

        $query = "UPDATE $table SET ";
        $query.= $this->spliceCaseSet($dataList,$key);
        $query.= $conditions ? ' AND '.static::spliceCondition($conditions) : '';

        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,i18n('SYS_SQL_ERROR')) :
            $this->take($table)->success(i18n('SYS_UPD_SUC'));

    }

    /**
     * 数值型字段自增
     * Increase for numberic field(int,float,double...)
     * @param  string  $field
     * @param  string  $table
     * @param  mixed   $conditions
     * @param  float   $size
     * @return ASResult
     */
    public function increase( string $field, string $table, $conditions, float $size = 1 ): ASResult
    {

        $query = "UPDATE $table SET ";
        $query.= " {$field} = {$field} + {$size} ";
        $query.= ' WHERE '.static::spliceCondition($conditions);
        $DB   = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,i18n('SYS_SQL_ERROR')) :
            $this->take($table)->success(i18n('SYS_UPD_SUC'));

    }

    /**
     * 数据减少 仅支持int float double 等数字类型
     * reduce
     * @param  string  $field
     * @param  string  $table
     * @param  mixed   $conditions
     * @param  float   $size
     * @return ASResult
     */
    public function reduce( string $field, string $table, $conditions, float $size = 1  ): ASResult
    {

        return $this->decrease( $field, $table, $conditions, $size );
    }


    /**
     * 数据增长 仅支持int float double 等数字类型
     * decrease
     * @param  string  $field
     * @param  string  $table
     * @param  null    $conditions
     * @param  float   $size
     * @return ASResult
     */
    public function decrease( string $field, string $table, $conditions, float $size = 1  ){

        return $this->increase( $field, $table, $conditions, 0 - $size );
    }


    /**
     * 从数据库中移除
     * remove row(s) from table
     * @param  string  $table
     * @param  array|string  $conditions
     * @return ASResult
     */
    public function remove( string $table, $conditions ): ASResult
    {

        $this->sign('ASDB->remove');

        $conditions = static::spliceCondition($conditions);

        $query = "DELETE FROM {$table} WHERE {$conditions}";

        if ($this->count($table,$conditions)->getContent()===0) {
            return $this->error(10086,'Target not found.');
        }

        $DB = $this->query($query);

        return (!$DB) ?
            $this->take($query)->error(550,i18n('SYS_SQL_ERROR')) :
            $this->take($table)->success(i18n('SQL_RM_SUC'));

    }

    /**
     * 通用行计数
     * count of row
     * @param  string       $table
     * @param  null         $conditions  筛选条件
     * @param  string|null  $distinct    排重字段
     * @return ASResult
     */
    public function count( string $table , $conditions = null, string $distinct = null ): ASResult
    {

        $conditions = static::spliceCondition($conditions);

        $query = " SELECT COUNT( ".($distinct ? "DISTINCT($distinct)" : "*")." ) FROM ";
        $query.= $table;
        $query.= $conditions ? ' WHERE '.$conditions : '';

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        $result = mysqli_fetch_array($DB)[0];

        return $this->take((int)$result)->success(i18n('SQL_COUNT_SUC'));

    }

    /**
     * 获取数据
     * get data
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-08-17T01:30:42+0800
     * @param array|string $fields  查询字段
     * @param string       $table   表名
     * @param null $conditions      条件
     * @param null $sort            排序方式
     * @param int $page             数据长度
     * @param int $size             页数
     * @param string|null $distinct 排重字段
     * @param null $sets            FIND_IN_SET查询方式 子集 field,values
     * @return ASResult
     *@version 1.5
     */
    public function get( $fields, string $table, $conditions = null, int $page = 1, int $size = 25, $sort = null, string $distinct = null, $sets = null ): ASResult
    {

        $this->sign("ASDB->get");

        $start = ($page-1)*$size;
        $size  = $size >10000 ? 10000 : $size ;  //
        $start = $start<0 ? 0 : $start;  // 开始不能小于0

        $query = " SELECT ";
        $query.= static::spliceFields($fields,$table,$distinct);
        $query.= " FROM {$table} ";

        if ($sets && isset($sets['field']) && isset($sets['values']) ){
            $_sets = "FIND_IN_SET(".$sets['field'].",'";
            foreach ($sets['values'] as $key => $value) {
                $_sets .= ($key==0 ? $value : ','.$value);
            }
            $_sets.= "') ";

            $conditions .= $conditions ? 'AND '.$_sets : $_sets;
        }

        $query .= $conditions ? ' WHERE '.static::spliceCondition($conditions) : '';
        $query .= $sort  ?  " ORDER BY {$sort} " : '';
        $query .= " LIMIT {$start},{$size} ";

        return $this->processQueryResult($query);
    }

    public function processQueryResult( String $query ):ASResult{

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}
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
    public function batchGet( $fields, string $table, string $key, array $keyList ): ASResult
    {

        $this->sign("ASDB->batchGet");

        $query = "SELECT ";

        $query .= static::spliceFields($fields);

        $query .= " FROM {$table}";
        $query .= " WHERE {$key} IN ";

        if (count($keyList)>=1) {
            $i = 0;
            $query .= "( ";
            foreach ($keyList as $k => $v) {
                $query .= ( $i>0 ? ' , ' : '' )." '$v' ";
                $i++;
            }
            $query .= " )";
        }

        $query .= " ORDER BY FIELD ";

        if (count($keyList)>=1) {
            $i = 0;
            $query .= "( {$key} ,";
            foreach ($keyList as $k => $v) {
                $query .= ( $i>0 ? ' , ' : '' )." '$v' ";
                $i++;
            }
            $query .= " )";
        }

        return $this->processQueryResult($query);

    }

    /**
     * 比对数据
     * check
     * @param  mixed   $value
     * @param  string  $field
     * @param  string  $table
     * @param  array|string $conditions
     * @param  null    $sort
     * @return ASResult
     */
    public function check( $value , string $field, string $table, $conditions, $sort = null ): ASResult
    {

        $this->sign("ASDB->check");

        $query  = "SELECT * FROM ";
        $query .= $table." WHERE ".static::spliceCondition($conditions);
        $query .= $sort ? " ORDER BY {$sort} LIMIT 0,1" : " ORDER BY createtime DESC LIMIT 0,1";

        $DB     = $this->query($query);
        $res    = $DB ? mysqli_fetch_array($DB) : false;

        if (!$res){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}
        // 使用了错误的表或字段数据

        if ($value !== $res[$field]){ return $this->take($value)->error(300,i18n('SYS_CHK_FAL')); }

        if (isset( $res['expire'])) { //检查是否设置过期

            $t = time();
            $e = intval($res['expire']);

            if ($t>$e){ return $this->take($e.'-'.$t)->error(308,i18n('SYS_CHK_EXPIRE')); } //数据过期

        }

        return $this->take($value)->success(i18n('SYS_CHK_SUC'));

    }

    # Fusion Request 混合查询


    /**
     * 累加求和
     * sum
     * @param  string  $key
     * @param  string  $table
     * @param  array|string $conditions
     * @param  int|null    $page
     * @param  int|null    $size
     * @return  ASResult
     * @mark    SELECT SUM(salary) as "Total Salary"    FROM employees  WHERE salary > 25000;
     */
    public function sum( string $key, string $table, $conditions = null, int $page = null, int $size = null ): ASResult
    {

        $this->sign('ASDB->sum');

        $query     = "SELECT SUM({$key}) as sum, AVG({$key}) as avg FROM {$table}";
        $query    .= $conditions ? ' WHERE '.static::spliceCondition($conditions) : '';

        if ( isset($page) && isset($size) ){
            $start     = ($page-1)*$size;
            $size      = $size>50000 ? 50000 : $size ;  //
            $start     = $start<0   ? 0   : $start;  // 开始不能小于0
            $query    .= ' LIMIT '.$start.','.$size;
        }

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        $result = mysqli_fetch_array($DB,true);

        $result['sum'] = $result['sum'] ?? 0;
        $result['avg'] = $result['avg'] ?? 0;

        return $this->take($result)->success(i18n('SYS_ANA_SUC'));

    }


    // SELECT userid,COUNT(*) AS count ,SUM(amount) AS sum FROM commerce_order GROUP BY userid ORDER BY SUM(amount) DESC

    //
    // SELECT col1,SUM(col2) FROM t1
    // GROUP BY col1
    // ORDER BY SUM(col2);

    /**
     * 求和列表统计 根据 key 字段进行求和统计
     * sumByGroup
     * @param  string|array $fields
     * @param  string       $table
     * @param  string|array $key
     * @param  string       $group
     * @param  string|array $conditions
     * @param  string|null  $sort
     * @param  int|null     $page
     * @param  int|null     $size
     * @param  string|array|null  $count
     * @return ASResult
     */
    public function sumByGroup( $fields, string $table, $key, string $group, $conditions = null, int $page = null, int $size = null, $sort = null, $count = null ): ASResult
    {

        $this->sign("ASDB->sumByGroup");

        $query = "SELECT ";

        $query.= static::spliceFields($fields);

        // 对不同的key进行累计
        if (gettype($key)=='array') {

            for ($i=0; $i <count($key) ; $i++) {

                $k = $key[$i];
                $query .= ", SUM({$k}) as sum_{$k} ";

            }

        }else if(gettype($key)=='string'){

            $query .= ", SUM({$key}) as sum_{$key} ";
        }

        // 默认统计不排重 总数
        $query .= ", COUNT(id) as count ";

        // 是否需要进行额外计数
        if ($count) {

            if (gettype($count)=='array') {

                for ($i=0; $i <count($count) ; $i++) {

                    $k = $count[$i];
                    $query .= ", COUNT(DISTINCT({$k})) as count_{$k} ";
                }

            }else if(gettype($count)=='string'){
                //单条数据

                $k = $count;
                $query .= ", COUNT(DISTINCT({$k})) as count_{$k} ";

            }
        }

        $query .= " FROM $table";
        $query .= $conditions ? ' WHERE '.$conditions : '';
        $query .= " GROUP BY {$group} ";

        if (gettype($key)=='string') {

            $query .= " ORDER BY SUM({$key}) DESC";

        }else{

            $k = $key[0];
            $query .= " ORDER BY SUM({$k}) DESC";
        }

        $query   .= $sort ? ", $sort " : "";

        if ( isset($page) && isset($size) ){
            $start     = ($page-1)*$size;
            $size      = $size>50000 ? 50000 : $size ;  //
            $start     = $start<0   ? 0   : $start;  // 开始不能小于0
            $query    .= " LIMIT {$start} , {$size}";
        }

        return $this->processQueryResult($query);

    }


    /**
     * 根据Group聚合查询 计数
     * countByGroup
     * @param  string       $group
     * @param  string       $table
     * @param  null         $conditions
     * @param  string|null  $distinct
     * @return ASResult
     */
    public function countByGroup( string $group, string $table, $conditions = null, string $distinct = null ): ASResult
    {

        $this->sign("ASDB->countByGroup");

        $query = $distinct ? "SELECT COUNT(DISTINCT($distinct)) " : 'SELECT COUNT(*) ';

        $query .= " FROM $table ";
        $query .= $conditions ? ' WHERE '.static::spliceCondition($conditions) : '';
        $query .= " GROUP BY $group ";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        $result = mysqli_fetch_array($DB)[0];

        return $this->take((int)$result)->success(i18n('SYS_ANA_SUC'));

    }

    /**
     * 根据Group聚合查询
     * getByGroup
     * @param  string|array $fields
     * @param  string       $table
     * @param  string|array $key
     * @param  string       $group
     * @param  string|array $conditions
     * @param  int|null     $page
     * @param  int|null     $size
     * @param  null         $sort
     * @param  string|null  $distinct
     * @return ASResult|bool
     * @mark   SELECT userid,COUNT(*) AS count ,SUM(amount) AS sum FROM commerce_order
     * GROUP BY userid ORDER BY SUM(amount) DESC
     * SELECT col1,SUM(col2) FROM t1  GROUP BY col1  ORDER BY SUM(col2);
     */
    public function getByGroup( $fields, string $table, $key, string $group, $conditions = null, int $page = 1, int $size = 25, $sort = null, string $distinct = null  ){

        $this->sign("ASDB->getByGroup");

        $start = ($page-1)*$size;
        $size  = $size >10000 ? 10000 : $size ;  //
        $start = $start<0 ? 0 : $start;  // 开始不能小于0

        $query = " SELECT ";
        $query.= static::spliceFields($fields,$table,$distinct);

        // 对不同的key进行累计
        if (gettype($key)=='array') {

            for ($i=0; $i <count($key) ; $i++) {

                $k = $key[$i];
                $query .= ", SUM($k) as sum_$k ";
            }

            $query .= ", COUNT(id) as count ";

        }else if(gettype($key)=='string'){

            $query .= ", SUM($key) as sum ,COUNT(id) as count ";
        }

        $query .= " FROM $table ";
        $query .= $conditions ? ' WHERE '.$conditions : '';
        $query .= " GROUP BY {$group} ";
        $query .= $sort ? " ORDER BY {$sort} " : '';
        $query .= " LIMIT {$start},{$size} ";

        return $this->processQueryResult($query);
    }


    /**
     * 联合查询模式计数 joinCount
     * @param JoinPrimaryParams $primaryParams  [primaryParams]
     * @param JoinParams[] $joinParams     [array of joinParams]
     * @return   ASResult
     * @mark     优化条件说明 https://shezw.com/archives/113/
     */
    public function joinCount( JoinPrimaryParams $primaryParams, array $joinParams ): ASResult
    {

        $table      = $primaryParams->table;
        $primaryKey = $primaryParams->key;
        $countKey   = "";

        # 判断优化条件
        // $ignorePrimary = !$filters && !$conditions; # 主表无关
        $groups = [];
        if(isset($primaryParams['group'])){
            $groups[$table] = $primaryParams['group'];
        }

        if(!empty($primaryParams->count)){
            foreach ( $primaryParams->count as $i => $c ){
                $countKey .= ($countKey ? "," : "") . $table.'.'.$c;
            }
        }

        $query  = !empty($primaryParams->count) ? "SELECT COUNT({$primaryParams->table}.{$primaryParams->count[0]}) " : "SELECT COUNT(*)";

        $query .= " FROM ".$table;

        if( !empty($joinParams) ){
            foreach ($joinParams as $i => $jParams) {

                $query .= isset($jParams->conditions) ? " LEFT JOIN ".$jParams->table : "";

                $key    = $jParams->key ?? $primaryParams->key;   # 默认为主表key
                $bind   = $jParams->bind;         # 当该组数据并不是与主表key进行绑定时，可以进行自定义绑定

                $query .= isset($jParams->conditions) ? (" ON " . ($bind ?? "{$table}.{$primaryKey}") . " = {$jParams->table}.{$key}") : '';

            }
        }

        # 主表条件
        $where = $primaryParams->conditions ? "WHERE".static::spliceCondition($primaryParams->conditions,null,$table) : "";

        # 副表条件
        foreach ($joinParams as $i => $jParams) {

            $__ = (!$where && $i===0) ? " WHERE " : " AND ";
            $where .= isset($jParams->conditions) ? $__.static::spliceCondition($jParams->conditions,null,$jParams->table) : "" ;
        }

        # 默认保留主表
        $query .= " $where";
        $query .= $countKey ? " GROUP BY {$countKey} " :'';
        $query  = $countKey ? " SELECT COUNT(*) FROM ( $query ) AS TMP" : $query ;

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        $result = mysqli_fetch_array($DB)[0];

        return $this->take((int)$result)->success(0,i18n('SYS_ANA_SUC'));
    }


    /**
     * 联合模式查询
     * 支持多张表联合查询,取消JOIN方向,INNER使用句末WHERE方式取代.
     * 其中每一行可以定义 key|field(索引字段)
     * Get data by JOIN tables
     * @param  JoinPrimaryParams  $primaryParams    主表参数 $table 必填, $key|$field 必填, $fields 可选, filters,conditions,count 可选,
     * @param  int                $page
     * @param  int                $size
     * @param  string             $sort
     * @param JoinParams[] $joinParams       副表参数(多个副表数组)] $table 必填, $key|$field 可选, $fields 可选, filters (表内过滤)可选,conditions (全局筛选)可选
     * @return ASResult
     * @mark   SELECT * FROM user_account LEFT JOIN user_info ON user_account.userid = user_info.userid
     *                                            LEFT JOIN user_pocket ON user_account.userid = user_pocket.userid AND user_pocket.point > 5000
     *                                            LEFT JOIN user_group ON user_account.groupid = user_group.groupid AND user_group.level > 100
     *                                            WHERE user_info.wechatid IS NOT null
     * @mark   SELECT *,COUNT(relation_combine.relationid) as count_unit FROM relation_combine
     *                                            LEFT JOIN item_scene ON relation_combine.itemid = item_scene.sceneid
     *                                            GROUP BY relation_combine.itemid,relation_combine.itemtype AND relation_combine.itemtype='scene'
     */
    public function joinGet( JoinPrimaryParams $primaryParams, array $joinParams, int $page = 1 , int $size = 25 , string $sort = null ):ASResult{

        if( count($joinParams) == 0 ){
            return $this->error(600,'joinParams is empty');
        }

        $this->sign("ASDB->joinGet");
        $filtered = 0;

        $alias = [];

        $query = "SELECT ";
        $query.= static::spliceFields($primaryParams->fields,$primaryParams->table);

        $table = $primaryParams->table;
        $primaryKey = $primaryParams->key;

        $groups = []; # GROUP BY FILTER
        if( !empty($primaryParams->group) ){
            $groups[$primaryParams->table] = $primaryParams->group;
        }

        $counts = []; # COUNT( FIELD )
        if(!empty($primaryParams->count)){
            $counts[$primaryParams->table] = $primaryParams->count;
        }

        $sums = []; # SUM( FIELD ) AS SUM_FIELD
        if(!empty($primaryParams->sum)){
            $sums[$primaryParams->table] = $primaryParams->sum;
        }

        foreach ($joinParams as $i => $jParams ) {

            if(isset($jParams->alias)){ $alias[] = $jParams->alias; }
            if(!empty($jParams->sum)){
                $sums[$jParams->table] = [];
                foreach ( $jParams->sum as $k => $s ){
                    $sums[$jParams->table][] = [$s,$jParams->sumAs[$k]];
                }
            }
            if(!empty($jParams->count)){
                $counts[$jParams->table] = [];
                foreach ( $jParams->count as $k => $s ){
                    $counts[$jParams->table][] = [$s,$jParams->countAs[$k]];
                }
            }
            if(!empty($jParams->groupConditions)){
                $groups[$jParams->table] = $jParams->groupConditions;
            }

            $selections = static::spliceFields($jParams->fields,$jParams->table,null,$jParams->alias);
            $query .= ', '.$selections;
        }

        if( !empty($counts) ){

            $countSelect = "";

            foreach ($counts as $_table => $key) {

                foreach ($key as $k => $g) {

                    $countSelect .= ", COUNT({$_table}.{$g[0]}) AS {$g[1]}";
                }
            }
            $query.= $countSelect;
        }


        if( !empty($sums) ){

            $sumSelect = "";

            foreach ($sums as $_table => $key) {

                foreach ($key as $k => $g) {

                    $sumSelect .= ", SUM({$_table}.{$g[0]}) AS {$g[1]}";
                }
            }
            $query.= $sumSelect;
        }

        $query.= " FROM ".$table;

        foreach ($joinParams as $i => $jParams) {

            $query .= " LEFT JOIN ".$jParams->table;

            $key    = $jParams->key  ?? $primaryKey; # 默认为主表key
            $bind   = $jParams->bind ?? null;       # 当该组数据并不是与主表key进行绑定时，可以进行自定义绑定

            $query .= " ON " . ($bind ?? "{$table}.{$primaryKey}") . " = {$jParams->table}.{$key}";

            $query .= $jParams->filters ? " AND ".static::spliceCondition($jParams->filters,null,$jParams->table) : '';

            if( !empty($jParams->group) ){

                $groupby = "";

                foreach ($jParams->group as $k => $g) {

                    $groupby .= $groupby ? ',' : '';
                    $groupby .= " {$jParams->table}.{$g} ";
                }
                $query .= " GROUP BY $groupby ";
                $filtered ++;
            }
        }

        # 主表条件
        $where  = $primaryParams->conditions ? ( $filtered ? " AND " : " WHERE " ).static::spliceCondition($primaryParams->conditions,null,$primaryParams->table) : "";

        # 副表条件
        foreach ($joinParams as $i => $jParams) {

            $__ = (!$where && $i===0) ? " WHERE " : " AND ";
            $where .= isset($jParams->conditions) ? $__.static::spliceCondition($jParams->conditions,null,$jParams->table) : "" ;
        }

        $query .= $where;

        if( !empty($groups) ){

            $groupby = "";

            foreach ($groups as $g_table => $group) {

                foreach ($group as $k => $g) {

                    $groupby .= $groupby ? ',' : '';
                    $groupby .= " $g_table.$g ";
                }
            }
            $query .= " GROUP BY $groupby ";
        }

        $start  = ($page-1)*$size;

        $size   = $size>1000 ? 1000 : $size;
        $start  = $start<0  ? 0   : $start;  // 开始不能小于0

        $query .= $sort  ?  " ORDER BY $sort" : '';
        $query .= ' LIMIT '.$start.','.$size;

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}
        if ( mysqli_num_rows($DB) === 0 ){ return $this->take(false)->error(400,i18n('SYS_GET_NON')); }

        $result = [];
        $re = mysqli_fetch_all($DB,true);

        foreach ( $re as $r ) {

            if(!empty($alias)){

                foreach ($alias as $i => $a) {

                    $aliasData = [];

                    foreach ($r as $key => $value) {

                        if(strstr($key, $a."$")){
                            unset($r[$key]);
                            $aliasData[str_replace($a."$", "", $key)] = $value;
                        }
                    }
                    $r[$a] = $aliasData;
                }
            }

            $result[] = $r;
        }

        return $this->take($result)->success(i18n('SYS_GET_SUC'));

    }


    /* Database io 表相关操作 */

    public function showTables( string $base = null ): ASResult
    {

        $this->sign('ASDB->showTables');

        $base = $base ?? $this->base ?? getConfig('base');

        $query = "SELECT * FROM information_schema.tables WHERE table_schema='{$base}'";

        return $this->processQueryResult($query);

    }

    public function showColumns( $table ): ASResult
    {

        $this->sign('ASDB->showColumns');

        $query = "SELECT COLUMNS.*, STATISTICS.INDEX_NAME, STATISTICS.INDEX_TYPE, STATISTICS.CARDINALITY, STATISTICS.COLLATION "
            . "FROM INFORMATION_SCHEMA.COLUMNS LEFT JOIN INFORMATION_SCHEMA.STATISTICS "
            . "ON COLUMNS.COLUMN_NAME=STATISTICS.COLUMN_NAME AND COLUMNS.TABLE_NAME = STATISTICS.TABLE_NAME AND  STATISTICS.TABLE_SCHEMA = '{$this->base}' AND STATISTICS.TABLE_NAME = '$table' "
            . "WHERE COLUMNS.TABLE_SCHEMA = '{$this->base}' AND COLUMNS.TABLE_NAME = '{$table}' ";

        return $this->processQueryResult($query);

    }

    public function exists( $table ){

        $query = "show tables like '{$table}'";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return mysqli_num_rows($DB)>0;
    }


    // 创建表
    public function newTable( array $_ ): ASResult
    {

        //  @fields array of arrays
        //  @field  key-value array
        //  $field->name,type,length,default,primary,unique,autoincrement

        $this->sign("ASDB->newTable");

        $fields  = $_['fields']  ?? null  ;
        $table   = $_['table']   ?? null  ;
        $engine  = $_['engine']  ?? 'InnoDB'  ;
        $collate = $_['collate'] ?? 'utf8mb4_general_ci'  ;
        $charset = $_['charset'] ?? 'utf8mb4'  ;

        // Check required arguments
        if(!$fields ){ return $this->error(800,'fields is requried!');}
        if(!$table ){ return $this->error(800,'Table is requried!');}

        $fields[] = ['name'=>'createtime','type'=>'bigint',   'len'=>11,'dft'=>'','cmt'=>'创建时间戳','index'=>1];
        $fields[] = ['name'=>'lasttime',  'type'=>'bigint',   'len'=>11,'dft'=>'','cmt'=>'最后更新时间戳',];
        $fields[] = ['name'=>'featured',  'type'=>'tinyint',  'len'=>1, 'dft'=>0, 'cmt'=>'置顶',];
        $fields[] = ['name'=>'sort',      'type'=>'mediumint','len'=>5, 'dft'=>0, 'cmt'=>'优先排序',];

        $this->query("DROP TABLE IF EXISTS {$table};");

        // 生成Query
        $query = "CREATE TABLE IF NOT EXISTS {$table} (";

        // 自动添加 id字段
        $query.= "id BIGINT(15) NOT NULL AUTO_INCREMENT COMMENT '序列ID'";

        // 初始化
        $primary = ""; // 主键
        $unique  = []; // 唯一键位
        $ngram   = []; // 分词键位
        $index   = []; // 索引键位
        $spatial = []; // 定位键位

        $tableComment = "";

        // 循环遍历字段 生成Query主体
        for ($i=0; $i < count($fields) ; $i++) {

            $field = $fields[$i];

            if( isset($field['table']) ){
                $tableComment = $field['table'];
                continue;
            }

            if(!$field['name'] ){ return $this->error(800,'Name is requried!','ASDB->newTable->generateQuery');}
            if(!$field['type'] ){ return $this->error(800,'Type is requried!','ASDB->newTable->generateQuery');}
            if(!$field['len']  ){ return $this->error(800,'Length is requried!','ASDB->newTable->generateQuery');}

            $query.= " , ".$field['name']." ".$field['type']; // 字段类型
            $query.= $field['len']==='NAN' || $field['len']===-1 ? '' : "(".$field['len'].")"; // 设置字段长度

            if (isset( $field['dft']) && $field['dft']!=='NULL' && $field['dft']!=='') { // 字段是否可以为空

                $query .= " NOT NULL DEFAULT '".$field['dft']."'";

            }else if($field['dft']==='NULL'){

                $query .= " NULL DEFAULT NULL";

            }else{

                $query .= " NOT NULL";
            }

            if (isset( $field['unq']) && $field['unq']!=='' && $field['unq']) { // 唯一字段
                $unique[]= ['name'=>$field['name']];
            }

            if (isset( $field['ngr']) && $field['ngr']!=='' && $field['ngr']) { // ngram 分词字段
                $ngram[] = ['name'=>$field['name']];
            }

            if (isset( $field['idx']) && $field['idx']!=='' && $field['idx']) { // index 索引字段
                $index[] = ['name'=>$field['name']];
            }

            if (isset( $field['spt']) && $field['spt']!=='' && $field['spt']) { // spatial 空间字段
                $spatial[] = ['name'=>$field['name']];
            }

            $query.= isset($field['cmt']) ? " COMMENT '".$field['cmt']."'" : ""; // 添加注释

        }

        $query .= $primary ? ", PRIMARY KEY ( ".$primary." ) " : ', PRIMARY KEY (id) ';

        if (isset( $unique)&&$unique!=='') {
            $uni = '';
            if (count($unique)===1) {
                $uni =  ", UNIQUE (".$unique[0]['name'].") USING HASH ";
            }
        }
        if (isset( $ngram)&&$ngram!=='') {
            $ngr = '';
            if (count($ngram)===1) {
                $ngr =  ", FULLTEXT (".$ngram[0]['name'].") WITH PARSER ngram ";
            }
        }

        if (isset( $spatial)&&$spatial!=='') {
            $spt = '';
            if (count($spatial)===1) {
                $spt =  ", SPATIAL (".$spatial[0]['name'].") ";
            }
        }

        $query .= $uni  ? $uni : '';
        $query .= $ngr  ? $ngr : '';
        $query .= $spt  ? $spt : '';
        $query .= ")";

        $query .= " ENGINE  $engine";
        $query .= " CHARSET $charset";
        $query .= " COLLATE $collate";
        $query .= $tableComment ? " COMMENT = '$tableComment' " : '';

        // 连接数据库
        $DB     = $this->query($query);

        // 索引字段多个的时候合并索引
        if (isset( $index)&& count($index)>0) {
            for ($i=0; $i < count($index) ; $i++) {
                $this->index(['field'=>$index[$i]['name'],'table'=>$table]);
            }
        }

        // 其他类型不合并
        if (isset( $unique)&& count($unique)>1) {
            for ($i=0; $i < count($unique) ; $i++) {
                $this->unique(['field'=>$unique[$i]['name'],'table'=>$table]);
            }
        }

        if (isset( $ngram)&& count($ngram)>1) {
            for ($i=0; $i < count($ngram) ; $i++) {
                $this->ngram(['field'=>$ngram[$i]['name'],'table'=>$table]);
            }
        }

        if (isset( $spatial)&& count($spatial)>1) {
            for ($i=0; $i < count($spatial) ; $i++) {
                $this->spatial(['field'=>$spatial[$i]['name'],'table'=>$table]);
            }
        }

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success(i18n('SYS_SQL_SUC'));

    }

    public static function generateTableQuery( array $_ ): string
    {

        $fields  = $_['fields']  ?? null  ;
        $table   = $_['table']   ?? null  ;
        $engine  = $_['engine']  ?? 'InnoDB'  ;
        $collate = $_['collate'] ?? 'utf8mb4_general_ci'  ;
        $charset = $_['charset'] ?? 'utf8mb4'  ;

        $fields[] = ['name'=>'createtime','type'=>'bigint',   'len'=>11,'dft'=>'','cmt'=>'创建时间戳','index'=>1];
        $fields[] = ['name'=>'lasttime',  'type'=>'bigint',   'len'=>11,'dft'=>'','cmt'=>'最后更新时间戳',];
        $fields[] = ['name'=>'featured',  'type'=>'tinyint',  'len'=>1, 'dft'=>0, 'cmt'=>'置顶',];
        $fields[] = ['name'=>'sort',      'type'=>'mediumint','len'=>5, 'dft'=>0, 'cmt'=>'优先排序',];

        // 生成Query
        $query = "CREATE TABLE IF NOT EXISTS {$table} (";

        // 自动添加 id字段
        $query.= "id BIGINT(15) NOT null AUTO_INCREMENT COMMENT '序列ID'";

        // 初始化
        $primary = ""; // 主键
        $unique  = []; // 唯一键位
        $ngram   = []; // 分词键位
        $index   = []; // 索引键位
        $spatial = []; // 定位键位

        $tableComment = "";

        // 循环遍历字段 生成Query主体
        for ($i=0; $i < count($fields) ; $i++) {

            $field = $fields[$i];

            if( isset($field['table']) ){
                $tableComment = $field['table'];
                continue;
            }

            $query.= " , ".$field['name']." ".$field['type']; // 字段类型
            $query.= $field['len']==='NAN' || $field['len']===-1 ? '' : "(".$field['len'].")"; // 设置字段长度

            if (isset( $field['dft']) && $field['dft']!=='NULL' && $field['dft']!=='') { // 字段是否可以为空

                $query .= " NOT NULL DEFAULT '".$field['dft']."'";

            }else if($field['dft']==='NULL'){

                $query .= " NULL DEFAULT NULL";

            }else{

                $query .= " NOT NULL";
            }

            $query.= isset($field['cmt']) ? " COMMENT '".$field['cmt']."'" : ""; // 添加注释
        }

        $query .= $primary ? ", PRIMARY KEY ( ".$primary." ) " : ', PRIMARY KEY (id) ';

        if (isset( $unique)&&$unique!=='') {
            $uni = '';
            if (count($unique)===1) {
                $uni =  ", UNIQUE (".$unique[0]['name'].") USING HASH ";
            }
        }
        if (isset( $ngram)&&$ngram!=='') {
            $ngr = '';
            if (count($ngram)===1) {
                $ngr =  ", FULLTEXT (".$ngram[0]['name'].") WITH PARSER ngram ";
            }
        }

        if (isset( $spatial)&&$spatial!=='') {
            $spt = '';
            if (count($spatial)===1) {
                $spt =  ", SPATIAL (".$spatial[0]['name'].") ";
            }
        }

        $query .= $uni  ? $uni : '';
        $query .= $ngr  ? $ngr : '';
        $query .= $spt  ? $spt : '';
        $query .= ")";

        $query .= " ENGINE  $engine";
        $query .= " CHARSET $charset";
        $query .= " COLLATE $collate";
        $query .= $tableComment ? " COMMENT = '$tableComment' " : '';

        return $query;

    }

    // 公用
    // fields编辑
    public function fields( array $_ ){
        //  @fields array of arrays
        //  @field  key-value array
        //  $field->name,type,length,default,primary,unique,autoincrement
        $this->sign("ASDB->fields");

        $fields  = $_['fields']  ?? null  ;
        $table   = $_['table']   ?? null  ;
        $func    = $_['func']    ?? null  ;
        $engine  = $_['engine']  ?? 'InnoDB'  ;
        $collate = $_['collate'] ?? 'utf8mb4_general_ci'  ;
        $charset = $_['charset'] ?? 'utf8mb4'  ;

        // Check required arguments
        if(!$fields ) { return $this->error(800,'fields is requried!');}
        if(!$table ) { return $this->error(800,'Table is requried!');}

        $query = "ALTER TABLE $table ";

        // 初始化
        $primary = ""; // 主键
        $unique  = []; // 唯一键位
        $ngram   = []; // 分词键位
        $index   = []; // 索引键位

        for ($i=0; $i < count($fields) ; $i++) {
            $field = $fields[$i];

            if(!$field['name']){ return $this->take($i)->error(800,'Name is requried!','ASDB->fields->field');}
            if(!$field['type']){ return $this->take($i)->error(800,'Type is requried!','ASDB->fields->field');}
            if(!$field['len']){ return $this->take($i)->error(800,'Length is requried!','ASDB->fields->field');}

            if ($i>0) {
                $query.=", ";
            }

            $query.= $func." ".$field['name']." ".$field['type']."(".$field['len'].")";

            if (isset( $field['dft']) && $field['dft']!=='NULL' && $field['dft']!=='') {

                $query.= " NOT NULL DEFAULT '".$field['dft']."'";

            }else if($field['dft']==='NULL'){

                $query.= " NULL DEFAULT NULL";

            }else{

                $query.= " NOT NULL";
            }

            if (isset( $field['unq']) && $field['unq']!=='' && $field['unq']) {
                $unique.=", ADD UNIQUE (".$field['name'].")";
            }

            if (isset( $field['ngr']) && $field['ngr']!=='' && $field['ngr']) {
                $ngram .=",".$field['name'];
            }

            if (isset( $field['idx']) && $field['idx']!=='' && $field['idx']) {
                $index[] = ['name'=>$field['name']];
            }

        }

        $query .= $primary ? ", PRIMARY KEY ( ".$primary." ) " : ', PRIMARY KEY (id) ';

        $query .= $unique  ? $unique : '';
        $query .= ")";

        $query .= " ENGINE $engine";
        $query .= " CHARSET $charset";
        $query .= " COLLATE $collate";

        // 连接数据库
        $DB = $this->query($query);

        if (isset( $index)) {
            $this->index(['fields'=>$index,'table'=>$table]);
        }

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($fields)->success(i18n('SYS_UPD_SUC'));

    }

    // 修改表结构  //添加字段
    public function addfields( array $_ ): ASResult
    {

        $_['func']='ADD';
        return $this->fields($_);

    }

    // 修改表结构  //编辑字段
    public function updatefields( array $_ ): ASResult
    {

        $_['func']='CHANGE';
        return $this->fields($_);

    }

    // 建立索引
    public function index( array $_ ): ASResult
    {

        $this->sign("ASDB->index");

        $field  = $_['field']  ?? null ;
        $table   = $_['table'] ?? null ;

        // Check required arguments
        if(!$table  ){ return $this->error(800,'Table is requried!');}
        if(!$field ){ return $this->error(800,'field is requried!');}

        $query = "ALTER TABLE $table ADD INDEX( $field ) USING HASH";

        // 连接数据库
        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success(i18n('SYS_DFT_SUC'));

    }

    // 设定唯一索引
    public function unique( array $_ ): ASResult
    {

        $this->sign("ASDB->unique");

        $field = $_['field']  ?? null ;
        $table = $_['table']  ?? null ;

        // Check required arguments
        if(!$field){ return $this->error(800,'Unique is requried!');}
        if(!$table){ return $this->error(800,'Table is requried!');}

        $query = "ALTER TABLE {$table} ADD UNIQUE( {$field} ) USING HASH";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success('Unique Index success!');

    }

    // Fulltext索引 分词索引
    // ALTER TABLE base.table ADD FULLTEXT field;
    public function ngram( array $_ ): ASResult
    {

        $this->sign("ASDB->ngram");

        $field  = $_['field']  ?? null ;
        $table  = $_['table']  ?? null ;

        // Check required arguments
        if(!$field ){return $this->error(800,'Unique is requried!');}
        if(!$table ){return $this->error(800,'Table is requried!');}

        $query = "ALTER TABLE $table ADD FULLTEXT ( $field ) WITH PARSER ngram ";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success('Ngram index success!');

    }

    // spatial 空间索引
    // ALTER TABLE base.table ADD FULLTEXT field;
    public function spatial( array $_ ): ASResult
    {

        $this->sign("spatial");

        $field   = $_['field'] ?? null ;
        $table   = $_['table'] ?? null ;

        // Check required arguments
        if(!$field){ return $this->error(800,'Spatial is requried!');}
        if(!$table){ return $this->error(800,'Table is requried!');}

        $query = "ALTER TABLE $table ADD SPATIAL ( $field ) ";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success('Spatial index success!');

    }


    // 修改表结构 //移除字段
    public function drop( array $_ ): ASResult
    {

        $this->sign("ASDB->drop");

        $fields  = $_['fields'] ?? null ;
        $table   = $_['table']  ?? null ;

        // Check required arguments
        if(!$fields ){return $this->error(800,'fields is requried!');}
        if(!$table  ){return $this->error(800,'Table is requried!');}

        $query = "ALTER TABLE $table ";

        // 循环遍历字段 生成Query主体
        for ($i=0; $i < count($fields) ; $i++) {

            $field = $fields[$i];

            if(!$field['name']){ $this->take($i)->error(800,'Name is requried!','ASDB->drop->generateQuery');}

            if ($i>0) {
                $query.= ", ";
            }
            $query.="DROP ".$field['name'];
        }

        // 连接数据库
        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success(i18n('SYS_DEL_SUC'));

    }


    // 清空数据表
    public function truncate( string $table ): ASResult
    {

        $this->sign("ASDB->truncate");

        $query = "TRUNCATE $table";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success('Truncate success!');

    }

    // 移除数据表
    public function dropTable( string $table ): ASResult
    {

        $this->sign("ASDB->dropTable");

        $query = "DROP TABLE IF EXISTS {$table} ";

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($table)->success('Drop success!');

    }

    // 库相关操作
    // !!! 需要数据库Root权限 !!!
    public function base( array $_=null ): ASResult
    {

        $this->sign("ASDB->base");

        $func    = $_['func'] ?? null ;
        $base    = $_['base'] ?? getConfig('DB_NAME') ;

        // Check required arguments
        if(!isset($base) ){$this->error(800,'Base is requried!');}
        if(!isset($func) ){$this->error(800,'Func is requried!');}

        $query = $func." DATABASE ".$base;

        $DB = $this->query($query);

        if (!$DB){ return $this->take($query)->error(550,i18n('SYS_SQL_ERROR'));}

        return $this->take($func.'-'.$base)->success('success!');

    }

    /**
     * 新数据库
     * newBase
     * @param  string  $base
     * @return ASResult
     */
    public function newBase( string $base ): ASResult
    {

        $__['base'] = $base;
        $__['func'] = 'CREATE';
        return $this->base($__);

    }

    /**
     * 移除数据库
     * dropBase
     * @param  string  $base
     * @return ASResult
     */
    public function dropBase( string $base ): ASResult
    {

        $__['base'] = $base;
        $__['func'] = 'DROP';

        return $this->base($__);

    }

    /**
     * 查询是否存在数据库
     * hasBase
     * @param  string  $base
     * @return bool
     */
    public function hasBase( string $base ): bool
    {
        $tmpBase = $this->base;
        $this->base = 'information_schema';
        $count = $this->count('SCHEMATA',["SCHEMA_NAME"=>$base])->getContent();
        $this->base = $tmpBase;
        return $count>0;
    }


    /**
     * 查询是否存在数据库下对应表
     * hasTableInBase
     * @param  string  $table
     * @param  string  $base
     * @return bool
     */
    public function hasTableInBase( string $table, string $base ): bool
    {

        if (!$this->hasBase($base)) {
            return false;
        }
        $tmpBase = $this->base;
        $count = $this->count('TABLES',["TABLE_SCHEMA"=>$base,"TABLE_NAME"=>$table])->getContent();
        $this->base = $tmpBase;
        return $count>0;
    }


    /**
     * 根据数据结构生成数据库
     * generate DataStruct
     * @param  array   $struct
     * @param  string  $base
     * @return ASResult
     */
    public function newDataStruct( array $struct, string $base): ASResult
    {

        $this->connect();

        $this->hasBase($base)
        && $this->dropBase($base)
        && $this->newBase($base);

        $this->hasBase($base)
        || $this->newBase($base);

        $addResult = [];

        foreach ($struct as $class => $table) {

            foreach ($table as $key => $value) {

                $res = $this->newTable(['fields'=>$value,'table'=>$class.'_'.$key]);
                $addResult[] = $res;
            }
        }
        return $this->take($addResult)->feedback();
    }

    /**
     * 根据结构清理数据表
     * clearDataStruct
     * @param  array  $struct
     * @return ASResult
     */
    public function clearDataStruct( array $struct ): ASResult
    {

        $this->connect();

        $dropResult = [];

        foreach ($struct as $class => $table) {

            foreach ($table as $key => $value) {

                $dropResult[] = $this->dropTable($class.'_'.$key);
            }
        }
        return $this->take($dropResult)->feedback();
    }


    /**
     * 根据结构化数据自动填充至数据库
     * autoInsertData
     * @param  array  $dataWithStruct
     * @param  string $base
     * @return ASResult
     */
    public function autoInsertData( array $dataWithStruct, string $base = null ): ASResult
    {

        if( isset($base) ){ $this->selectDB($base); }
        $addResult = [];

        foreach ( $dataWithStruct as $class => $dataList ){

            foreach ( $dataList as $i => $data ){

                $addResult[] = $class::common()->add( $data );
            }
        }
        return $this->take($addResult)->feedback();
    }


    // Search # 搜索

    /**
     * 可能性搜索 计数
     * maybeCount
     * @param  array   $target
     * @param  string  $table
     * @return ASResult
     */
    public function maybeCount(array $target, string $table ): ASResult
    {

        $this->sign("ASDB->maybeCount");

        if (count($target)>10){ return $this->take($target)->error(8000,'Too many arguments! Limited with 10.');}

        $i = 0;
        $conditions='';

        foreach ($target as $key => $value) {

            $conditions.= $i>0 ? " OR " : '' ;
            $conditions.= "$key='$value' ";
            $i++;
        }

        return $this->count($table,$conditions);

    }

    /**
     * 可能性搜索
     * maybeSearch
     * @param  array   $target
     * @param  string  $table
     * @param  string|array|null  $fields
     * @return ASResult
     */
    public function maybeSearch( array $target, string $table, $fields = null ): ASResult
    {

        $this->sign("ASDB->maybeSearch");

        if (count($target)>10){ return $this->take($target)->error(8000,'Too many arguments! Limited with 10.');}

        $i = 0;
        $conditions='';

        foreach ($target as $key => $value) {

            $conditions.= $i>0 ? " OR " : '' ;
            $conditions.= "$key='$value' ";
            $i++;
        }

        return $this->get($fields,$table,$conditions);
    }


    /**
     * 精确搜索 计数
     * exactCount
     * @param  array   $target
     * @param  string  $table
     * @return ASResult
     */
    public function exactCount( array $target, string $table ): ASResult
    {

        $this->sign("ASDB->exactCount");

        if (count($target)>10){ return $this->take($target)->error(8000,'Too many arguments! Limited with 10.');}

        $i = 0;
        $conditions='';

        foreach ($target as $key => $value) {

            $conditions.= $i>0 ? "AND " : '' ;
            $conditions.= "$key='$value' ";
            $i++;
        }

        return $this->count($table,$conditions);

    }


    /**
     * 精确搜索
     * exactSearch
     * @param  array   $target
     * @param  string  $table
     * @param  null    $fields
     * @return ASResult
     */
    public function exactSearch( array $target, string $table, $fields = null ): ASResult
    {

        $this->sign("ASDB->exactSearch");

        if (count($target)>10){ return $this->take($target)->error(8000,'Too many arguments! Limited with 10.');}

        $i = 0;
        $conditions='';

        foreach ($target as $key => $value) {

            $conditions.= $i>0 ? "AND " : '' ;
            $conditions.= "$key='$value' ";
            $i++;
        }

        return $this->get($fields,$table,$conditions);
    }


    /**
     * 模糊搜索 计数
     * blurCount
     * @param  array   $target
     * @param  string  $table
     * @return ASResult
     */
    public function blurCount(array $target, string $table ): ASResult
    {

        $this->sign("ASDB->blurCount");

        if (count($target)>3){ return $this->take($target)->error(8000,'Too many arguments! Limited with 3.');}

        $i = 0;
        $conditions='';

        foreach ($target as $key => $value) {

            $conditions.= $i>0 ? "OR " : '' ;
            $conditions.= "$key LIKE '%$value%' ";
            $i++;
        }

        return $this->count($table,$conditions);
    }

    /**
     * 模糊搜索
     * blurSearch
     * @param  array  $_
     * @return ASResult
     */
    public function blurSearch( array $target, string $table, $fields = null ): ASResult
    {

        $this->sign("ASDB->blurSearch");

        if (count($target)>3){ return $this->take($target)->error(8000,'Too many arguments! Limited with 3.');}

        $i = 0;
        $conditions='';

        foreach ($target as $key => $value) {

            $conditions.= $i>0 ? "OR " : '' ;
            $conditions.= "$key LIKE '%$value%' ";
            $i++;
        }

        return $this->get($fields,$table,$conditions);
    }


    /**
     * 分词搜索计数 (自然语言)  需要fulltext索引
     * natureCount Require fulltext index
     * @param          $target
     * @param  string  $value
     * @param  string  $table
     * @param  null    $conditions
     * @return ASResult
     */
    public function natureCount( $target, string $value, string $table, $conditions = null): ASResult
    {

        return $this->natureSearch($target,$value,$table,$conditions,null,true);
    }

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
    public function natureSearch( $target, string $value, string $table, $conditions = null, $fields = null, bool $isCounting = false  ): ASResult
    {

        $this->sign($isCounting ? "ASDB->natureCount" : "ASDB->natureSearch");

        if (count($target)>5){ return $this->take($target)->error(8000,'Too many arguments! Limited with 5.');}

        $query = ' (';
        for ($i=0;$i<count($target);$i++) {
            $name = $target[$i];
            $query.= $i>0 ? " OR " : '' ;
            $query.=" MATCH ( {$name} ) AGAINST ('{$value}' IN NATURAL LANGUAGE MODE) ";
        }
        $query.= ') ';

        $conditions = ($conditions ? " $conditions AND " : "").$query;

        return $isCounting ? $this->count($table,$conditions) : $this->get($fields,$table,$conditions);

    }


    /**
     * 分词搜索 计数(布尔)
     * booleanCount
     * @param  array   $target
     * @param  array   $valueList
     * @param  string  $table
     * @return ASResult
     * @mark   valueList ['+value','-value','~value','>value','<value']
     */
    public function booleanCount(array $target, array $valueList, string $table ): ASResult
    {

        return $this->booleanSearch($target,$valueList,$table,null,true);
    }


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
    public function booleanSearch(array $target, array $valueList, string $table ,$fields = null, bool $isCounting = false ): ASResult
    {

        $this->sign($isCounting ? "ASDB->booleanCount" : "ASDB->booleanSearch" );

        if (count($target)>3) {
            return $this->take($target)->error(8000,'Too many arguments! Limited with 3.');
        }

        if (count($valueList)>5) {
            return $this->take($valueList)->error(8000,'Too many arguments! Limited with 5.');
        }

        $conditions='MATCH (';

        for ($i=0;$i<count($target);$i++) {
            $name = $target[$i];
            $conditions.= $i>0 ? ", " : '' ;
            $conditions.= "$name";
        }

        $conditions.=") AGAINST ( '";

        for ($j=0;$j<count($valueList);$j++) {
            $conditions.= ' '.$valueList[$j];
        }

        $conditions.="' IN BOOLEAN MODE)";

        return $isCounting ? $this->count($table,$conditions) : $this->get($fields,$table,$conditions);

    }


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
    public static function spliceCondition( $params = null , array $filter = null, string $table = null ){

        if(!$params){ return ""; }
        if (gettype($params) == 'string' ){ return $params; }

        $condition = '';
        $count     = 0;

        if ($filter) { $params = Filter::purify($params,$filter); }

        $params = Filter::removeInvalid($params);

        foreach ($params as $key => $value) {

            if (isset($value)) {

                $condition .= $count>0 ? ' AND ' : ' ';

                if($key==="keyword"||$key==="KEYWORD"){

                    if(isset($value['value'])){

                        $val = $value['value'];

                        $condition .= ' (';
                        for ($i=0;$i<count($value['target']);$i++) {
                            $name = $value['target'][$i];
                            $condition .= $i>0 ? " OR " : '' ;
                            $condition .=' MATCH (';
                            $condition .= $table ? " $table." : ' ';
                            $condition .= "$name";
                            $condition .= ") AGAINST ('$val' IN NATURAL LANGUAGE MODE) ";
                        }
                        $condition .= ') ';

                    }else{

                        $condition .= ' MATCH( ';
                        $condition .= $table ? " $table." : ' ';
                        $condition .= 'title';
                        $condition .= ") AGAINST ('$value' IN NATURAL LANGUAGE MODE) ";
                    }

                }else if(gettype($value)=='array' && count($value) == count($value,1)){

                    $condition .= " (";
                    for ($i=0; $i <count($value) ; $i++) {

                        $condition .= ($i!==0?' OR ':'');
                        $condition .= $table ? " $table." : ' ';
                        $condition .= "$key";

                        if(static::hasSymbol($value[$i])){
                            $symbol_   = static::getSymbol($value[$i],true);
                            $symbol    = static::getSymbol($value[$i],false);
                            $value[$i] = str_replace($symbol_, '', $value[$i]);
                            $condition.= $symbol;
                        }else{
                            $condition.= "=";
                        }

                        $condition .= static::generateQueryValue($value[$i]);
                    }
                    $condition .= ") ";

                }else if(static::hasSymbol($value)){

                    $symbol_ = static::getSymbol($value,true);
                    $symbol  = static::getSymbol($value,false);
                    $value   = str_replace($symbol_, '', $value);

                    if( $symbol_ != '[[FIND]]' && $symbol_ != '[[QUERY]]' ){

                        $condition .= $table ? " $table." : ' ';
                    }

                    if($symbol_=='[[IN]]'){
                        $values = explode(',', $value);
                        $V = '';
                        for ($i=0; $i < count($values); $i++) {

                            $V .= $i==0 ? '' : ',';
                            $V .= Encrypt::isNumber($values[$i]) ? $values[$i] : "'{$values[$i]}'";

                        }
                        $condition .= "{$key} {$symbol} ({$V}) " ;

                    }else if( $symbol_ == '[[BETWEEN]]' ){

                        $value = str_replace(",", " AND ", $value);
                        $condition .= $key.$symbol.$value;

                    }else if( $symbol_ == '[[FIND]]' ){

                        $condition .= " {$symbol}( '{$value}' ,";
                        $condition .= $table ? " $table." : ' ';
                        $condition .= " {$key} )";

                    }else if( $symbol_ == '[[QUERY]]' ){

                        $condition .= " {$value} ";

                    }else if( strstr($symbol_,'>') || strstr($symbol_, '<') ){

                        $condition .=  "{$key} {$symbol} {$value} " ;

                    }else{

                        $condition .= (Encrypt::isNumber($value) && $value!==0 ) || $value==='null' ? "{$key} {$symbol} {$value} " : "{$key} {$symbol} '{$value}' " ;
                    }

                }else if( $value === 'IS_NULL' ){

                    $condition .= $table ? " {$table}." : ' ';
                    $condition .= "{$key} IS NULL " ;

                }else{

                    $condition .= $table ? " $table." : ' ';
                    $condition .= !is_string($value) && (Encrypt::isNumber($value) && $value!==0 ) ? "{$key}={$value} " : "{$key}='{$value}' " ;

                }
                $count++;
            }
        }

        return $condition;
    }

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
     * 根据地理位置排序query生成 locationSort
     * @param    string|array             $location       定位 "lng,lat" | ["lng"=>1,"lat"=>1]
     * @param    string                   $field          对应数据库字段
     * @return   string                                   语句 Query String
     */
    public static function locationSort( $location, string $field = 'location' ): string
    {

        if (isset($location[0])&&isset($location[1])) {
            $location['lng']=$location[0];
            $location['lat']=$location[1];
        }

        $point = gettype($location)=='array' ? $location['lng'].','.$location['lat'] : $location;

        return " GLength(LineStringFromWKB(LineString($field, point($point)))) ASC ";
    }

    /**
     * 拼接CASE SET 用于批量更新 spliceCaseSet
     * @param array $kvArrayList [description]
     * @param string $key [description]
     * @return string [type]                                   [description]
     */
    public function spliceCaseSet( array $kvArrayList , string $key ): string
    {

        $query = "";
        $keys  = $this->getValidKeys( $kvArrayList );

        $c = 0;

        for ($i=0; $i < count($keys); $i++) {

            if( $keys[$i] != $key ){

                $query .= $c > 0 ? " , " : "";
                $query .= $keys[$i]. " = CASE $key ";

                for ($j=0; $j < count($kvArrayList) ; $j++) {

                    if(isset($kvArrayList[$j][$keys[$i]]) && isset($kvArrayList[$j][$key]) ){

                        $k = $kvArrayList[$j][$key];
                        $v = $kvArrayList[$j][$keys[$i]];

                        $query .= " WHEN ".(static::generateQueryValue($k))." THEN ";

                        if($keys[$i]=='location'){
                            $query .= static::generateLocationValue($v);
                        }else if($v==='SET_NULL'){
                            $query .= " NULL " ;
                        }else{
                            $query .= static::generateQueryValue($v);
                        }
                    }
                }
                $query .= " END ";

                $c ++;
            }
        }

        // 因为SQL 使用CASE WHEN THAN语句的时候 会对当前整个表进行更新  所以需要注意先强制做结果筛选 否则可能会污染数据库 或更新失败
        // 默认使用 WHERE KEY IN (1,2,3)的方式约束
        $query .= " WHERE $key IN (";

        for ($i=0; $i < count($kvArrayList) ; $i++) {

            if(isset($kvArrayList[$i][$keys[$i]]) && isset($kvArrayList[$i][$key]) ){

                $k = $kvArrayList[$i][$key];

                $query .= $i>0 ? "," : "";
                $query .= static::generateQueryValue($k);
            }
        }

        $query .= ") ";

        return $query;

    }

    /**
     * 获取特殊操作符号 getSymbol
     * @param    string                   $input          [输入]
     * @param    bool|boolean             $returnKey      [是否输出源符号]
     * @return   string                                   [操作符号]
     */
    private static function getSymbol( string $input, bool $returnKey = false ): string
    {

        $symbol = '';
        foreach (static::symbols as $key => $value) {
            $symbol = strstr($input, $key) ? ($returnKey?$key:$value) : $symbol ;
        }

        return $symbol;
    }

    /**
     * 检测是否含有操作符 hasSymbol
     * @param    string                   $input          [输入]
     * @return   bool                                  [是否]
     */
    private static function hasSymbol( string $input ):bool{
        $c = 0;
        foreach (static::symbols as $key => $value) {
            $c += strstr($input, $key) ? 1 : 0;
        }
        return $c>0;
    }

}
