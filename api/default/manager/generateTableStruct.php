<?php
namespace manager;
use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\DBTableStruct;

class generateTableStruct extends ASAPI{

    const groupLevelRequirement = GroupLevel_SuperAdmin;
    const scope = ASAPI_Scope_Public;
    public $mode = ASAPI_Mode_Json;

    public function run(): ASResult
    {
        $className = $this->params['class'] ?? ASModel::class;
        if( !isset($className) ){
            return $this->error(103,'Please Input class name.');
        }

        return _ASDB()->newTable(
            DBTableStruct::init( $className::table )->fromArray( $className::tableStruct )
        );
    }
}