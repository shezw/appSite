<?php
/**
 * Description
 * getUserList.php
 */

namespace user;

use APS\ASAPI;
use APS\ASResult;

class getList extends ASAPI
{

    const groupLevelRequirement = 900;
    const groupCharacterRequirement = 'manager';

    public function run(): ASResult
    {

        return $this->success('请求用户列表api');

    }

}