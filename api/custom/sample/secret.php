<?php

namespace sample;

use APS\ASAPI;

/**
 * 示例
 * secret
 * @package sample
 */
class secret extends ASAPI
{

    protected $scope = 'system';
    public  $mode = 'JSON';

    public function run(): \APS\ASResult
    {

        return $this->success( i18n('SYS_GET_SUC') );

    }

}