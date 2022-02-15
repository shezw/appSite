<?php


namespace APS;

/**
 * 用于筛选数据内容的条件组合
 * Class DBConditions
 * @package APS
 */
class DBConditions
{

    /**
     * @var array[DBFilter] $list
     */
    private $list = [];
    private $table;

    private $order;
    private $order_orientation;
    private $order_type;

    private $limit = -1;
    private $limit_length = -1;

    private $group;

    public function __construct( string $table = NULL )
    {
        if( isset($table) ){
            $this->table = $table;
        }
    }

    /**
     * @param string|null $table
     * @return DBConditions
     */
    public static function init( string $table = NULL ): DBConditions
    {
        return new static( $table );
    }

    private function last():DBFilter{

        return $this->list[ count($this->list) - 1 ];
    }

    private function removeLast(): DBConditions
    {
        array_splice($this->list,count($this->list)-1 );

        return $this;
    }

    public function groupBy( string $field ): DBConditions
    {
        $this->group = $field;
        return $this;
    }

    public function add( DBFilter $filter ): DBConditions
    {
        $this->list[] = $filter;
        return $this;
    }

    /**
     * WHERE查询 等同于AND查询
     * @param string $field
     * @return $this
     */
    public function where( string $field ): DBConditions{
        return $this->and( $field );
    }

    /**
     * 增加AND 交集查询
     * @param string $field
     * @return DBConditions
     */
    public function and( string $field ): DBConditions
    {
        $DBField = DBFilter::init($field, DBFilter::AND_MODE );

        $this->list[] = $DBField;
        return $this;
    }

    /**
     * 增加OR 并集查询
     * @param string $field
     * @return DBConditions
     */
    public function or( string $field ): DBConditions{

        $DBField = DBFilter::init($field, DBFilter::OR_MODE );

        $this->list[] = $DBField;
        return $this;
    }


    public function isOrdered(): bool{
        return !!($this->order);
    }

    /**
     * 限定查询排序方式
     * @param string $field
     * @param string $DBConditions_ORDER
     * @return $this
     */
    public function orderBy( string $field, string $DBConditions_ORDER = DBOrder_DESC ): DBConditions
    {
        $this->order = $field;
        $this->order_orientation = $DBConditions_ORDER;

        return $this;
    }

    public function orderWith( string $fullOrderQuery ): DBConditions
    {
        $this->order = $fullOrderQuery;
        $this->order_type = 'alias';

        return $this;
    }


    /**
     * 根据地理位置排序query生成 locationSort
     * @param $lng
     * @param $lat
     * @param string $field 对应数据库字段
     * @param string $orientation
     * @return DBConditions 语句 Query String
     */
    public function orderByDistance( $lng, $lat, string $field = 'location', string $orientation = DBOrder_ASC ): DBConditions
    {

        $this->order =  floatval(_ASDB()->getVersion()) >= 8
            ? " GLength(LineStringFromWKB(LineString(". ( $this->table ? "{$this->table}." : "" ) ."{$field}, point({$lng},{$lat})))) "
            : " GLength(ST_LineStringFromWKB(LineString(". ( $this->table ? "{$this->table}." : "" ) ."{$field}, point({$lat},{$lng})))) "
        ;
        $this->order_orientation = $orientation;
        $this->order_type = 'alias';

        return $this;
    }

    /** 根据查询中的地理距离排序，需要查询字段中已经进行计算
     * @param string $alias
     * @param string $orientation
     * @return DBConditions
     */
    public function orderByAlias( string $alias, string $orientation = DBOrder_ASC ): DBConditions
    {
        $this->order = $alias;
        $this->order_orientation = $orientation;
        $this->order_type = 'alias';
        return $this;
    }


    /**
     * 限制查询数量
     * @param int $startAt
     * @param int $length
     * @return $this
     */
    public function limitWith( int $startAt = 0, int $length = 1 ): DBConditions
    {

        $this->limit = $startAt;
        $this->limit_length = $length;

        return $this;
    }

    /**
     * 通过多组特定关键词查找
     * @param array[DBConditionKeyword] $keywords
     * @return DBConditions
     */
    public function searchWith( array $keywords ): DBConditions
    {
        $this->last()->searchWith($keywords);
        return $this;
    }

    /**
     * 通过单一字段关键词查找
     * @param string $keyword
     * @return DBConditions
     */
    public function search( string $keyword ): DBConditions
    {
        $this->last()->search($keyword);
        return $this;
    }


    /**
     * 查找两个数字之间的
     * @param $from
     * @param $to
     * @return DBConditions
     */
    public function between( $from, $to ): DBConditions
    {
        $this->last()->between( $from, $to );
        return $this;
    }

    /**
     * 使用IN查询，查找属于列表中任意元素的结果
     * @param array $elements
     * @return DBConditions
     */
    public function belongTo( array $elements ): DBConditions
    {
        $this->last()->belongTo( $elements );
        return $this;
    }

