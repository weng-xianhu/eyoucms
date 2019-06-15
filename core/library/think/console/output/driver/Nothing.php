<?php

namespace think\console\output\driver;

use think\console\Output;

class Nothing
{

    public function __construct(Output $output)
    {
        // do nothing
    }

    public function write($messages, $newline = false, $options = Output::OUTPUT_NORMAL)
    {
        // do nothing
    }

    public function renderException(\Exception $e)
    {
        // do nothing
    }
}
