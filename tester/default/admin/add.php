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

    protected static $groupLevelRequirement = 90000;
    public $mode = 'JSON';
    public $scope = 'public';

    public function runTest(): ASResult
    {
        return User::common()->add( $this->params );
    }
}