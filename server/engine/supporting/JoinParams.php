<?php

namespace APS;

/**
 * 联合查询参数 - 联合
 * JoinParams
 * @package APS\core\ASDB
 *
 * @deprecated
 */
class JoinParams{

    /**
     * 模型类名称 Name of model class
     * @example \APS\Category
     * @var string
     */
    public $modelClass;

    /**
     * 数据库表 ( 默认是modelClass::$table )
     * Name of Join table ( use modelClass::$table by default )
     * @var string
     */
    public $table;

    /**
     * 查询索引字段
     * The key field name for JoinQuery
     * @var string
     */
    public $key;

    /**
     * 绑定其他表字段 ( 默认与$key相同 )
     * Field name equal to the key field ( equal to $key by default )
     * @var string | null
     */
    public $bind;

    /**
     * 求和字段 可选
     * Field name need sum calculate if needed
     * @var array
     */
    public $sum = [];

    /**
     * 求和字段显示名称
     * Display name of sum calculate
     * @mark default by sum_{$table}_{$sum}
     * @var array
     */
    public $sumAs = [];

    /**
     * 计数字段  可选
     * Field name need count if needed
     * @var array
     */
    public $count = [];

    /**
     * 计数字段显示名称
     * Display name of count field
     * @var array
     */
    public $countAs = [];

    /**
     * 合并字段  可选
     * Field name need Group Query if needed
     * @var array
     */
    public $group = [];

    /**
     * 合并字段 影响结果 可选
     * @var array
     */
    public $groupConditions = [];

    /**
     * 查询的结果字段
     * Fields for display
     * @var array | string
     */
    public $fields;

    /**
     * 过滤筛选( 不影响主表数据 )  可选
     * The filters for data of Join table if needed, it's not affect to final result
     * @var array | string | null
     */
    public $filters;

    /**
     * 条件筛选( 影响主表数据结果 ) 可选
     * The filters for data of Join table if need, it's affect to final result
     * @var array | string | null
     */
    public $conditions;

    /**
     * 别称 ( 数据导出为子集时使用 )
     * Alias ( Export table data as sub array of final result )
     * @var string | null
     */
    public $alias;

    /**
     * 单例
     * common
     * @param  string       $modelClass
     * @param  string|null  $key
     * @return $this
     */
    public static function init(string $modelClass, string $key = null ):JoinParams{
        return new JoinParams($modelClass,$key);
    }

    public function __construct( string $modelClass, string $key = null )
    {
        $this->modelClass = $modelClass;
        $this->table  = $modelClass::$table;
        $this->key    = $key ?? $modelClass::$primaryid;
        $this->fields = $modelClass::$overviewFields ?? $modelClass::$detailFields ?? '*';
    }

    /**
     * 选择表
     * at
     * @param  string  $table
     * @return $this
     */
    public function at( string $table ): JoinParams
    {
        $this->table = $table;
        return $this;
    }


    /**
     * 查询指定字段
     * get specific fields
     * @param  array | string  $fields
     * @return $this
     */
    public function get( $fields ): JoinParams
    {

        $this->fields = $fields;
        return $this;
    }

    /**
     * 绑定其他表字段
     * equalTo
     * @param  string  $tableAndField
     * @example user_account.groupid
     * @return $this
     */
    public function equalTo(string $tableAndField ): JoinParams
    {

        $this->bind = $tableAndField;
        return $this;
    }


    /**
     * 字段求和
     * sum field
     * @param  string       $field
     * @param  string|null  $as
     * @return $this
     */
    public function sum( string $field, string $as = null ): JoinParams
    {

        if( !in_array($field,$this->sum) ){
            $this->sum[] = $field;
            $this->sumAs[] = $as ?? "sum_{$this->table}_{$field}";
        }
        return $this;
    }


    /**
     * 统计字段总数( 行数 )
     * Count row at field
     * @param  string       $field
     * @param  string|null  $as
     * @return $this
     */
    public function count( string $field, string $as = null ): JoinParams
    {
        if( !in_array($field,$this->count) ){
            $this->count[] = $field;
            $this->countAs[] = $as ?? "count_{$this->table}_{$field}";
        }
        return $this;
    }

    /**
     * 合并字段
     * groupBy
     * @param $field
     * @return $this
     */
    public function groupBy( $field ): JoinParams
    {
        if( !in_array($field,$this->group) ){
            $this->group[] = $field;
        }
        return $this;
    }

    /**
     * 合并字段 影响结果
     * groupByInResult
     * @param $field
     * @return $this
     */
    public function groupInResult( $field ): JoinParams
    {
        if( !in_array($field,$this->groupConditions) ){
            $this->groupConditions[] = $field;
        }
        return $this;
    }


    public function withRowFilter( $filter ): JoinParams
    {
        $this->filters = $filter;
        return $this;
    }

    public function withResultFilter( $filter ): JoinParams
    {
        $this->conditions = $filter;
        return $this;
    }


    /**
     * 作为子集
     * asSubData
     * @param  string  $alias
     * @return JoinParams
     */
    public function asSubData( string $alias ):JoinParams{
        $this->alias = $alias;
        return $this;
    }

    /**
     * Convert to array type
     * @return array
     */
    public function toArray():array{

        return [
            'class'=>$this->modelClass,
            'table'=>$this->table,
            'key'=>$this->key,
            'fields'=>$this->fields
        ];
    }

    /**
     * 转换objectlist 为 array
     * Convert ObjectList to Array
     * @param JoinParams[] $paramList
     * @return array
     */
    public static function listToArrayList(array $paramList = null ):array{

        $arrayList = [];
        if( !isset($paramList) ){ return $arrayList; }
        foreach ( $paramList as $i => $p ){

            $arrayList = $p->toArray();
        }
        return $arrayList;
    }

}