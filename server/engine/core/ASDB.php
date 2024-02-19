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
     * @deprecated
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
            $conditions->orderBy('createtime', DBOrder_DESC);
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

        if (!$DB){ return $this->take($query)->error(550,mysqli_error($this->connect) ?? 'SQL Connect failed');}
        if ( mysqli_num_rows($DB) === 0 ){ return $this->take(false)->error(400,i18n('SYS_GET_NON')); }

        $re = mysqli_fetch_all($DB,true);

        for ( $i=0; $i<count($re); $i++ ){
            $joinParams->convertSubData($re[$i]);
        }

        return $this->take($re)->success(i18n('SYS_GET_SUC'));

    }


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

}
