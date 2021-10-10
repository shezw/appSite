<?php

namespace APS;

/**
 * 权限验证
 * AccessVerify
 * @package APS\service\Access
 */
class AccessVerify extends ASModel{

    const table     = 'access_verify';
    const comment   = '权限验证';
    const primaryid = 'origin';

    const addFields = ['saasid','origin','scope','code','expire','createtime','lasttime'];
    const updateFields = ['expire','lasttime'];
    const detailFields = ['saasid','origin','scope','code','expire','createtime','lasttime'];
    const filterFields = ['saasid','origin','scope','expire','createtime','lasttime'];

    const depthStruct = [
        'expire'=>DBField_TimeStamp,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'origin'=>   ['type'=>DBField_String,    'len'=>127, 'nullable'=>0,  'cmt'=>'来源信息',  'idx'=>DBIndex_Index ],
        // 申请认证的来源信息 eg: 18601013441 .. sprite@donsee.cn
        'scope'=>    ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'权限作用域'],
        'code'=>     ['type'=>DBField_String,    'len'=>511, 'nullable'=>0,  'cmt'=>'认证码'],
        'expire'=>   ['type'=>DBField_TimeStamp, 'len'=>11,  'nullable'=>0,  'cmt'=>'过期时间 时间戳 必填'],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    /**
     * 验证流程开始
     * begin verify
     * @param  string  $origin     验证源   eg: '18xxxxxxxxx' ? 'email@ xxx.com' ...
     * @param  string  $scope      验证作用域
     * @param  int     $duration   有效时长 单位秒 day*24*3600
     * @return ASResult
     */
    public function begin( string $origin, int $duration, string $scope = 'common' ):ASResult{

        $code   = Encrypt::radomNum(getConfig('ACCESSVERIFY_LENGTH')??6);
        $expire = time()+$duration;

        $data = static::initValuesFromArray([
            'origin'   => $origin,
            'scope'    => $scope,
            'code'     => $code,
            'expire'   => $expire,
            'saasid'   => saasId()
        ]);

        if (!$this->checkInterval($origin,$scope)) {

            return $this->take($origin)->error(2018,i18n('FREQ_LIMIT'),'AccessVerify->begin');
        }

        $DB = $this->getDB()->add($data,static::table);

        if( !$DB ){ return $DB; }

        return $this->take($code)->success(i18n('ACC_CODE_SUC'),'AccessVerify->begin');

    }

    /**
     * 检测验证间隔时间
     * checkInterval
     * @param  string  $origin  验证源   eg: '18xxxxxxxxx' ? 'email@ xxx.com' ...
     * @param  string  $scope   验证作用域
     * @return bool
     */
    public function checkInterval( string $origin, string $scope = 'common' ): bool
    {

        $expire     = time() - ( getConfig('ACCESSVERIFY_INTERVAL') ?? 30 );

        $conditions = DBConditions::init( static::table )->where('origin')->equal($origin)->and('scope')->equal($scope)->and('lasttime')->bigger($expire)->and('saasid')->equalIf(saasId());

        $DB  = $this->getDB()->count(static::table,$conditions);

        return $DB['content']===0;
    }


    /**
     * 进行验证
     * validate code
     * @param  string  $origin  验证源   eg: '18xxxxxxxxx' ? 'email@ xxx.com' ...
     * @param  string  $code    验证码   eg: 123456
     * @param  string  $scope   验证作用域
     * @return ASResult
     */
    public function validate( string $origin, string $code, string $scope = 'common' ): ASResult
    {
        $conditions = DBConditions::init(static::table)->and('origin')->equal($origin)->and('scope')->equal($scope)->and('saasid')->equalIf(saasId());

        $DB_COUNT = $this->getDB()->count(static::table,$conditions);

        if ($DB_COUNT->getContent()<=0){ return $this->error( 10086,i18n('ACC_VER_NEST'),'AccessVerify->validate'); }

        $DB = $this->getDB()->check($code,'code',static::table,$conditions);

        if ( !$DB->isSucceed() ){ return $DB; }

        $this->setExpire($conditions);

        return $this->take($origin)->success(i18n('AUTH_SUC'),'AccessVerify->validate');

    }


    /**
     * 设置为过期
     * setExpire
     * @param  DBConditions  $conditions
     * @return ASResult
     */
    public function setExpire( DBConditions $conditions ): ASResult
    {
        return $this->getDB()->update(DBValues::init('expire')->number(0),static::table, $conditions->and('saasid')->equalIf(saasId()));
    }


    /**
     * 清理过期验证信息
     * clear expired verify
     * @return ASResult
     */
    public function clearVerify(): ASResult
    {
        $t  = time() - 3 * Time::DAY ;
        return $this->getDB()->remove(static::table,DBConditions::init()->where('expire')->less($t)->and('saasid')->equalIf(saasId()));
    }

}