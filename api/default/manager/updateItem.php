<?php
/**
 * Description
 * updateItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class updateItem extends ASAPI
{

    private $itemClass = '\APS\ASModel';
    private $itemId = '';
    private $data   = [];

    protected $scope = 'public';

    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 40000;


    public function run(): ASResult
    {
        $this->itemClass = $this->params['itemClass'] ?? ASModel::class;
        $this->itemId    = $this->params['itemId'];
        $this->data      = $this->params['data'];

        if( !class_exists( $this->itemClass ) ){
            $this->itemClass = 'APS\\'.$this->itemClass ;
        }

        return $updateItem = $this->itemClass::common()->update( $this->data , $this->itemId ) ?? ASResult::shared();

    }

}