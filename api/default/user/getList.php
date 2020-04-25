<?php
/**
 * Description
 * getUserList.php
 */

namespace user;

class getList extends \APS\ASAPI
{

    protected static $groupLevelRequirement = 900;
    protected static $groupCharacterRequirement = 'manager';

    public function run():\APS\ASResult
    {

        return $this->success('请求用户列表api');

    }

}