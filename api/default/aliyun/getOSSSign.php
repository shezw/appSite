<?php
/**
 * Description
 * getOSSSign.php
 */

namespace aliyun;

use APS\AliyunOSS;
use APS\ASAPI;
use APS\ASResult;

class getOSSSign extends ASAPI
{

    const mode = ASAPI_Mode_Json;
    const scope = ASAPI_Scope_Public;
    const groupLevelRequirement = 10000;

    public function run(): ASResult
    {
        $type = $this->params['type'] ?? 'image';

        return  AliyunOSS::common()->policySign($type);

    }


}