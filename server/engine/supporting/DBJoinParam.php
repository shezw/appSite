<?php

namespace APS;

class DBJoinParam{

    /**
     * @var DBFields 查询字段
     */
    private $fields;

    private $table;
    public  $class;

    /**
     * 表内过滤 (被过滤的数据在合并时会为空)
     * @var DBConditions
     */
    private $filters;

    /**
     * 全局过滤 (被过滤的数据将不会出现在结果中)
     * @var DBConditions
     */
    private $conditions;

    /**
     * 是否作为子数据
     * @var bool
     */
    private $sub = false;
    private $subAlias;

    private $key;
    private $bindWith;

    public function __construct( string $table, string $keyField, string $bindWith = NULL )
    {
        $this->table = $table;
        $this->key   = $keyField;
        $this->bindWith = $bindWith;
        $this->fields = DBFields::init($this->table);
    }

    public static function init( string $table, string $keyField, string $bindWith ): DBJoinParam
    {
        return new static( $table, $keyField, $bindWith );
    }

    /**
     * 主表条件
     * @param string $table
     * @param string $keyField
     * @return DBJoinParam
     */
    public static function primary( string $table, string $keyField ): DBJoinParam
    {
        return new static( $table, $keyField );
    }

    /**
     * 用于联表查询详情的 主表条件 快捷方式
     * @param string $ModelClass
     * @param string $uid
     * @return DBJoinParam
     */
    public static function convincePrimaryForDetail( string $ModelClass, string $uid ): DBJoinParam
    {
        $primaryJoin = static::primary( $ModelClass::table, $ModelClass::primaryid );
        $primaryJoin->class = $ModelClass;
        $primaryJoin->select($ModelClass::primaryid)->equal($uid )->limitWith();
        $primaryJoin->getDetail();

        return $primaryJoin;
    }

    /**
     * 用于联表查询列表的 主表条件 快捷方式
     * @param string $ModelClass
     * @param DBConditions $conditions
     * @return DBJoinParam
     */
    public static function convincePrimaryForList(string $ModelClass, DBConditions $conditions ): DBJoinParam
    {
        $primaryJoin = static::primary( $ModelClass::table, $ModelClass::primaryid );
        $primaryJoin->class = $ModelClass;
        $primaryJoin->conditions = $conditions;
        $primaryJoin->getList();
        return $primaryJoin;
    }

    /**
     * 用于联表查询的 JOIN表条件 快捷方式
     * @param string $ModelClass
     * @param string $bindTo
     * @return DBJoinParam
     */
    public static function convinceForJoin( string $ModelClass, string $bindTo, string $at ): DBJoinParam
    {
        $joinParam = static::init( $ModelClass::table, $ModelClass::primaryid, "{$at}.{$bindTo}" );
        $joinParam->class = $ModelClass;

        return $joinParam;
    }



    public function getWith( array $fields ): DBJoinParam
    {
        $this->get()->addFromList( $fields, $this->class::table );
        return $this;
    }

    /**
     * 查询详情字段 / 快捷
     * @return $this
     */
    public function getDetail(): DBJoinParam
    {
        return $this->getWith( $this->class::detailFields );
    }

    /**
     * 查询列表字段 / 快捷
     * @return $this
     */
    public function getList(): DBJoinParam
    {
        return $this->getWith( $this->class::listFields );
    }

    public function getOverview(): DBJoinParam
    {
        return $this->getWith( $this->class::overviewFields );
    }

    public static function convinceForDetail( string $ModelClass, string $bindTo, string $at ): DBJoinParam
    {
        return static::convinceForJoin($ModelClass,$bindTo, $at)->getDetail();
    }

    public static function convinceForList( string $ModelClass, string $bindTo, string $at ): DBJoinParam
    {
        return static::convinceForJoin($ModelClass,$bindTo, $at)->getList();
    }


