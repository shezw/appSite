<?php


namespace APS;

/**
 * 用于筛选数据内容的条件
 * Class DBCondition
 * @package APS
 */
class DBFilter
{
    private $table;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string DBFilterSymbol_Enum     查询模式
     */
    private $symbol = ' = ';

    private $mode = ' AND ';

    const AND_MODE = ' AND ';
    const OR_MODE  = ' OR ';
    const WHERE_MODE = ' WHERE ';

    /**
     * @var mixed|null $value 指定值
     *      NULL,IS NULL, IS NOT NULL 查询时无需指定值
     *      KEYWORD 查询需要传入 DBConditionKeyword
     *      BETWEEN 查询需要传入 '0,1'的字符串
     *      IN 查询需要传入 [1,2,3,4] 的数组
     *      OR 查询需要传入 ['a','b','c'] 的数组 或 [DBCondition,DBCondition] 二级查询数组
     */
    private $value;

    /**
     * DBCondition constructor.
     * @param string $field 字段
     * @param string $mode
     */
    public function __construct(string $field, $mode = ' WHERE ')
    {
        $this->field = $field;
        $this->mode = $mode;

        return $this;
    }

    public static function init(string $field, $mode = ' WHERE '): DBFilter
    {
        return new static( $field, $mode );
    }

    public function key(): string
    {
        return $this->field;
    }

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode( string $mode ): DBFilter
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * 通过多组特定关键词查找
     * @param array[DBConditionKeyword] $keywords
     * @return DBFilter
     */
    public function searchWith( array $keywords ): DBFilter
    {
        $this->symbol = DBFilterSymbol_Keywords;
        $this->value = $keywords;
        return $this;
    }

    /**
     * 通过单一字段关键词查找
     * @param string $keyword
     * @return DBFilter
     */
    public function search( string $keyword ): DBFilter
    {
        $this->symbol = DBFilterSymbol_Keyword;
        $this->value = $keyword;
        return $this;
    }

    /**
     * 查找两个数字之间的
     * @param $from
     * @param $to
     * @return DBFilter
     */
    public function between( $from, $to ): DBFilter
    {
        $this->symbol = DBFilterSymbol_Between;
        $this->value = "{$from},{$to}";
        return $this;
    }

    /**
     * 使用IN查询，查找属于列表中任意元素的结果
     * @param array $elements
     * @return DBFilter
     */
    public function belongTo( array $elements ): DBFilter
    {
        $this->symbol = DBFilterSymbol_In;
        $this->value = $elements;
        return $this;
    }

    /**
     * 使用OR查询，查找匹配任意情况的结果 支持 DBCondition子查询
     * field = 1 OR field = 2
     * field = 0 OR other > 100
     * @param array $conditionList
     * @return DBFilter
     */
    public function each( array $conditionList ): DBFilter
    {
        $this->symbol = DBFilterSymbol_Or;
        $this->value = $conditionList;
        return $this;
    }

    /**
     * 匹配真假
     * @param bool $trueOrFalse
     * @return $this
     */
    public function bool( bool $trueOrFalse ): DBFilter
    {
        $this->symbol = DBFilterSymbol_Equal;
        $this->value  = $trueOrFalse ? 1 : 0;
        return $this;
    }

    /**
     * 匹配空值结果
     */
    public function isNull(): DBFilter
    {
        $this->symbol = DBFilterSymbol_Null;
        return $this;
    }

    /**
     * 匹配非空结果
     */
    public function isNotNull(): DBFilter
    {
        $this->symbol = DBFilterSymbol_NotNull;
        return $this;
    }

    /**
     * @param $value
     * @param $mode
     * @return $this
     */
    private function compareWith( $value, $mode ): DBFilter
    {
        $this->symbol = $mode;
        $this->value = $value;

        return $this;
    }

    /**
     * 等于 =
     * @param number|string $value
     * @return DBFilter
     */
    public function equal( $value ): DBFilter
    {
        return $this->compareWith( $value, DBFilterSymbol_Equal );
    }

    /**
     * 不等于 !=
     * @param number|string $value
     * @return DBFilter
     */
    public function notEqual( $value ): DBFilter
    {
        return $this->compareWith( $value, DBFilterSymbol_NotEqual );
    }

    /**
     * 小于 <
     * @param number $value
     * @return DBFilter
     */
    public function less( $value ): DBFilter
    {
        return $this->compareWith( $value, DBFilterSymbol_Less );
    }

    /**
     * 小于等于 <=
     * @param number $value
     * @return DBFilter
     */
    public function lessAnd( $value ): DBFilter
    {
        return $this->compareWith( $value, DBFilterSymbol_LessAnd );
    }

