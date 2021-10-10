<?php

namespace APS;

/**
 * 用于更新/存储数据时的字段/值设定
 * Class DBValues
 * @package APS
 */
class DBValues{

    /**
     * @var array[DBValue]
     */
    private $list;
    private $index = [];

    private $pointer = 0;

    public static function init( string $field ): DBValues {
        $values = new static();
        return $values->set( $field );
    }

    public function has( string $field ): bool
    {
        return isset( $this->index[$field] );
    }

    private function current(): DBValue{
        return $this->list[$this->pointer];
    }

    private function removeLast(): DBValues
    {
        $field = $this->current()->key();
        unset($this->index[$field]);
        array_splice($this->list,count($this->list)-1 );
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function set( string $field ): DBValues
    {
        if( isset($this->index[$field]) ){
            $this->pointer = $this->index[$field];
        }else{
            $this->list[] = DBValue::init( $field );
            $this->pointer = count($this->list) - 1;
            $this->index[ $field ] = $this->pointer;
        }
        return $this;
    }

    public function add( DBValue $DBValue ):DBValues
    {
        if( isset($this->index[$DBValue->key()]) ){
            $this->list[$this->index[$DBValue->key()]] = $DBValue;
            $this->pointer = $this->index[$DBValue->key()];
        }else{
            $this->list[] = $DBValue;
            $this->pointer = count($this->list) - 1;
            $this->index[ $DBValue->key() ] = $this->pointer;
        }
        return $this;
    }

    /**
     * 存储为NULL
     * @return DBValues
     */
    public function null(): DBValues{

        $this->current()->setNull();
        return $this;
    }


    /**
     * 存储为布尔值 （数据库中转化为0,1）
     * @param bool $boolean
     * @return DBValues
     */
    public function bool( bool $boolean ): DBValues
    {
        $this->current()->bool( $boolean );
        return $this;
    }

    /**
     * @param double $number
     * @return DBValues
     */
    public function number( $number ): DBValues
    {
        $this->current()->equal($number);
        return $this;
    }
    public function numberIf( $number = NULL ):DBValues{
        if ( isset($number) ){
            $this->current()->equal($number);
        }else{
            $this->removeLast();
        }
        return $this;
    }

    /**
     * 存储为JSON序列
     * @param array $json
     * @return DBValues
     */
    public function json( array $json ): DBValues
    {
        $this->current()->json( $json );
        return $this;
    }
    public function jsonIf( array $json = NULL ):DBValues{
        if ( isset($json) ){
            $this->current()->json($json);
        }else{
            $this->removeLast();
        }
        return $this;
    }

    /**
     * @param $ASJson
     * @return DBValues
     */
    public function ASJson( $ASJson ): DBValues
    {
        $this->current()->ASJson( $ASJson );
        return $this;
    }

    /**
     * @param float $lng
     * @param float $lat
     * @param bool $autoFill     自动补充 （true自动补充 lng,lat两个字段）
     * @return $this
     */
    public function location( float $lng, float $lat, bool $autoFill = false ): DBValues
    {
        $this->current()->location( $lng, $lat, $autoFill );
        return $this;
    }

    /**
     * 自动完成 GEO值 ,lng字段 float值, lat字段 float值
     * @param float $lng
     * @param float $lat
     * @return $this
     */
    public function locations( float $lng, float $lat ): DBValues{

        return $this->location( $lng, $lat, true );
    }

    /**
     * @param string $string
     * @return $this
     */
    public function string( string $string ): DBValues
    {
        $this->current()->string( $string );
        return $this;
    }
    public function stringIf( string $string = NULL ):DBValues{
        if ( isset($string) ){
            $this->current()->string($string);
        }else{
            $this->removeLast();
        }
        return $this;
    }

    /**
     * 富文本内容
     * @param string $richText
     * @return $this
     */
    public function richText( string $richText ): DBValues
    {
        $this->current()->richText($richText);
        return $this;
    }


    public function length(): int{

        return count($this->list);
    }

    public function keys(): string{

        $query = '';

        for ( $i=0; $i<count($this->list); $i++ ){

            if ( $i > 0 ){
                $query .= " , ";
            }

            $query .= $this->list[$i]->key();
        }

        return $query;
    }

    public function hasKeyIn( array $list ): bool
    {
        foreach ($this->index as $key => $V) {

            if ( in_array($key,$list, true) ){
                return true;
            }
        }
        return false;
    }


    public function getValue(string $field )
    {
        return $this->list[$this->index[$field]]->value();
    }

    /**
     * 净化字段，移除不合法字段
     * @param array $validFields
     * @return $this
     */
    public function purify( array $validFields ): DBValues
    {
        $list = [];

        foreach ( $this->list as $i => $field ) {
            if ( in_array( $field->key(), $validFields, true ) ){
                $list[] = $this->list[$i];
                $this->index[$field->key()] = count($list)-1;
            }else{
                unset($this->index[$field->key()]);
                $this->pointer = count($list) - 1;
            }
        }
        $this->list = $list;
        return $this;
    }

    public function purifyCopy( array $validFields ): DBValues
    {
        $DBValues = new static();

        foreach ( $this->list as $i => $field ) {
            if ( in_array( $field->key(), $validFields, true ) ){
//                $DBValues->add($field->copy());
                $DBValues->add($field);
            }
        }
        return $DBValues;
    }


    public function export( bool $onlyValue = false ): string{

        $query = $onlyValue ? ' (' : "";

        for ( $i=0; $i<count($this->list); $i++ ){

            if ( $i > 0 ){
                $query .= " , ";
            }

            $query .= $this->list[$i]->export( $onlyValue );
        }

        $query .= $onlyValue ? ')' : '';
        return $query;
    }

    public function toArray(): array
    {
        $array = [];

        for ( $i=0; $i<count($this->list); $i++ ){

            $array[$this->list[$i]->key()] = $this->list[$i]->export( true );
        }
        return $array;
    }

}