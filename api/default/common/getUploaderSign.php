<?php
/**
 * Description
 * getOSSSign.php
 */

namespace common;

use APS\ASAPI;
use APS\ASResult;
use APS\Uploader;

class getUploaderSign extends ASAPI
{

    const mode = ASAPI_Scope_Public;
    const scope = ASAPI_Scope_Public;
    const groupLevelRequirement = GroupLevel_Registered;

    public function run(): ASResult
    {
        $this->mode = $this->params['mode'] ?? 'JSON';
        $type = $this->params['type'] ?? 'image';

        return  Uploader::common()->getSign($type);

    }


}