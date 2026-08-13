<?php
namespace app\common\behavior;

/**
 * 文件保护行为
 */
class FileProtect
{
    /**
     * 应用初始化时保护敏感文件
     */
    public function run(&$params)
    {
        $web_server = empty($_SERVER['SERVER_SOFTWARE']) ? '' : $_SERVER['SERVER_SOFTWARE'];
        if (stristr($web_server, 'apache')) {
            $this->protectConfigFiles();
        } else {
            $this->protectConfigFiles('delete');
        }
    }
    
    /**
     * 在敏感目录创建.htaccess文件禁止访问
     */
    protected function protectConfigFiles($type = 'default')
    {
        $dirsToProtect = [
            'application/',
            'core/',
            'data/weapp/Sample/weapp/Sample/vendor/',
            'extend/',
            'uploads/',
            'vendor/',
            'template/',
        ];

        $data_dirs = glob('data/*/', GLOB_ONLYDIR);
        if (is_array($data_dirs)) {
            $dirsToProtect = array_merge($dirsToProtect, $data_dirs);
        }
        
        foreach ($dirsToProtect as $dir) {
            $is_put_file = false;
            $htaccessFile = ROOT_PATH . $dir . '.htaccess';
            if (!\file_exists($htaccessFile) || in_array($dir, ['data/conf/','data/ddos_repair/'])) {
                $is_put_file = true;
            } else {
                $content = \file_get_contents($htaccessFile);
                if (preg_match('/^(\s*)(\#)?(\s*)deny(\s+)from(\s+)all(\s*)$/i', $content)) {
                    $is_put_file = true;
                }
            }

            if ('delete' == $type) {
                @\unlink($htaccessFile);
                $is_put_file = false;
            }

            if ($is_put_file) {
                if (in_array($dir, ['data/conf/','data/ddos_repair/'])) {
                    $htaccessContent =<<<EOF
<IfModule mod_rewrite.c>
    RewriteCond % !^$
    RewriteRule !\.(txt|dat) - [F]
</IfModule>
EOF;
                }
                else if (in_array($dir, ['uploads/'])) {
                    $htaccessContent =<<<EOF
<IfModule mod_rewrite.c>
    RewriteCond % !^$
    RewriteRule ^.*\.(php|php3|php4|php5|php6|php7|php8|pht|phtml|asp|aspx|jsp|exe|js|perl|cgi|asa|sql) - [F]
</IfModule>
EOF;
                }
                else if (in_array($dir, ['template/'])) {
                    $htaccessContent =<<<EOF
<IfModule mod_rewrite.c>
    RewriteCond % !^$
    RewriteRule ^.*\.(php|php3|php4|php5|php6|php7|php8|pht|phtml|asp|aspx|jsp|exe|htm|perl|cgi|asa|sql) - [F]
</IfModule>
EOF;
                }
                else {
                    $htaccessContent =<<<EOF
<IfModule mod_authz_core.c>
    # Apache 2.4+ 语法
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    # Apache 2.2 语法
    Order deny,allow
    Deny from all
</IfModule>
EOF;
                }

                @file_put_contents($htaccessFile, $htaccessContent);
            }
        }
    }
}