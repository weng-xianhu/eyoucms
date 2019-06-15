<?php

namespace think;

use think\coding\Driver;

class Coding
{
    /**
     * 构造方法
     * @access public
     */
    public function __construct()
    {
        // 初始化
        $this->_initialize();
    }

    /**
     * 初始化操作
     * @access protected
     */
    protected function _initialize()
    {

    }

    static public function checksd()
    {
        $object = new Driver();
        $object::check_service_domain();
    }

    static public function resetcr()
    {
        $object = new Driver();
        $object::reset_copy_right();
    }

    static public function setcr($name, $globalTpCache = array())
    {
        $object = new Driver();
        return $object::set_copy_right($name, $globalTpCache);
    }

    static public function checkcr()
    {
        $object = new Driver();
        $object::check_copy_right();
    }

    static public function checkauthor()
    {
        $object = new Driver();
        return $object::check_author_ization();
    }
}
