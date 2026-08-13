<?php
/**
 * ZanCms
 * ============================================================================
 * 版权所有 2020-2035 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.zancms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\admin\controller;

use think\Db;

class Robots extends Base
{
    public function _initialize() {
        parent::_initialize();
        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(5);
        $this->language_access(); // 多语言功能操作权限
    }

    /*
     * robots上传
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $param = [];
            $param['robots_mode'] = (int)$post['robots_mode'];

            /*多语言*/
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('robots', $param, $val['mark']);
            }
            /*--end*/

            $bool = false;
            clearstatcache(); // 清除文件夹权限缓存
            $filepath = ROOT_PATH."robots.txt";
            $fp = @fopen($filepath, "w+");
            if (is_writeable($filepath) && !empty($fp)) {
                if (false !== fwrite($fp, $post['robots_content'])) {
                    $bool = true;
                    fclose($fp);
                } else {
                    if (false !== file_put_contents( $filepath, $post['robots_content'] )) {
                        $bool = true;
                    }
                }
            }

            if ($bool) {
                $lang = Db::name('language')->where(['is_home_default'=>1])->value('mark');
                sitemap_all('all', [$lang]);
                $this->success('操作成功', url('Robots/edit'));
            }
            $this->error('文件 robots.txt 没有读写权限');
        }

        $assign_data = [];
        $assign_data['robots_content_1'] =<<<EOF
User-agent: *
Disallow: /
EOF;
        $assign_data['robots_content_2'] =<<<EOF
User-agent: *
Allow: /
EOF;
        $assign_data['robots_content_3'] =<<<EOF
User-agent: *
Allow: /
User-agent: BaiduSpider
Disallow: /
User-Agent: Sosospider
Disallow: /
User-Agent: YoudaoBot
Disallow: /
User-Agent: Sogou Spider 
Disallow: /
EOF;
        $assign_data['robots_content_4'] =<<<EOF
User-agent: *
Allow: /
User-agent: googlebot
Disallow: /
EOF;
        $web_basehost = preg_replace('/^(([^\:\.]+):)?(\/\/)?([^\/\:]*)(.*)$/i', '${1}${3}${4}'.ROOT_DIR, $this->globalConfig['web_basehost']);
        $assign_data['robots_content_5'] =<<<EOF
User-agent: *
Allow: /

# 允许AI爬虫访问
User-agent: GPTBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: CCBot
Allow: /

User-agent: Claude-Web
Allow: /

User-agent: anthropic-ai
Allow: /

User-agent: Baiduspider
Allow: /

# 国内主要AI大模型爬虫
User-agent: ByteSpider
Allow: /

User-agent: YiSouSpider
Allow: /

User-agent: GLMBot
Allow: /

User-agent: ChatGLM
Allow: /

User-agent: QihooBot
Allow: /

User-agent: SenseBot
Allow: /

User-agent: BaiduBoxapp
Allow: /

User-agent: TongYiBot
Allow: /

User-agent: SparkBot
Allow: /

User-agent: KimiBot
Allow: /

User-agent: WenxinBot
Allow: /

User-agent: DoubaoBot
Allow: /

User-agent: MoonshotBot
Allow: /

User-agent: DeepSeekBot
Allow: /

User-agent: MinimaxBot
Allow: /

# 禁止访问敏感目录
Disallow: /data/
Disallow: /install_*
Disallow: /core/
Disallow: /application/
Disallow: /vendor/

# 网站地图位置
Sitemap: {$web_basehost}/sitemap.xml

# AI助手专用页面
AI Content Index: {$web_basehost}/ai-content-index.html
EOF;
        $file_robots_content = @file_get_contents(ROOT_PATH.'robots.txt');
        if (empty($file_robots_content)) {
            $file_robots_content = '';
        }
        $this->globalConfig['robots_content'] = $file_robots_content;
        if ($assign_data['robots_content_1'] == $this->globalConfig['robots_content']) {
            $this->globalConfig['robots_mode'] = 1;
        } else if ($assign_data['robots_content_2'] == $this->globalConfig['robots_content']) {
            $this->globalConfig['robots_mode'] = 2;
        } else if ($assign_data['robots_content_3'] == $this->globalConfig['robots_content']) {
            $this->globalConfig['robots_mode'] = 3;
        } else if ($assign_data['robots_content_4'] == $this->globalConfig['robots_content']) {
            $this->globalConfig['robots_mode'] = 4;
        } else if ($assign_data['robots_content_5'] == $this->globalConfig['robots_content']) {
            $this->globalConfig['robots_mode'] = 5;
        } else {
            $this->globalConfig['robots_mode'] = 0;
        }
        $assign_data['global'] = $this->globalConfig;

        $this->assign($assign_data);
        return $this->fetch();
    }
}