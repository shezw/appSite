<?php

namespace APS;

class AdminRecord extends ASRecord {

    const table = 'record_admin';
    const comment = '后台日志';

    protected $mode = ASRecord::Mode_Admin;

}