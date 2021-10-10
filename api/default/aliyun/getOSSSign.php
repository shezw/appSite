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

    const mode = ASAPI_Scope_Public;
    const scope = ASAPI_Scope_Public;
    const groupLevelRequirement = 10000;

    private $type = 'image';

    public function run(): ASResult
    {
        $this->mode = $this->params['mode'] ?? 'JSON';
        $this->type = $this->params['type'] ?? 'image';

        return  AliyunOSS::common()->policySign( $this->type );

    }


}