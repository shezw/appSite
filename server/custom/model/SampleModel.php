<?php

namespace APS;

/**
 * 一个模型的示范类
 * A Model Class sample
 * @package APS\custom\model
 */
class SampleModel extends ASModel{

    public static $table = 'item_sample';
    protected static $addFields = [
        'sampleid','title','gallery','status','createtime','sort','featured'
    ];
    protected static $updateFields = [
        'title','gallery','status','lasttime','sort','featured'
    ];
    protected static $detailFields = '*';
    protected static $overviewFields = [
        'sampleid','title','status','createtime','lasttime','sort','featured'
    ];
    protected static $countFilters = [
        'sampleid','status','createtime','lasttime','sort','featured'
    ];
    protected static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'sort'=>'int',
        'featured'=>'int',
        'gallery'=>'ASJson'
    ];

}