    /**
     * 主(关联)字段
     * @param string $field
     * @return $this
     */
    public function bind( string $field ): DBJoinParam
    {
        $this->key = $field;
        return $this;
    }

    /**
     * 目标字段
     * @param string $target
     * @return $this
     */
    public function to( string $target ): DBJoinParam
    {
        $this->bindWith = $target;
        return $this;
    }

    /**
     * 表内筛选
     * @param string|null $field
     * @return DBConditions
     */
    public function filter( string $field = NULL ): DBConditions
    {
        $this->filters = DBConditions::init($this->table);
        return $field ? $this->filters->where($field) : $this->filters;
    }

    /**
     * 全局筛选
     * @param string|null $field
     * @return DBConditions
     */
    public function select( string $field = NULL ): DBConditions
    {
        $this->conditions = DBConditions::init( $this->table );
        return $field ? $this->conditions->where($field) : $this->conditions;
    }

    /**
     * 获取条件筛选对象
     * @param string|null $field
     * @return DBConditions
     */
    public function condition( string $field = NULL ): DBConditions
    {
        return $this->select($field);
    }

    /**
     * 获取查询对象
     * @param string|null $field
     * @return DBFields
     */
    public function get( string $field = NULL ): DBFields
    {
        return $field ? $this->fields->and($field) : $this->fields;
    }

    /**
     * 作为子数据集
     * @param string|null $alias
     * @return $this
     */
    public function asSub( string $alias = NULL ): DBJoinParam
    {
        $this->sub = true;
        $this->subAlias = $alias ?? $this->table;
        return $this;
    }

    /**
     * 获取 子数据 (别称)组名
     * @return string
     */
    public function subAlias(): string
    {
        return $this->subAlias;
    }

    /**
     * 是否子数据
     * @return bool
     */
    public function isSub(): bool
    {
        return $this->sub;
    }

    public function toArray(): array
    {
        return [
            'table'=>$this->table,
            'fields'=>$this->fields->toArray(),
            'key'=>$this->key,
            'bindWith'=>$this->bindWith,
            'filters'=> isset($this->filters) ? $this->filters->toArray() : null ,
            'conditions'=>isset($this->conditions) ? $this->conditions->toArray() : null
        ];
    }

    /**
     * 全部查询字段
     * @return string
     */
    public function exportFields(): string{

        return $this->fields->export( $this->sub );
    }

    /**
     * 输出 联合表
     * @param bool $ignoreFilters 忽略行内过滤
     * @return string
     */
    public function exportJoin( bool $ignoreFilters = false ): string
    {
        $query = "LEFT JOIN {$this->table} ON {$this->bindWith} = {$this->table}.{$this->key} ";
        if ( $this->filters instanceof DBConditions && !$ignoreFilters ){

            $query .= $this->filters->export(true);
        }
        return $query;
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * 是否有条件筛选
     * @return bool
     */
    public function hasCondition(): bool
    {
        return $this->conditions && !$this->conditions->isEmpty();
    }

    /**
     * 是否有表内过滤
     * @return bool
     */
    public function hasFilter(): bool
    {
        return $this->filters && !$this->filters->isEmpty();
    }

    /**
     * @param bool $isAdding 是否使用AND
     * @return string
     */
    public function exportCondition( bool $isAdding = false ): string
    {
        return $this->conditions->exportCondition($isAdding);
    }

    /**
     * Restrict Operation:   GROUP BY, LIMIT, ORDER BY
     * @return string
     */
    public function exportRestrict(): string
    {
        return $this->conditions->exportRestrict();
    }

    /**
     * 将并列数据转换为 子数组形式
     * @param array $data
     */
    public function convertSubData( array &$data )
    {
        $keyList = $this->fields->exportKeyList();
        $keyDict = [];

        foreach ( $keyList as $field => $key ){

            $keyDict[ $field ] = $data[$key];

            unset( $data[$key] );
        }
        $data[$this->subAlias] = $keyDict;
    }
}