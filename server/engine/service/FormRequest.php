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


    const table     = "form_request";
    const comment   = '表单-申请';
    const primaryid = "uid";
    const addFields = [
        'uid', 'saasid', 'userid', 'itemtype', 'itemid',
        'open',
        'form',
        'expire',
        'status',
        'applycall', 'rejectcall',
    ];
    const updateFields = [
        'itemtype', 'itemid',
        'open',
        'status',
        'applycall', 'rejectcall',
    ];
    const detailFields = [
        'uid', 'saasid', 'userid', 'itemtype', 'itemid',
        'open',
        'form',
        'expire',
        'applycall', 'rejectcall',
        'status', 'createtime', 'lasttime',
    ];
    const overviewFields = [
        'uid', 'saasid', 'userid', 'itemtype', 'itemid',
        'open',
        'form',
        'status', 'createtime', 'lasttime',
    ];
    const listFields = [
        'uid', 'saasid','userid','itemtype', 'itemid',
        'open',
        'form',
        'expire',
        'status', 'createtime', 'lasttime',
    ];
    const filterFields = [
        'uid', 'saasid', 'userid', 'itemtype', 'itemid',
        'open',
        'status', 'createtime', 'lasttime',
    ];
    const depthStruct = [
        'form'=>DBField_Json,
        'open'=>DBField_Boolean,
        'expire'=>DBField_TimeStamp,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'applycall'=>DBField_Json,
        'rejectcall'=>DBField_Json
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID' , 'idx'=>DBIndex_Unique ],
        'saasid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],

        'userid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'itemid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'目标ID' , 'idx'=>DBIndex_Index ],
        'itemtype'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'目标类别' , 'idx'=>DBIndex_Index ],

        'open'=>        ['type'=>DBField_Boolean,   'len'=>1,   'nullable'=>0,  'cmt'=>'是否公开' , 'dft'=>0,       ],
        'form'=>        ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'表单内容 JSON 不限制' ],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'pending',        ],
        // enabled 开启, disabled 关闭, rejected 被拒绝, pending 等待中, applied 通过

        'expire'=>      ['type'=>DBField_TimeStamp, 'len'=>13,  'nullable'=>0,  'cmt'=>'过期时间 时间戳' ,     'dft'=>0,       ],
        'applycall'=>   ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'通过回调 k-v json' ],
        'rejectcall'=>  ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'退款回调' ],
        // 回调函数最外层必须是 queue数组 [{},{},{},{}] or [{}]
        // eg: {{'action':'setvip','params':{'userid':'xxx','expire':158989990}},{'action':'newPromotion','params':{'userid':'xxx','amount':8.88}}
        // 通过回调函数激活支付成功后进行的事件

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

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

            $UPDATE = $this->update(DBValues::init('applyCall')->stringIf($callback->getContent()),$requestid);

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

                $this->update(DBValues::init('rejectcall')->stringIf($callback->getContent()),$requestid);
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

        $count = $this->getDB()->count(static::table,static::uidCondition($requestid))->getContent();
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


}