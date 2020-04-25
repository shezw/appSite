<?php

namespace APS;

/**
 * 权限验证
 * AccessVerify
 * @package APS\service\Access
 */
class AccessVerify extends ASModel{

    public static $table     = 'access_verify';
    public static $primaryid = 'origin';

    /**
     * 验证流程开始
     * begin verify
     * @param  string  $origin     验证源   eg: '18xxxxxxxxx' ? 'email@ xxx.com' ...
     * @param  string  $scope      验证作用域
     * @param  int     $duration   有效时长 单位秒 day*24*3600
     * @return \APS\ASResult
     */
    public function begin( string $origin, int $duration, string $scope = 'common' ):ASResult{

        $code   = Encrypt::radomNum(getConfig('ACCESSVERIFY_LENGTH')??6);
        $expire = time()+$duration;

        $data = [
            'origin'   => $origin,
            'scope'    => $scope,
            'code'     => $code,
            'expire'   => $expire,
        ];

        if (!$this->checkInterval($origin,$scope)) {

            return $this->take($origin)->error(2018,i18n('FREQ_LIMIT'),'AccessVerify->begin');
        }

        $DB = $this->getDB()->add($data,static::$table);

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
    public function checkInterval( string $origin, string $scope = 'common' ){

        $expire     = time() - ( getConfig('ACCESSVERIFY_INTERVAL') ?? 30 );
        $conditions = "origin='{$origin}' AND scope='{$scope}' AND lasttime>{$expire}";

        $DB  = $this->getDB()->count(static::$table,$conditions);

        return $DB['content']===0;
    }


    /**
     * 进行验证
     * validate code
     * @param  string  $origin  验证源   eg: '18xxxxxxxxx' ? 'email@ xxx.com' ...
     * @param  string  $code    验证码   eg: 123456
     * @param  string  $scope   验证作用域
     * @return \APS\ASResult
     */
    public function validate( string $origin, string $code, string $scope = 'common' ){

        $conditions = ['origin'=>$origin,'scope'=>$scope];

        $DB_COUNT = $this->getDB()->count(static::$table,$conditions);

        if ($DB_COUNT->getContent()<=0){ return $this->error( 10086,i18n('ACC_VER_NEST'),'AccessVerify->validate'); }

        $DB = $this->getDB()->check($code,'code',static::$table,$conditions);

        if ( !$DB->isSucceed() ){ return $DB; }

        $this->setExpire($conditions);

        return $this->take($origin)->success(i18n('AUTH_SUC'),'AccessVerify->validate');

    }


    /**
     * 设置为过期
     * setExpire
     * @param  array|string  $conditions
     * @return \APS\ASResult
     */
    public function setExpire( $conditions ){

        return $this->getDB()->update(['expire'=>0],static::$table, $conditions);
    }


    /**
     * 清理过期验证信息
     * clear expired verify
     * @return \APS\ASResult
     */
    public function clearVerify(){

        $t  = time() - 3 * Time::DAY ;
        return $this->getDB()->remove(static::$table,"expire<{$t}");
    }

}