<?php
/**
 * Description
 * addItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class addItem extends ASAPI
{

    private $itemClass = '\APS\ASModel';
    private $itemId = '';
    private $data   = [];

    protected $scope = 'public';
    public  $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 40000;

    public function run(): ASResult
    {
        $this->itemClass = $this->params['itemClass'] ?? ASModel::class;
        $this->data      = $this->params['data'];

        if( !class_exists( $this->itemClass ) ){
            $this->itemClass = 'APS\\'.$this->itemClass ;
        }

        $this->data['authorid'] = $this->user->userid;
        $this->data['userid']   = $this->user->userid;

        return $updateItem = $this->itemClass::common()->add( $this->data ) ?? ASResult::shared();

    }

}