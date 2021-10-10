<?php

namespace APS;

class DBValue{

    private $field;

    private $type;

    private $boolean;
    private $number;
    private $string;
    private $text;
    private $json;
    private $location;


    public function __construct( string $field )
    {
        $this->field = $field;
    }

    public static function init( string $field ): DBValue
    {
        return new static( $field );
    }

    public function setNull(): DBValue
    {
        $this->type = DBField_Null;
        return $this;
    }


    /**
     * @param bool $boolean
     * @return DBValue
     */
    public function bool( bool $boolean ): DBValue
    {
        $this->type = DBField_Boolean;
        $this->boolean = $boolean;
        return $this;
    }

    /**
     * @param double $number
     * @return DBValue
     */
    public function equal( $number ): DBValue
    {
        $this->type   = DBField_Float;
        $this->number = (double)$number;
        return $this;
    }

    /**
     * @param array $json
     * @return DBValue
     */
    public function json( array $json ): DBValue
    {
        $this->type   = DBField_Json;
        $this->json = $json;
        return $this;
    }

    /**
     * @param $ASJson
     * @return DBValue
     */
    public function ASJson( $ASJson ): DBValue
    {
        $this->type   = DBField_ASJson;
        $this->json = $ASJson;
        return $this;
    }

    /**
     * @param float $lng
     * @param float $lat
     * @param bool $autoFill
     * @return DBValue
     */
    public function location( float $lng, float $lat, bool $autoFill = false ): DBValue
    {
        $this->type   = DBField_Location;
        $this->location = [$lng,$lat,$autoFill];
        return $this;
    }

    public function string( string $string ): DBValue
    {
        $this->type   = DBField_String;
        $this->string = $string;
        return $this;
    }

    public function richText( string $richText ): DBValue
    {
        $this->type   = DBField_RichText;
        $this->text = $richText;
        return $this;
    }

    public function key():string{
        return $this->field;
    }

    public function value(){

        switch ( $this->type ){
            case DBField_Boolean  :
                return $this->boolean;
            case DBField_Int  :
            case DBField_Float  :
            case DBField_Double  :
            case DBField_Decimal  :
            case DBField_TimeStamp  :
                return $this->number;
            case DBField_String  :
                return $this->string;
            case DBField_RichText :
                return $this->text;
            case DBField_Json     :
            case DBField_ASJson   :
                return $this->json;
            case DBField_Location :
                return $this->location;
        }
        return NULL;
    }

    public function copy(): DBValue
    {
        $dbValue = new static( $this->field );

        switch ( $this->type ){

            case DBField_Null:
                $dbValue->setNull();
            break;
            case DBField_Boolean:
                $dbValue->bool($this->boolean);
            break;
            case DBField_Int  :
            case DBField_Float  :
            case DBField_Double  :
            case DBField_Decimal  :
            case DBField_TimeStamp  :
                $dbValue->equal( $this->number );
                break;
            case DBField_String  :
                $dbValue->string($this->string);
                break;
            case DBField_RichText :
                $dbValue->richText($this->text);
                break;
            case DBField_Json     :
                $dbValue->json($this->json);
                break;
            case DBField_ASJson   :
                $dbValue->ASJson($this->json);
                break;
            case DBField_Location :
                $dbValue->location( $this->location[0],$this->location[1], $this->location[2] ?? false );
                break;
        }
        return $dbValue;
    }

    public function export( bool $onlyValue = false ):string{

        $query = "";
        if( !$onlyValue ){
            $query .= "`{$this->field}` ";
            $query .= " = ";
        }
        switch ( $this->type ){
            case DBField_Null :
                $query .= " NULL ";
                break;
            case DBField_Boolean  :
                $query .= $this->boolean ? '1' : '0' ;
                break;
            case DBField_Int  :
            case DBField_Float  :
            case DBField_Double  :
            case DBField_Decimal  :
            case DBField_TimeStamp  :
                $query .= " {$this->number} ";
                break;
            case DBField_String  :
                $query .= "'". addslashes( $this->string ) ."'" ;
                break;
            case DBField_RichText :
                $query .= "'". addslashes( $this->text ) ."' " ;
                break;
            case DBField_Json     :
                $query .= "'".json_encode( $this->json,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK )."'";
                break;
            case DBField_ASJson   :
                $query .= "'".Encrypt::ASJsonEncode( $this->json )."'";
                break;
            case DBField_Location :
                $lng = $this->location[0];
                $lat = $this->location[1];

                $query .= floatval(_ASDB()->getVersion()) >= 8 ? "ST_GeomFromText('POINT({$lat} {$lng})',4326)" : " GeomFromWKB(POINT({$lng},{$lat}))" ;

                if( $this->location[2] ){
                    $query .= $onlyValue ? ", {$lng}, {$lat} " : ", lng={$lng}, lat={$lat} ";
                }
                break;

        }

        return $query;
    }

}