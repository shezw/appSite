<?php
/**
 * Description
 * admin.php
 */

namespace user;

use APS\ASResult;
use APS\ASTester;
use APS\Encrypt;
use APS\Time;
use APS\User;

class add extends ASTester{

    const groupLevelRequirement = GroupLevel_Admin;
    const mode = ASAPI_Mode_Json;
    const scope = ASAPI_Scope_Public;

    public function runTest(): ASResult
    {
        $count = $this->params['total'] ?? 1;
        $completed = 0;

        for( $i=0; $i<$count; $i ++ ){
            $username = Encrypt::radomCode(10);
            $password = Encrypt::randomNumber(12);
            $t = Time::common()->now;
            $nickname = "testUser_{$i}_{$t}";

            $completed += User::common()->addByArray( ['username'=>$username,'password'=>$password,'nickname'=>$nickname] )->isSucceed() ? 1 : 0  ;
        }

        return $this->take($completed)->success();
    }
}