<?php
namespace app\model;
use app\YunzhiModel;

class ModelModel extends YunzhiModel
{
    // 自动时间戳
    protected $autoWriteTimestamp = true;

    protected $type = [
        'config'    => 'json',
        'filter'    => 'json',
        'param'     => 'json',
    ];
}