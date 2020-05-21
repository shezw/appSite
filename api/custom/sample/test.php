<?php

namespace sample;

use APS\ASAPI;

/**
 * 示例公开接口
 * test (Public scope)
 */
class test extends ASAPI
{
    protected $scope = 'public';
    public  $mode = 'JSON';

    public function run(): \APS\ASResult
    {

        return $this->success( i18n('SYS_GET_SUC') );

    }

}