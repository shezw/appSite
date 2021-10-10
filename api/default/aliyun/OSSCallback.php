<?php
/**
 * Description
 * OSSCallback.php
 */

namespace aliyun;

use APS\AliyunOSS;
use APS\ASAPI;
use APS\ASResult;

class OSSCallback extends ASAPI
{
    const scope = ASAPI_Scope_Public;

    public function run(): ASResult
    {
        AliyunOSS::callback();

        return $this->success();
    }


}