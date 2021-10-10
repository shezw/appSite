<?php
/**
 * Description
 * updateItem.php
 */

namespace manager;

use APS\ASAPI;
use APS\ASModel;
use APS\ASResult;
use APS\User;

class updateUser extends ASAPI
{
    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = GroupLevel_Admin;

    public function run(): ASResult
    {
        $userId = $this->params['userId'];
        $data = $this->params['data'];

        $user = new User($userId);

        return $user->updateByArray( $data, $userId);
    }

}