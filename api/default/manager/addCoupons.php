<?php
/**
 * Description
 * addItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\CommerceCoupon;

class addCoupons extends ASAPI
{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = 80000;

    public function run(): ASResult
    {
        // $this->itemClass = $this->params['itemClass'] ?? ASModel::class;
        $data = [
            'amount'=>$this->params['amount'],
            'min'=>$this->params['min'],
            'max'=>$this->params['max'],
            'userid'=>$this->params['userid'],
        ];

        $bulk =  $this->params['bulk'];

        for ($i=0; $i < $bulk; $i++) {
            CommerceCoupon::common()->addByArray($data);
        }

        return $this->success();

    }

}