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

    protected $scope = 'public';
    public  $mode = 'JSON';

    protected static $groupCharacterRequirement = ['super','manager'];
    protected static $groupLevelRequirement = 90000;

    public function run(): ASResult
    {
        return _ASRedis()->flush() ? $this->success('Success','refreshRedis') : $this->error(700,'Unknown Error.','refreshRedis') ;
    }

}