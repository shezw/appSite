<?php

namespace APS;

/**
 * 设置项、系统配置
 * Setting, Configs
 *
 * 设置主要解决 程序的配置一些服务通用参数配置 一些公共资源、启动项动态配置的问题
 * SETTING需要改造成比较通用的模型
 * 即 所有需要个性化定制 存储到MySQL服务器或者REDIS服务器的设置 都可以用统一的方法来添加
 * 后期考虑自动处理REDIS服务器和MySQL之间的自动关系
 *
 * @package APS\core
 * @mark
 *     settingid    设置ID
 *     keyid        查询key
 *     description  描述 120字以内
 *     content      设置内容 k-v json
 *     type         格式 JSON INT DOUBLE FLOAT STRING BOOL BOOLEAN
 *     scope        版本号/作用域
 *     status       状态
 *
 * PS:当keyid 和版本号一致时 会直接覆盖数据, 没有版本号时 添加默认为1 ,更新默认取版本号最大版本, 删除同更新(一次最多删除一条)
 */
class ASSetting extends ASModel{

    // SETTING和普通 Model类不同

    protected static $rds_auto_cache = true;

    /**
     * 全局单例
     * @return ASSetting
     */
    public static function shared():ASSetting{

        if ( !isset($GLOBALS['ASSetting']) ){
            $GLOBALS['ASSetting'] = new ASSetting();
        }
        return $GLOBALS['ASSetting'];
    }

    /**
     * 获取配置
     * getConf
     * @param  string       $keyid
     * @param  string|null  $scope
     * @return mixed |null
     */
    public function getConf( string $keyid, string $scope = null ){

        $setting = $this->read($keyid,$scope);

        return $setting->isSucceed() ? $setting->getContent() : null ;
    }


    /**
     * 读取设置
     * read
     * @param  string       $keyid
     * @param  string|null  $scope
     * @return \APS\ASResult
     */
    public function read( string $keyid , string $scope = null ){

        if( !$this->has(['keyid'=>$keyid,'scope'=>$scope,'status'=>'enabled']) ){
            return $this->error(10086,i18n('SYS_NON'),'Setting->read');
        }

        $DETAIL = $this->list(['keyid'=>$keyid,'scope'=>$scope,'status'=>'enabled'],1,1,'scope DESC, createtime DESC')->getContent()[0]['content'];
        $setting = Encrypt::ASJsonDecode($DETAIL);

        return $this->take($setting)->success(0,i18n('SYS_GET_SUC'));

    }

    /**
     * 通过keyid ,scope 获取settingID
     * getSettingid
     * @param  string       $keyid
     * @param  string|null  $scope
     * @return \APS\ASResult
     */
    public function getSettingid( string $keyid, string $scope = null ){

        $getFirst = $this->list(['keyid'=>$keyid,'scope'=>$scope],1,1,'scope DESC, createtime DESC');

        if(!$getFirst->isSucceed()){
            return $this->error(10086,i18n('SYS_NON'),'Setting->getSettingid');
        }else{
            return $this->take($getFirst->getContent()[0]['settingid'])->success(i18n('SYS_GET_SUC'),'Setting->getSettingid');
        }
    }

    // 设定
    public function set( string $keyid, $value, string $description = null, string $scope = null ){

        $data = ['keyid'=>$keyid,'content'=>$value,'scope'=>$scope,'description'=>$description];

        $getSettingId = $this->getSettingid($keyid,$scope);

        if($getSettingId->isSucceed()){
            return $this->update($data,$getSettingId->getContent());
        }else{
            return $this->add($data);
        }
    }

    /**
     * 删除
     * delete
     * @param  string       $keyid
     * @param  string|null  $scope
     * @return \APS\ASResult
     */
    public function delete( string $keyid, string $scope = null ){

        $getSettingId = $this->getSettingid($keyid,$scope);

        if($getSettingId->isSucceed()){
            return $this->remove($getSettingId->getContent());
        }else{
            return $getSettingId;
        }
    }


    /**
     * 切换状态 (用于bool值切换)
     * switchStatus for boolean setting
     * @param  string       $keyid
     * @param  string|null  $scope
     * @return bool|mixed
     */
    public function switchStatus( string $keyid, string $scope = null ){

        if($this->_isCacheEnabled()){

            return $this->getRedis()->read( [$keyid,$scope] );
        }else{

            $Setting = static::read( $keyid,$scope );
            return $Setting->isSucceed() && $Setting->getContent();
        }
    }

    public function switchSet( string $keyid, string $scope = null, bool $status = true ){

        if($this->_isCacheEnabled()){

            return $this->getRedis()->cache([$keyid,$scope],$status );
        }else{

            return $this->set($keyid,$status,null,$scope);
        }
    }

    public function switchOn( string $keyid, string $scope = null ){

        return $this->switchSet($keyid,$scope,true);
    }

    public function switchOff( string $keyid, string $scope = null ){

        return $this->switchSet($keyid,$scope,false);
    }


    public static $table     = "system_setting";  // 表
    public static $primaryid = "settingid";     // 主字段
    public static $addFields = [
        'settingid',
        'keyid',
        'description',
        'content',
        'scope',
        'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'keyid',
        'description',
        'content',
        'scope',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'settingid',
        'keyid',
        'description',
        'content',
        'scope',
        'status',
    ]; // 概览支持字段
    public static $listFields = [
        'settingid',
        'keyid',
        'description',
        'content',
        'scope',
        'status',
    ];     // 列表支持字段
    public static $countFilters = [
        'settingid',
        'keyid',
        'status',
        'scope',
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'content'=>'ASJson'
    ];

}

