<?php
/**
 * Description
 * getOSSSign.php
 */

namespace aliyun;

use APS\AliyunOSS;
use APS\ASResult;

class getOSSSign extends \APS\ASAPI
{

    public $mode = 'JSON';
    private $type = 'image';

    protected $scope = 'public';

    protected static $groupLevelRequirement = 10000;

    public function run(): ASResult
    {
        $this->mode = $this->params['mode'] ?? 'JSON';
        $this->type = $this->params['type'] ?? 'image';

        return  AliyunOSS::common()->policySign( $this->type );

    }


}