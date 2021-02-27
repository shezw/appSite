<?php
namespace manager;
use APS\ASModel;

class generateTableStruct extends \APS\ASAPI{

    protected static $groupLevelRequirement = 900;
    protected $scope = 'public';
    public $mode = 'JSON';

    public function run(): \APS\ASResult
    {
        $className = $this->params['class'];
        if( !isset($className) ){
            return $this->error(103,'Please Input class name.');
        }

        return _ASDB()->newTable( ['fields'=>$className::$dataStruct, 'table'=>$className::$table] );
    }
}