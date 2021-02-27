<?php
namespace manager;
use APS\ASModel;

class generateTableStructByJson extends \APS\ASAPI{

    protected static $groupLevelRequirement = 900;
    protected $scope = 'public';
    public $mode = 'JSON';

    public function run(): \APS\ASResult
    {
        $json = $this->params['json'];
        if( !isset($json) ){
            return $this->error(103,'Please Input JSON Struct.');
        }

        $struct = json_decode($json, true );
        if (!$struct){
            return $this->error(110,'Decode failed');
        }
        if ( !isset($struct['table']) || !isset($struct['fields']) ){
            return $this->error(120,'Valid struct is : {"table":"item_table","fields":[{"name":"uid","type":"varchar","len":8,	"dft":","unq":1,"cmt":"签ID"},{"name":"categoryid","type":"varchar","len":8,	"dft":"NULL","idx":1,"cmt":"分类ID"},{"name":"type","type":"varchar","len":12,	"dft":"NULL","idx":1,"cmt":"类型"}]} .Please check your json.');
        }

        return _ASDB()->newTable( $json );
    }
}