<?php

namespace think\exception;

class ValidateException extends \RuntimeException
{
    protected $error;

    public function __construct($error)
    {
        $this->error   = $error;
        $this->message = is_array($error) ? implode("\n\r", $error) : $error;
    }

    /**
     * 获取验证错误信息
     * @access public
     * @return array|string
     */
    public function getError()
    {
        return $this->error;
    }
}
