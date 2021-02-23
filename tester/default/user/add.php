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

    protected static $groupLevelRequirement = 80000;
    public $mode = 'JSON';
    public $scope = 'public';

    public function runTest(): ASResult
    {
        $count = $this->params['total'] ?? 1;
        $completed = 0;

        for( $i=0; $i<$count; $i ++ ){
            $username = Encrypt::radomCode(10);
            $password = Encrypt::radomNum(12);
            $t = Time::common()->now;
            $nickname = "testUser_{$i}_{$t}";

            $completed += User::common()->add( ['username'=>$username,'password'=>$password,'nickname'=>$nickname] )->isSucceed() ? 1 : 0  ;
        }

        return $this->take($completed)->success();
    }
}