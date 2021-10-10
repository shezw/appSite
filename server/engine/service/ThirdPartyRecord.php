<?php

namespace APS;

class ThirdPartyRecord extends ASRecord {

    const table = 'record_thirdparty';
    const comment = '第三方日志';

    protected $mode = ASRecord::Mode_ThirdParty;

}