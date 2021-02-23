<?php

namespace APS;

/**
 * 表单 - 申请
 * FormRequest
 *
 * 表单申请主要用于需要后台审核的业务逻辑，前台添加申请时将拒绝和通过审核的回调api写入applycall,rejectcall中
 *
 * @package APS\service
 */
class FormRequest extends ASModel{


    /**
     * 表单通过回调
     * applyCall
     * @param  string  $requestid
     * @return ASResult
     */
    public function applyCall( string $requestid ): ASResult
    {

        // 检查表单状态是否needpay
        if (!$this->isNeedcall($requestid)){

            return $this->take($requestid)->success(i18n('SYS_CAL_SEC'),'FormRequest->applyCall');
        }

        $DB = $this->get('applycall',$requestid);

        _ASRecord()->add([
            'itemid'=>$requestid,
            'type'=>'request',
            'event'=>'APPLYCALL',
            'status'=>$DB->getStatus(),
            'content'=>$DB,
            'sign'=>'$this->applyCall'
        ]);

        if (!$DB->isSucceed()) {

            return $this->take($requestid)->error(400,i18n('SYS_NON'),'FormRequest->applyCall');
        }

        $callback = ASAPI::systemInit('system\innerCallback',$DB->getContent())->run();

        if (!$callback->isSucceed()) {

            $UPDATE = $this->update(['applycall'=>$callback->getContent()],$requestid);

            return $this->take($callback)->error(305,i18n('SYS_CAL_FAL'),'$this->applyCall');
        }
        return $this->done($requestid);
    }

    /**
     * 表单拒绝回调
     * rejectCall
     * @param  string  $requestid
     * @return ASResult
     */
    public function rejectCall( string $requestid ): ASResult
    {

        // 检查表单状态是否needpay
        if ($this->isNeedcall($requestid)) {

            $DB = $this->get('rejectcall',$requestid);

            _ASRecord()->add([
                'itemid'=>$requestid,
                'type'=>'request',
                'event'=>'REJECTCALL',
                'status'=>$DB->getStatus(),
                'content'=>$DB,
                'sign'=>'$this->rejectCall'
            ]);

            if (!$DB->isSucceed()) {

                return $this->take($requestid)->error(400,i18n('SYS_NON'),'FormRequest->rejectcall');
            }

            $callback = ASAPI::systemInit('system\innerCallback',$DB->getContent())->run();;

            if (!$callback->isSucceed()) {

                $this->update(['rejectcall'=>$callback->getContent()],$requestid);
                return $this->take($callback)->error(305,i18n('SYS_CAL_FAL'),'$this->rejectcall');

            }
            return $this->reject($requestid);

        }else{

            return $this->success(i18n('SYS_CAL_SEC'),'$this->rejectcall');
        }
    }


    /////////// 状态确认

    // 表单是否存在
    public function exist( string $requestid ): bool
    {

        $count = $this->getDB()->count(static::$table,['requestid'=>$requestid])->getContent();
        return $count>0;

    }

    // 表单是否待支付
    public function isNeedpay( string $requestid ): bool
    {

        return $this->isStatus($requestid,'needpay');

    }

    // 表单是否完成
    public function isDone( string $requestid ): bool
    {

        return $this->isStatus($requestid,'done');

    }

    // 表单是否付款
    public function isPaid( string $requestid ): bool
    {

        return $this->isStatus($requestid,'paid');

    }

    // 是否被拒绝
    public function isRejected( string $requestid ): bool
    {

        return $this->isStatus($requestid,'rejected');

    }

    // 是否需要回调
    public function isNeedcall( string $requestid ): bool
    {

        return $this->isStatus($requestid,'pending');

    }

    // 表单是否过期
    public function isExpired( string $requestid ): bool
    {

        $expire = (int)$this->get('expire',$requestid)->getContent();

        return $expire!==0 && $expire<time(); // 0时代表无限期

    }


    ////////// 表单操作部分

    // 表单已支付
    public function paid( string $requestid ):ASResult{

        return $this->status($requestid,'paid');

    }

    // 表单完成
    public function done( string $requestid ):ASResult{

        return $this->status($requestid,'done');

    }

    // 拒绝表单
    public function reject( string $requestid ):ASResult{

        return $this->status($requestid,'rejected');

    }

    // 取消表单
    public function cancel( string $requestid ):ASResult{

        return $this->status($requestid,'canceled');

    }

    // 完成退款

    public function refunded( string $requestid ):ASResult{

        return $this->status($requestid,'refunded');

    }



    public static $table     = "form_request";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'uid',
        'areaid',
        'userid',
        'itemtype',
        'itemid',
        'open',
        'form',
        'featured',
        'expire',
        'status',
        'applycall',
        'rejectcall',
    ];      // 添加支持字段
    public static $updateFields = [
        'itemtype',
        'itemid',
        'open',
        'featured',
        'status',
        'applycall',
        'rejectcall',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'uid',
        'areaid',
        'userid',
        'itemtype',
        'itemid',
        'open',
        'form',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ]; // 概览支持字段
    public static $listFields = [
        'uid',
        'areaid',
        'userid',
        'itemtype',
        'itemid',
        'open',
        'form',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ];     // 列表支持字段
    public static $countFilters = [
        'uid',
        'areaid',
        'userid',
        'itemtype',
        'itemid',
        'open',
        'featured',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'form'=>'ASJson',
        'open'=>'int',
        'featured'=>'int',
        'expire'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'applycall'=>'ASJson',
        'rejectcall'=>'ASJson'
    ];

}