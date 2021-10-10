<?php

namespace APS;

/**
 * 关键词搜索查询定义
 * Class DBConditionKeyword
 * @package APS
 */
class DBConditionKeyword{

    public $field  = 'title';
    public $value = '';

    public function __construct( $field, $value )
    {
        $this->field = $field;
        $this->value = $value;
    }

    public static function init( $field, $search ): DBConditionKeyword
    {
        return new static( $field, $search );
    }
}
