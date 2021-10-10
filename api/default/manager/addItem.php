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
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = GroupLevel_Editor;

    public function run(): ASResult
    {
        $itemClass = $this->params['itemClass'] ?? ASModel::class;
        $data = $this->params['data'];

        if( !class_exists($itemClass) ){
            $itemClass = 'APS\\'. $itemClass;
        }

        $data['authorid'] = $this->user->userid;

        return $updateItem = $itemClass::common()->addByArray($data) ?? ASResult::shared();

    }

}