    /**
     * 使用OR查询，查找匹配任意情况的结果 支持 DBCondition子查询
     * field = 1 OR field = 2
     * field = 0 OR other > 100
     * @param array $conditionList
     * @return DBConditions
     */
    public function each( array $conditionList ): DBConditions
    {
        $this->last()->each( $conditionList );
        return $this;
    }

    /**
     * 匹配空值结果
     */
    public function isNull(): DBConditions
    {
        $this->last()->isNull( );
        return $this;
    }

    /**
     * 匹配非空结果
     */
    public function isNotNull(): DBConditions
    {
        $this->last()->isNotNull( );
        return $this;
    }

    /**
     * 等于 =
     * @param number|string $value
     * @return DBConditions
     */
    public function equal( $value = NULL ): DBConditions
    {
        $this->last()->equal($value);
        return $this;
    }
    public function equalIf( $value = NULL ): DBConditions
    {
        return isset($value) ? $this->equal($value) : $this->removeLast();
    }

    public function bool( bool $trueOrFalse ): DBConditions
    {
        $this->last()->bool($trueOrFalse);
        return $this;
    }


    /**
     * 不等于 !=
     * @param number|string $value
     * @return DBConditions
     */
    public function notEqual( $value ): DBConditions
    {
        $this->last()->notEqual( $value );
        return $this;
    }

    /**
     * 小于 <
     * @param number $value
     * @return DBConditions
     */
    public function less( $value ): DBConditions
    {
        $this->last()->less( $value );
        return $this;
    }

    /**
     * 小于等于 <=
     * @param number $value
     * @return DBConditions
     */
    public function lessAnd( $value ): DBConditions
    {
        $this->last()->lessAnd( $value );
        return $this;
    }

    /**
     * 大于 >
     * @param number $value
     * @return DBConditions
     */
    public function bigger( $value ): DBConditions
    {
        $this->last()->bigger( $value );
        return $this;
    }

    /**
     * 大于等于 >=
     * @param number $value
     * @return DBConditions
     */
    public function biggerAnd( $value ): DBConditions
    {
        $this->last()->biggerAnd( $value );
        return $this;
    }


    /**
     * 净化字段，移除不合法字段
     * @param array $validKeys
     * @return $this
     */
    public function purify( array $validKeys ): DBConditions
    {
        $list = [];

        foreach ( $this->list as $i => $DBFilter ) {
            if ( in_array( $DBFilter->key(), $validKeys, true ) ){
                $list[] = $this->list[$i];
            }
        }
        $this->list = $list;
        return $this;
    }

    public function purifyCopy( array $validKeys ): DBConditions
    {
        $DBConditions = new static();

        foreach ( $this->list as $i => $DBFilter ) {
            if ( in_array( $DBFilter->key(), $validKeys, true ) ){

                $DBConditions->add($this->list[$i]);
            }
        }
        return $DBConditions;
    }


    public function exportCondition(bool $isAdding = false ): string
    {
        $conditions = '';

        if( !empty( $this->list ) ){

            $this->list[0]->setMode( $isAdding ? DBFilter::AND_MODE : DBFilter::WHERE_MODE);

            for ( $i=0; $i<count($this->list); $i++ ){

                $conditions .= $this->list[$i]->export( $this->table );
            }
        }
        return $conditions;
    }

    public function isEmpty(): bool
    {
        return empty($this->list);
    }

    public function exportRestrict(): string
    {
        $conditions = '';

        if( $this->group ){

            $conditions .= " GROUP BY ";
            $conditions .= $this->table ? " {$this->table}." : ' ';
            $conditions .= "{$this->group} ";
        }

        if( $this->order > -1 ){

            $conditions .= " ORDER BY ";
            if( $this->order_type !== 'alias' ){
                $conditions .= $this->table ? " {$this->table}." : ' ';
            }
            $conditions .= "{$this->order} {$this->order_orientation} ";
        }

        if( $this->limit > -1 ){

            $conditions .= " LIMIT {$this->limit},{$this->limit_length} ";
        }

        return $conditions;
    }


    /**
     * 输出QUERY语句
     * @param bool $isAdding    是否追加模式
     * @return string
     */
    public function export( bool $isAdding = false ):string{

        $conditions = $this->exportCondition($isAdding);
        $conditions.= $this->exportRestrict();

        return $conditions;
    }

    private function listToArray(): array{
        $list = [];
        foreach ( $this->list as $i => $condition ){

            $list[] = $condition->toArray();
        }
        return $list;
    }

    public function toArray(  ): array
    {
        return [
            'list'=>$this->listToArray(),
            'table'=>$this->table,
            'order'=>[$this->order,$this->order_orientation,$this->order_type],
            'limit'=>[$this->limit,$this->limit_length],
            'group'=>$this->group
        ];
    }
}