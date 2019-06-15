<?php

namespace think\model;

use think\Model;

class Pivot extends Model
{

    /** @var Model */
    public $parent;

    protected $autoWriteTimestamp = false;

    /**
     * 架构函数
     * @access public
     * @param array|object  $data 数据
     * @param Model         $parent 上级模型
     * @param string        $table 中间数据表名
     */
    public function __construct($data = [], Model $parent = null, $table = '')
    {
        $this->parent = $parent;

        if (is_null($this->name)) {
            $this->name = $table;
        }

        parent::__construct($data);
    }

}
