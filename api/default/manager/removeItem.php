<?php
/**
 * Description
 * removeItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class removeItem extends ASAPI
{

    private $itemClass = '\APS\ASModel';
    private $itemId = '';

    protected $scope = 'public';
    public  $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 40000;

    public function run(): ASResult
    {
        $this->itemClass = $this->params['itemClass'] ?? ASModel::class;
        $this->itemId    = $this->params['itemId'];

        if( !class_exists( $this->itemClass ) ){
            $this->itemClass = 'APS\\'.$this->itemClass ;
        }

        return $updateItem = $this->itemClass::common()->remove( $this->itemId ) ?? ASResult::shared();

    }

}