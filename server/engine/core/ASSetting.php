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
 *     uid          设置ID
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

    const table     = "system_setting";
    const comment   = "系统设置";
    const primaryid = "uid";
    const addFields = [
        'uid','keyid','saasid',
        'description','content',
        'scope',
        'status','createtime','lasttime'
    ];
    const updateFields = [
        'keyid','saasid',
        'description',
        'content','scope',
        'status','createtime','lasttime'
    ];
    const detailFields = [
        'uid','keyid','saasid',
        'description',
        'content','scope',
        'status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid','keyid',
        'description',
        'content','scope',
        'status','createtime','lasttime'
    ];
    const listFields = [
        'uid','keyid','saasid',
        'description',
        'content','scope',
        'status','createtime','lasttime'
    ];
    const filterFields = [
        'uid','keyid','saasid',
        'status','scope','createtime','lasttime'
    ];
    const depthStruct = [
        'createtime'=>DBField_Int,
        'lasttime'=>DBField_Int,
        'content'=>DBField_ASJson
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'设置ID' ,      'idx'=>DBIndex_Unique ],
        'saasid'     =>['type'=>DBField_String,  'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',     'idx'=>DBIndex_Index,],
        'keyid'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'查询key' ,     'idx'=>DBIndex_Index ],
        'description'=>['type'=>DBField_String,  'len'=>255, 'nullable'=>1,  'cmt'=>'描述 120字以内' ],
        'content'=>  ['type'=>DBField_ASJson,      'len'=>-1,  'nullable'=>1,  'cmt'=>'设置内容 k-v json' ],
        'scope'=>    ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'作用域' ],
        'status'=>   ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',    'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    // SETTING和普通 Model类不同
    const rds_auto_cache = true;


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
     * @param string $keyId
     * @param string|null $scope
     * @return mixed |null
     */
    public function getConf(string $keyId, string $scope = null ){
        if( !_ASDB()->valid ){return null;}
        $setting = $this->read($keyId,$scope);
        return $setting->isSucceed() ? $setting->getContent() : null ;
    }


    /**
     * 读取设置
     * read
     * @param string $keyId
     * @param string|null $scope
     * @return ASResult
     */
    public function read(string $keyId , string $scope = null ): ASResult
    {
        $filter = DBConditions::init(static::table)
                ->where('keyid')->equal($keyId)
                ->where('saasid')->equalIf(saasId())
                ->and('status')->equal('enabled')
                ->and('scope')->equal($scope);

        if( !$this->has($filter) ){
            return $this->error(10086,i18n('SYS_NON'),'Setting->read');
        }

        $setting = $this->list($filter,1,1,'scope DESC, createtime DESC')->getContent()[0]['content'];

        return $this->take($setting)->success(0,i18n('SYS_GET_SUC'));

    }

    /**
     * 通过keyid ,scope 获取settingID
     * getSettingid
     * @param string $keyid
     * @param string|null $scope
     * @return ASResult
     */
    public function getSettingId(string $keyid, string $scope = null ): ASResult
    {
        $filter = DBConditions::init(static::table)
                ->where('keyid')->equal($keyid)
                ->and('saasid')->equalIf(saasId())
                ->and('scope')->equal($scope);

        $getFirst = $this->list($filter,1,1,'scope DESC, createtime DESC');

        if(!$getFirst->isSucceed()){
            return $this->error(10086,i18n('SYS_NON'),'Setting->getSettingId');
        }else{
            return $this->take($getFirst->getContent()[0][static::primaryid])->success(i18n('SYS_GET_SUC'),'Setting->getSettingid');
        }
    }

    // 设定
    public function set(string $keyId, $value, string $description = null, string $scope = null ): ASResult
    {
        $values = DBValues::init('keyid')->string($keyId)
            ->set('content')->ASJson($value)
            ->set('saasid')->stringIf(saasId())
            ->set('description')->stringIf($description)
            ->set('scope')->stringIf($scope);

        $getSettingId = $this->getSettingId($keyId,$scope);

        if($getSettingId->isSucceed()){
            return $this->update($values,$getSettingId->getContent());
        }else{
            return $this->add($values);
        }
    }

    /**
     * 删除
     * delete
     * @param string $keyId
     * @param string|null $scope
     * @return ASResult
     */
    public function delete(string $keyId, string $scope = null ): ASResult
    {
        $getSettingId = $this->getSettingId($keyId,$scope );

        if($getSettingId->isSucceed()){
            return $this->remove($getSettingId->getContent());
        }else{
            return $getSettingId;
        }
    }


    /**
     * 切换状态 (用于bool值切换)
     * switchStatus for boolean setting
     * @param string $keyId
     * @param string|null $scope
     * @return bool|mixed
     */
    public function switchStatus(string $keyId, string $scope = null ): bool
    {

        if($this->_isCacheEnabled()){

            return $this->getRedis()->read( [$keyId,$scope,saasId()] );
        }else{

            $Setting = static::read( $keyId,$scope );
            return $Setting->isSucceed() && $Setting->getContent();
        }
    }

    public function switchSet(string $keyId, string $scope = null, bool $status = true ){

        if($this->_isCacheEnabled()){

            return $this->getRedis()->cache([$keyId,$scope,saasId()],$status );
        }else{

            return $this->set($keyId,$status,null,$scope );
        }
    }

    public function switchOn(string $keyId, string $scope = null ){

        return $this->switchSet($keyId,$scope );
    }

    public function switchOff(string $keyId, string $scope = null ){

        return $this->switchSet($keyId,$scope,false);
    }

}

