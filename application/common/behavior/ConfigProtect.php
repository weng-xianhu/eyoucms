<?php
namespace app\common\behavior;

use think\Request;

/**
 * 敏感配置文件保护行为
 */
class ConfigProtect
{
    /**
     * 应用初始化行为
     */
    public function run(&$params)
    {
        // 保护敏感配置目录
        $this->protectConfigDir();
        
        // 生产环境屏蔽错误显示
        if ($this->isProduction()) {
            $this->disableErrorDisplay();
        }
    }
    
    /**
     * 保护配置目录访问
     */
    protected function protectConfigDir()
    {
        $request = Request::instance();
        $path = $request->path();
        
        // 检查是否尝试访问敏感目录
        $sensitivePatterns = [
            '#^data/conf/#i',
            '#^data/runtime#i',
            '#^application/database.php#i',
            '#^application/config.php#i'
        ];
        
        foreach ($sensitivePatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                header('HTTP/1.1 403 Forbidden');
                exit('Access Denied');
            }
        }
    }
    
    /**
     * 判断是否为生产环境
     * 
     * @return bool
     */
    protected function isProduction()
    {
        return config('app_debug') === false;
    }
    
    /**
     * 禁用错误显示
     */
    protected function disableErrorDisplay()
    {
        @ini_set('display_errors', 0);
        // error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    }
}