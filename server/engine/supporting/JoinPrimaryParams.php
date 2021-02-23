<?php

namespace APS;

/**
 * 联合查询参数 - 主
 * JoinPrimaryParams
 * @package APS\core\ASDB
 */
class JoinPrimaryParams{

    /**
     * 模型类名称
     * @var string
     */
    public $modelClass;

    /**
     * 表
     * @var string
     */
    public $table;

    /**
     * 获取字段集合
     * List of fields needed
     * @var array | string
     */
    public $fields;

    /**
     * 主字段
     * @var string
     */
    public $key;

    /**
     * 筛选条件
     * @var array | string
     */
    public $conditions;

    /**
     * 按字段进行集合处理
     * GROUP BY field
     * @var array
     */
    public $group = [];

    /**
     * 对字段统计行数
     * Count row at field
     * @var array
     */
    public $count = [];

    /**
     * Description
     * @var array
     */
    public $countAs = [];

    /**
     * 对字段求和
     * Sum total at field
     * @var array
     */
    public $sum = [];

    /**
     * Description
     * @var array
     */
    public $sumAs = [];

    /**
     * 单例
     * common
     * @param  string       $modelClass
     * @param  string|null  $key
     * @return JoinPrimaryParams
     */
    public static function common( string $modelClass, string $key = null ):JoinPrimaryParams{
        return new JoinPrimaryParams($modelClass,$key);
    }

    public function __construct( string $modelClass , string $key = null ){
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
    public function at( string $table ): JoinPrimaryParams
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
    public function get( $fields ): JoinPrimaryParams
    {

        $this->fields = $fields;
        return $this;
    }


    /**
     * 字段求和
     * sum field
     * @param  string       $field
     * @param  string|null  $as
     * @return $this
     */
    public function sum( string $field, string $as = null ): JoinPrimaryParams
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
    public function count( string $field, string $as = null ): JoinPrimaryParams
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
    public function groupBy( $field ): JoinPrimaryParams
    {
        if( !in_array($field,$this->group) ){
            $this->group[] = $field;
        }
        return $this;
    }

    public function withResultFilter( array $filter = null ): JoinPrimaryParams
    {
        $this->conditions = $filter;
        return $this;
    }

    public function toArray():array{

        return [
            'class'=>$this->modelClass,
            'table'=>$this->table,
            'key'=>$this->key,
            'fields'=>$this->fields
        ];
    }
}
