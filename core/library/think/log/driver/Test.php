<?php

namespace think\log\driver;

/**
 * 模拟测试输出
 */
class Test
{
    /**
     * 日志写入接口
     * @access public
     * @param array $log 日志信息
     * @return bool
     */
    public function save(array $log = [])
    {
        return true;
    }

}
