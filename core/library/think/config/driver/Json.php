<?php

namespace think\config\driver;

class Json
{
    public function parse($config)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }
        $result = json_decode($config, true);
        return $result;
    }
}
