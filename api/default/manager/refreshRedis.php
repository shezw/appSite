<?php
/**
 * Description
 * refreshRedis.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;

class refreshRedis extends ASAPI
{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager];
    const groupLevelRequirement = GroupLevel_SuperAdmin;

    public function run(): ASResult
    {
        return _ASRedis()->flush() ? $this->success('Success','refreshRedis') : $this->error(700,'Unknown Error.','refreshRedis') ;
    }

}