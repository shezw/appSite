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
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = GroupLevel_Editor;

    public function run(): ASResult
    {
        $itemClass = $this->params['itemClass'] ?? ASModel::class;
        $itemId = $this->params['itemId'];
        $data = $this->params['data'];

        if( !class_exists($itemClass) ){
            $itemClass = 'APS\\'. $itemClass;
        }

        return $updateItem = $itemClass::common()->updateByArray( $data, $itemId) ?? ASResult::shared();

    }

}