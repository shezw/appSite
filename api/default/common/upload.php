<?php
/**
 * Description
 * OSSCallback.php
 */

namespace common;

use APS\AliyunOSS;
use APS\ASAPI;
use APS\ASResult;
use APS\Uploader;

class upload extends ASAPI
{
    const scope = ASAPI_Scope_Public;

    public function run(): ASResult
    {
        Uploader::common()->receive( $this->params );

        return $this->success();
    }


}