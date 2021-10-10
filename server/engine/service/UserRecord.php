<?php

namespace APS;

class UserRecord extends ASRecord {

    const table = 'record_user';
    const comment = '用户日志';

    protected $mode = ASRecord::Mode_User;

}