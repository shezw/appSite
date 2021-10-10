<?php

namespace APS;

/**
 * 用于数据查询的字段集合
 * Class DBFields
 * @package APS
 */
class DBFields{

    private $table;

    /**
     * @var array [DBField]
     */
    private $list = [];

    /**
     * @var array [string]
     */
    private $index = [];

    public function __construct( string $table = NULL )
    {
        $this->table = $table;
    }

    public static function init( string $table = NULL ): DBFields
    {
        return new static( $table );
    }

    public static function allOf( string $table = NULL ): DBFields
    {
        return static::init( $table );
    }


    /**
     * 从数组快速建立
     * @param array[string] $fieldNameList
     * @param string|null $table
     * @return DBFields
     */
    public static function initBySimpleList( array $fieldNameList, string $table = NULL ): DBFields
    {
        $fields = new static( $table );
        for ( $i=0; $i<count($fieldNameList); $i++ ){
            $fields->add( DBField::init( $fieldNameList[$i], $table ) );
        }
        return $fields;
    }

    public function addFromList( array $fieldNameList, string $table = NULL ): DBFields
    {
        for ( $i=0; $i<count($fieldNameList); $i++ ){
            $this->add( DBField::init( $fieldNameList[$i], $table ) );
        }
        return $this;
    }

    private function last(): DBField{
        return $this->list[count($this->list)-1];
    }

    public function add( DBField $DBField ): DBFields
    {
        $this->list[] = $DBField;
        return $this;
    }

    public function and( string $field ): DBFields
    {
        if( isset($this->index[$field]) ){
            $this->list[$this->index[$field]] = DBField::init($field,$this->table );
        }else{
            $this->list[] = DBField::init($field,$this->table );
            $this->index[ $field ] = count($this->list) - 1;
        }
        return $this;
    }




    public function countLine(): DBFields
    {
        return $this->add( DBField::countLine($this->table ) );
    }

    /**
     * Glength LineStringFromWKB LineString
     * @param $lng
     * @param $lat
     * @return $this
     */
    public function distance( $lng, $lat ): DBFields
    {
        $this->last()->distance($lng,$lat );
        return $this;
    }

    public function named( string $alias ): DBFields
    {
        $this->last()->named($alias);
        return $this;
    }
    public function as( string $alias ): DBFields
    {
        return $this->named($alias );
    }


    /**
     * 去重查询
     * @param string $alias
     * @return $this
     */
    public function distinctAs( string $alias = NULL ): DBFields
    {
        $this->last()->distinctAs($alias);
        return $this;
    }
    public function distinct(): DBFields
    {
        return $this->distinctAs();
    }

    /**
     * 计数
     * @param string|null $alias
     * @return $this
     */
    public function countAs( string $alias = NULL ): DBFields
    {
        $this->last()->countAs($alias);
        return $this;
    }
    public function count():DBFields{
        return $this->countAs();
    }

    /**
     * 累加
     * @param string|null $alias
     * @return $this
     */
    public function sumAs( string $alias = NULL ): DBFields
    {
        $this->last()->sumAs($alias);
        return $this;
    }
    public function sum(): DBFields
    {
        return $this->sumAs();
    }


    /**
     * 平均数
     * @param string|null $alias
     * @return $this
     */
    public function avgAs( string $alias = NULL ): DBFields
    {
        $this->last()->avgAs($alias);
        return $this;
    }
    public function avg(): DBFields
    {
        return $this->avgAs();
    }





    public function export( bool $sub = false ): string
    {
        $query = "";

        if ( empty($this->list) ){
            $query .= $this->table ? " {$this->table}.* " : ' * ';
            return $query;
        }

        for ( $i=0; $i<count($this->list); $i++ )
        {
            $query .= $i>0 ? ', ' : '';
            if ( $sub ){
                $this->list[$i]->markAsSub();
            }
            $query .= $this->list[$i]->export();
        }
        return $query;
    }


    public function exportKeyList(): array
    {
        $list = [];

        if( empty($this->list) ){ return $list; }

        for ( $i=0; $i<count($this->list); $i++ )
        {
            $list[$this->list[$i]->fieldName()] = $this->list[$i]->exportKey();
        }
        return $list;
    }

    public function toArray(): array
    {
        $list = [];

        for ( $i=0; $i<count($this->list); $i++ )
        {
            $list[] = $this->list[$i]->export();
        }
        return $list;
    }


}