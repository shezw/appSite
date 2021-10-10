<?php

namespace APS;

class DBTableStruct{

    /**
     * @var array(DBFieldStruct) [ DBFieldStruct ]
     */
    private $fields;

    /**
     * @var string
     */
    public $name;

    private $engine  = 'InnoDB';
    private $collate = 'utf8mb4_general_ci';
    private $charset = 'utf8mb4';

    public function __construct( string $tableName )
    {
        $this->name = $tableName;
    }

    /**
     * 从数组数据中建立
     * @param array $tableStruct
     * @return $this
     */
    public function fromArray( array $tableStruct ): DBTableStruct
    {
        foreach ($tableStruct as $field => $properties ) {

            $name = $field;
            $type = $properties['type'];
            $length  = $properties['len'];
            $default = $properties['dft'] ?? $properties['default'] ?? NULL;
            $comment = $properties['cmt'] ?? NULL;
            $index   = $properties['idx'] ?? $properties['index'] ?? NULL;
            $nullable= $properties['nullable'];

            if ( $type === DBField_Decimal ){
                $_len = explode(',',$length);
                $DBfield = DBFieldStruct::decimal( $name, (int)$_len[0],(int)$_len[1],$default, $comment );
            }else{
                $DBfield = DBFieldStruct::init($name, $type, $length, $default, $comment );
            }

            $this->add( $DBfield );
            if( $index ){
                $this->indexWith( $index );
            }
            if ( $nullable ){
                $this->nullable();
            }
        }
        return $this;
    }

    public static function init( string $tableName ): DBTableStruct
    {
        $table = new static( $tableName );
        $table->primaryField('id');
        return $table;
    }

    private function last() : DBFieldStruct
    {
        return $this->fields[ count($this->fields) - 1 ];
    }


    public function add( DBFieldStruct $DBField ): DBTableStruct
    {
        $this->fields[] = $DBField;
        return $this;
    }

    /** 主键字段（自增INT） */
    private function primaryField( string $named ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Int, 15, NULL, 'Index ID' )->index(DBIndex_Primary) );
    }

    /** UID字段 */
    public function uidField( string $named ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_String, 8, NULL, 'UID' ) );
    }

    /** 布尔值字段 */
    public function booleanField( string $named, $default = false, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Boolean, 1, ($default ? 1 : 0), $comment ) );
    }

    public function intField( string $named, $length, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Int, $length, $default, $comment ) );
    }

    /** 时间戳字段 */
    public function timeField(string $named, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_TimeStamp, 13, $default, $comment ) );
    }

    /** 浮点数字段 */
    public function floatFiled( string $named, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Float, -1, $default, $comment ) );
    }

    /** 双精度 浮点数字段 */
    public function doubleFiled( string $named, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Double, -1, $default, $comment ) );
    }

    /** 精确数字段 用于价格等保留位数字段 */
    public function decimalField( string $named, int $lenL, int $lenR, $default = NULL, $comment = NULL ): DBTableStruct
    {
        $FieldStruct = DBFieldStruct::init( $named, DBField_Decimal, -1, $default, $comment );
        return $this->add( $FieldStruct->setDecimalLength($lenL,$lenR) );
    }

    /** 字符串字段 */
    public function varcharField( string $named, int $length = 8, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_String, $length, $default, $comment ) );
    }

    /** 文本字段 */
    public function textField( string $named, int $len = 0, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_String, $len, $default, $comment )->nullable() );
    }

    /** 富文本字段 （html） */
    public function richTextField( string $named, bool $bigCapacity = false, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_RichText, $bigCapacity ? 100000 : -1, NULL, $comment )->nullable() );
    }

    /** JSON,ASJson通用 */
    public function jsonField( string $named, $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Json, -1, NULL, $comment ) );
    }


    /** 经纬度字段 */
    public function locationField( string $named, $default = NULL, string $comment = NULL ): DBTableStruct
    {
        return $this->add( DBFieldStruct::init( $named, DBField_Location, -1, $default, $comment ) );
    }



    /** 设为可空 */
    public function nullable(): DBTableStruct
    {
        $this->last()->nullable();
        return $this;
    }

    /** 设置索引(类型)
     * @param string $DBFieldIndexType
     * @return DBTableStruct
     */
    public function indexWith( string $DBFieldIndexType ): DBTableStruct
    {
        $this->last()->index( $DBFieldIndexType );
        return $this;
    }

    public function index(): DBTableStruct
    {
        return $this->indexWith( DBIndex_Index );
    }

    public function unique(): DBTableStruct
    {
        return $this->indexWith( DBIndex_Unique );
    }


    /** 设置默认值 */
    public function defaultBy( $value ): DBTableStruct
    {
        $this->last()->defaultBy( $value );
        return $this;
    }

    /** 增加备注(描述) */
    public function comment( string $with ): DBTableStruct
    {
        $this->last()->comment($with);
        return $this;
    }

    public function export(): string{

        $query = " {$this->name} (";

        /** Fields */
        for ( $i=0; $i< count($this->fields); $i++ ) {

            $query .= $i>0 ? "," : '';
            $query .= $this->fields[$i]->export();

        }

        /** Index */
        for ( $i=0; $i< count($this->fields); $i++ ){

            if ( $this->fields[$i]->hasIndex() ){

                $query .= ",";
                $query .= $this->fields[$i]->exportIndex();

            }
        }

        $query .= ') ';
        $query .= " ENGINE  {$this->engine}";
        $query .= " CHARSET {$this->charset}";
        $query .= " COLLATE {$this->collate}";

        return $query;
    }
}
