<?php
/**
 * Description
 * admin.php
 */

namespace admin;

use APS\ASResult;
use APS\ASTester;
use APS\User;

class add extends ASTester{

    const groupLevelRequirement = GroupLevel_SuperAdmin;
    const mode = ASAPI_Mode_Json;
    const scope = ASAPI_Scope_Public;

    public function runTest(): ASResult
    {
        return User::common()->add( $this->params );
    }
}