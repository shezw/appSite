<?php
/**
 * Description
 * addItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class addCoupons extends ASAPI
{

    private $itemClass = '\APS\CommerceCoupon';
    private $itemId = '';
    private $data   = [];

    protected $scope = 'public';
    public    $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super','manager','editor'];
    protected static $groupLevelRequirement = 80000;

    public function run(): ASResult
    {
        // $this->itemClass = $this->params['itemClass'] ?? ASModel::class;
        $this->data      = [
            'amount'=>$this->params['amount'],
            'min'=>$this->params['min'],
            'max'=>$this->params['max'],
            'userid'=>$this->params['userid'],
        ];

        $bulk =  $this->params['bulk'];

        for ($i=0; $i < $bulk; $i++) { 
            $this->itemClass::common()->add( $this->data );
        }

        return $this->success();

    }

}