    /**
     * 大于 >
     * @param number $value
     * @return DBFilter
     */
    public function bigger( $value ): DBFilter
    {
        return $this->compareWith( $value, DBFilterSymbol_Bigger );
    }

    /**
     * 大于等于 >=
     * @param number $value
     * @return DBFilter
     */
    public function biggerAnd( $value ): DBFilter
    {
        return $this->compareWith( $value, DBFilterSymbol_BiggerAnd );
    }

    /**
     * 生成赋值语句
     * generateQueryValue
     * @param    string|number      $input          输入
     * @return   string                                   赋值语句 Set value query string
     */
    private static function generateQueryValue( $input ): string
    {
        $output = $input;
        if( Encrypt::isNumber( $input ) ){

            $output = " {$input} ";

        }else if(gettype($input)=='string'){

            $output = " '{$input}' ";
        }
        return $output;
    }

    /**
     * 输出字段处理结果
     * @param string|null $table
     * @return string
     */
    public function export( string $table = NULL ):string{

        if( isset($table) ){
            $this->table = $table;
        }

        $condition = $this->mode;

        if( !isset($this->value) ){return "";}

        $this->value = Filter::addslashesAll($this->value);

        switch ( $this->symbol ){

            case DBFilterSymbol_Keywords :

                $condition .= ' (';

                for ( $i=0; $i<count($this->value); $i++ ){

                    $value = $this->value[$i]->value;
                    $field = $this->value[$i]->field;

                    $condition .= $i>0 ? ' OR ' : '';
                    $condition .=' MATCH (';
                    $condition .= $this->table ? " `{$this->table}`." : ' ';
                    $condition .= "`{$field}`";
                    $condition .= ") AGAINST ('{$value}' IN NATURAL LANGUAGE MODE) ";

                }
                $condition .= ') ';

                break;

            case DBFilterSymbol_Keyword :

                $condition .= ' MATCH( ';
                $condition .= $this->table ? " `{$this->table}`." : ' ';
                $condition .= "`{$this->field}`";
                $condition .= ") AGAINST ('{$this->value}' IN NATURAL LANGUAGE MODE) ";

            break;

            case DBFilterSymbol_In :

                $V = '';
                for ($i=0; $i < count($this->value); $i++) {

                    $V .= $i==0 ? '' : ',';
                    $V .= Encrypt::isNumber($this->value[$i]) ? $this->value[$i] : "'{$this->value[$i]}'";

                }
                $condition .= $this->table ? " `{$this->table}`." : ' ';
                $condition .= "`{$this->field}` IN ({$V}) " ;

                break;

            case DBFilterSymbol_Between :
                $value = str_replace(",", " AND ", $this->value);
                $condition .= $this->table ? " `{$this->table}`." : ' ';
                $condition .= "`{$this->field}`".$this->symbol.$value;

                break;

            case DBFilterSymbol_Less:
            case DBFilterSymbol_LessAnd:
            case DBFilterSymbol_NotEqual:
            case DBFilterSymbol_Bigger:
            case DBFilterSymbol_BiggerAnd:

            $condition .= $this->table ? " `{$this->table}`." : ' ';
            $condition .=  "`{$this->field}` {$this->symbol} {$this->value} " ;
            break;

            case DBFilterSymbol_Match:

                $condition .= $this->table ? " `{$this->table}`." : ' ';
                $condition .= "`{$this->field}` {$this->field} ";

                break;

            case DBFilterSymbol_Or:

                $condition .= " (";
                for ($i=0; $i <count($this->value) ; $i++) {

                    $condition .= ($i!==0?' OR ':'');
                    $condition .= $this->table ? " `{$this->table}`." : ' ';
                    $condition .= "`{$this->field}`";

                    if( $this->value[$i] instanceof DBFilter ) {

                        $condition .= $this->value[$i]->export();
                    }else {
                        $condition.= "=";
                        $condition .= static::generateQueryValue($this->value[$i]);
                    }
                }
                $condition .= ") ";

                break;

            case ' IS ':
            case DBFilterSymbol_Equal:
            case ' IS NOT ':
            $condition .= $this->table ? " `{$this->table}`." : ' ';
            $condition .=  "`{$this->field}` {$this->symbol}" ;
            $condition .=  static::generateQueryValue( $this->value );

                break;

            case DBFilterSymbol_Null:
            case DBFilterSymbol_NotNull:
            $condition .= $this->table ? " `{$this->table}`." : ' ';
            $condition .=  "`{$this->field}` {$this->symbol}" ;

                break;

        }
        return $condition;
    }

    public function toArray(): array
    {
        return [
            'table'=>$this->table,
            'field'=>$this->field,
            'value'=>[$this->symbol,$this->mode,$this->value]
        ];
    }
}