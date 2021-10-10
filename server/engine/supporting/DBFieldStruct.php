<?php

namespace APS;


class DBFieldStruct{

    public  $name;
    private $comment;

    private $type = DBField_Int;

    private $default;
    private $nullable = false;

    private $length;

    private $index;

    public function __construct( string $name, int $DBFiled_, float $length = -1, $default = NULL, $comment = NULL )
    {
        $this->name = $name;
        $this->type = $DBFiled_;

        $this->length = $length;
        $this->default = $default;
        $this->comment = $comment;
    }

    public static function init( string $name, int $DBType, float $length = -1, $default = NULL, $comment = NULL ): DBFieldStruct{

        return new static( $name, $DBType, $length, $default, $comment );
    }


    /** 主键字段（自增INT） */
    public static function primary( string $named ): DBFieldStruct
    {
        return static::init( $named, DBField_Int, 15, NULL, 'Index ID' )->index(DBIndex_Primary);
    }

    /** UID字段 */
    public static function uid( string $named ): DBFieldStruct
    {
        return static::init( $named, DBField_String, 8, NULL, 'UID' );
    }

    /** 布尔值字段 */
    public static function boolean( string $named, $default = false, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_Boolean, 1, ($default ? 1 : 0), $comment );
    }

    public static function int( string $named, $length, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_Int, $length, $default, $comment );
    }

    /** 时间戳字段 */
    public static function time(string $named, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_TimeStamp, 13, $default, $comment );
    }

    /** 浮点数字段 */
    public static function float( string $named, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_Float, -1, $default, $comment );
    }

    /** 双精度 浮点数字段 */
    public static function double( string $named, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_Double, -1, $default, $comment );
    }

    /** 精确数字段 用于价格等保留位数字段 */
    public static function decimal( string $named, int $lenL, int $lenR, $default = NULL, $comment = NULL ): DBFieldStruct
    {
        $FieldStruct = static::init( $named, DBField_Decimal, -1, $default, $comment );
        return $FieldStruct->setDecimalLength($lenL,$lenR);
    }

    /** 字符串字段 */
    public static function varchar( string $named, int $length = 8, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_String, $length, $default, $comment );
    }

    /** 文本字段 */
    public static function text( string $named, int $len = 0, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_String, $len, $default, $comment )->nullable();
    }

    /** 富文本字段 （html） */
    public static function richText( string $named, bool $bigCapacity = false, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_RichText, $bigCapacity ? 100000 : -1, NULL, $comment )->nullable();
    }

    /** JSON,ASJson通用 */
    public static function json( string $named, $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_Json, -1, NULL, $comment );
    }


    /** 经纬度字段 */
    public static function location( string $named, $default = NULL, string $comment = NULL ): DBFieldStruct
    {
        return static::init( $named, DBField_Location, -1, $default, $comment );
    }





    public function comment( string $comment ): DBFieldStruct
    {
        $this->comment = $comment;
        return $this;
    }

    public function nullable(): DBFieldStruct
    {
        $this->nullable = true;
        return $this;
    }

    public function defaultBy( $value ): DBFieldStruct
    {
        $this->default = $value;
        return $this;
    }

    public function index( string $DBIndex = DBIndex_Index ): DBFieldStruct {
        $this->index = $DBIndex;
        return $this;
    }

    public function setDecimalLength( $lenL, $lenR ): DBFieldStruct{
        $this->length = "{$lenL},{$lenR}";
        return $this;
    }

    public function export(): string{

        $query = " `{$this->name}` ";

        /** 字段名称 类型 */
        switch ( $this->type ){

            case DBField_Boolean  :
                $query .= " TINYINT(1) ";
                # tinyint 1
                break;
            case DBField_Int      :
                if ( $this->length < 4 ){
                    $query .= " TINYINT({$this->length}) ";
                }elseif ( $this->length < 7 ){
                    $query .= " MEDIUMINT({$this->length}) ";
                }else{
                    $query .= " BIGINT({$this->length}) ";
                }
                # tinyint 1-3     mediumint 3-6   bigint 8-13
                break;
            case DBField_Float    :
                $query .= " FLOAT ";
                break;
            case DBField_Double   :
                $query .= " DOUBLE ";
                break;
            case DBField_Decimal  :
                $_ = explode(',',$this->length);
                $query .= " DECIMAL( {$_[0]},{$_[1]} ) ";
                break;
            case DBField_TimeStamp :
                $query .= " BIGINT(13) ";
                break;
            case DBField_String   :
                if( $this->length > 0 && $this->length < 2049 ){
                    $query .= " VARCHAR({$this->length}) ";
                }elseif( $this->length < 65535 ){
                    $query .= " TEXT ";
                }else{
                    $query .= " MEDIUMTEXT ";
                }
                # varchar <=2048 ,  text >2048
                break;
            case DBField_RichText :
                if( $this->length < 65535 ){
                    $query .= " TEXT ";
                }else{
                    $query .= " MEDIUMTEXT ";
                }
                # text 65535  mediumint 16777215
                break;
            case DBField_Json     :
            case DBField_ASJson   :
                $query .= " TEXT ";
                break;

            case DBField_Location :
                $query .= " GEOMETRY ";
                break;

        }

        /** 是否允许空值 */
        $query .= $this->nullable ? ' NULL ' : ' NOT NULL ';

        /** 默认值 */
        if ( isset($this->default) ){

            $query .= " DEFAULT ";
            $query .= $this->type >= 100 ? "'{$this->default}' " : "{$this->default} ";

        }else{
            $query .= $this->nullable ? ' DEFAULT NULL ' : '';
        }

        /** 主键 Primary */
        $query .= $this->index === DBIndex_Primary ? ' AUTO_INCREMENT ' : '';

        /** 备注 */
        $query .= $this->comment ? " COMMENT '{$this->comment}' " : '' ;

        return $query;
    }

    public function exportIndex(): string{

        $export = "";

        switch ( $this->index ){

            case DBIndex_Primary:
                $export .= " PRIMARY KEY (`{$this->name}`) ";
                break;

            case DBIndex_Unique:
                $export .= " UNIQUE (`{$this->name}`) USING HASH ";
                break;

            case DBIndex_Index:
                $export .= " INDEX (`{$this->name}`) USING HASH ";
                break;

            case DBIndex_FullText:
                $export .= " FULLTEXT ( `{$this->name}` ) WITH PARSER ngram ";
                break;

            case DBIndex_Spatial:
                $export .= " SPATIAL ( `{$this->name}` )  ";
                break;
        }
        return $export;
    }

    public function hasIndex(): bool{
        return isset($this->index);
    }
}
