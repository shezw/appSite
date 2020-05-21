<?php
/**
 * Description
 * OSSCallback.php
 */

namespace aliyun;

use APS\AliyunOSS;
use APS\ASResult;

class OSSCallback extends \APS\ASAPI
{

    protected $scope = 'public';

    public function run(): ASResult
    {
        return AliyunOSS::callback();
    }


}