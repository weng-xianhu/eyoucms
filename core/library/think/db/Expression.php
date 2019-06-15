<?php

namespace think\db;

class Expression
{
    /**
     * 查询表达式
     *
     * @var string
     */
    protected $value;

    /**
     * 创建一个查询表达式
     *
     * @param  string  $value
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * 获取表达式
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
