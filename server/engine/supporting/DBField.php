<?php


namespace APS;

/**
 * 用于数据查询的字段集合
 * Class DBField
 * @package APS
 */
class DBField
{
    public  $name;

    private $table;

    /** @var string 去重 */
    private $distinctAs;

    /** @var string 别称 */
    private $alias;

    /** @var string 计数 */
    private $countAs;

    /** @var string 总和 */
    private $sumAs;

    /** @var string 平均数 */
    private $avgAs;

    /** @var string query计算 */
    private $caculate;

    public function __construct( string $name, string $table = NULL  )
    {
        $this->name = $name;
        $this->table = $table;
    }

    public static function init( string $name, string $table = NULL ): DBField
    {
        return new static( $name, $table );
    }

    public static function countLine( string $table = NULL ): DBField
    {
        return static::init( 'id', $table )->countAs( $table ? "count_{$table}" : 'count');
    }

    public function distance( $lng, $lat ): DBField
    {
        $this->caculate = " GLength(LineStringFromWKB(LineString(". ( $this->table ? "{$this->table}." : "" ) ."{$this->name}, point({$lng},{$lat})))) ";
        return $this;
    }

    public function named( string $alias ): DBField
    {
        $this->alias = $alias;
        return $this;
    }
    public function as( string $alias ): DBField
    {
        return $this->named( $alias );
    }

    public function markAsSub(): DBField
    {
        return $this->as( $this->alias ?? "{$this->table}\${$this->name}" );
    }


    /**
     * 去重查询
     * @param string $alias
     * @return $this
     */
    public function distinctAs( string $alias = NULL ): DBField
    {
        $this->distinctAs = $alias ?? ( $this->table ? "{$this->table}_{$this->name}" : $this->name);
        return $this;
    }
    public function distinct(): DBField
    {
        return $this->distinctAs();
    }

    /**
     * 计数
     * @param string|null $alias
     * @return DBField
     */
    public function countAs( string $alias = NULL ): DBField
    {
        $this->countAs = $alias ?? ( $this->table ? "count_{$this->table}_{$this->name}" : "count_{$this->name}");
        return $this;
    }
    public function count():DBField{
        return $this->countAs();
    }

    /**
     * 累加
     * @param string|null $alias
     * @return $this
     */
    public function sumAs( string $alias = NULL ): DBField
    {
        $this->sumAs = $alias ?? ( $this->table ? "sum_{$this->table}_{$this->name}" : "sum_{$this->name}");
        return $this;
    }
    public function sum(): DBField
    {
        return $this->sumAs();
    }

    public function avgAs( string $alias = NULL ): DBField
    {
        $this->avgAs = $alias ?? ( $this->table ? "avg_{$this->table}_{$this->name}" : "avg_{$this->name}");
        return $this;
    }
    public function avg(): DBField
    {
        return $this->avgAs();
    }

    public function hasAlias(): bool
    {
        return !!$this->alias;
    }

    public function export(): string
    {
        $field  = $this->table ? "{$this->table}.`{$this->name}`" : "`{$this->name}`";

        $query  = $field;
        if( $this->caculate ){
            $query = $this->caculate;
        }

        $query .= $this->alias ? " AS {$this->alias} " : '';

        if( $this->distinctAs ){
            $query = " DISTINCT( {$field} ) AS {$this->distinctAs}";
        }

        if( $this->sumAs ){
            $query .= ", SUM( $field ) AS {$this->sumAs} ";
        }
        if( $this->avgAs ){
            $query .= ", AVG( $field ) AS {$this->avgAs} ";
        }
        if( $this->countAs ){
            $query .= ", COUNT( $field ) AS {$this->countAs} ";
        }

        return $query;
    }

    public function exportKey(): string
    {
        $field  = $this->table ? "{$this->table}.{$this->name}" : $this->name;

        $query  = $field;
        if( $this->caculate ){
            $query = $this->caculate;
        }

        if( $this->distinctAs ){
            $query = "{$this->distinctAs}";
        }

        if( $this->alias ){
            $query = "{$this->alias}";
        }

        return $query;
    }

    public function fieldName():string
    {
        return $this->name;
    }
}