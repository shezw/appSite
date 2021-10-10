<?php
/**
 * Description
 * sortIncreaseItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class sortDecreaseItem extends ASAPI
{
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = GroupLevel_Editor;


    public function run(): ASResult
    {
        $itemClass = $this->params['itemClass'] ?? ASModel::class;
        $itemId = $this->params['itemId'];
        $size = (int)($this->params['size'] ?? 1);

        if (!class_exists($itemClass)) {
            $itemClass = 'APS\\' . $itemClass;
        }

        return $updateItem = $itemClass::common()->decreaseSort( $itemId, $size) ?? ASResult::shared();

    }

}