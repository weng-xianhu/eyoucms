<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\api\logic;

use think\Db;
use think\Model;
use app\admin\logic\DdosLogic;

class DdosApiLogic extends Model
{
    public $ddosLogic;

    /*
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();
        $this->ddosLogic = new DdosLogic;
    }

    /**
     * 自动定期扫描
     * @return [type] [description]
     */
    public function ddos_auto_scan()
    {
        @ini_set('memory_limit', '-1');
        function_exists('set_time_limit') && set_time_limit(0);

        $ddos_auto_scan_file = tpSetting('ddos.ddos_auto_scan_file', [], 'cn');
        if (empty($ddos_auto_scan_file) || MyDate('Y-m-d', $ddos_auto_scan_file) != MyDate('Y-m-d', getTime())) {
            // 命令行执行的php脚本，不需要判断接口安全性
            if (IS_CLI) {
                $admin_id = Db::name('admin')->where(['parent_id'=>0, 'role_id'=>-1])->order('admin_id asc')->value('admin_id');
            } else {
                $hash = input('param.hash/s');
                $vars = json_decode(mchStrCode($hash, 'DECODE'), true);
                if (empty($vars['user_name'])) {
                    return ['code'=>0, 'msg'=>'接口不合法'];
                } else {
                    $admin_id = 0;
                    $admin_list = Db::name('admin')->where(['admin_id'=>['gt', 0]])->select();
                    foreach ($admin_list as $key => $val) {
                        if ($vars['user_name'] == md5($val['user_name'])) {
                            $admin_id = $val['admin_id'];
                            break;
                        }
                    }
                    if (empty($admin_id)) {
                        return ['code'=>0, 'msg'=>'接口不合法'];
                    }
                }
            }

            $this->ddosLogic->set_admin_id($admin_id);
            // 只扫描系统文件
            $setData = [
                'ddos_scan_range_files' => 1,
                'ddos_scan_range_attachment' => 0,
                'ddos_scan_range_uploads' => 0,
                'ddos_scan_is_finish' => 0,
                'ddos_scan_allscantotal' => 0,
                'ddos_is_auto_scan' => 1,
            ];
            tpSetting('ddos', $setData, 'cn');
            // 重置ddos_log表
            $this->ddosLogic->ddos_log_reset();
            // 病毒特征库
            $this->ddosLogic->feature_init('auto_scan');
            // 整理文件列表
            list($list, $eyoufilelist) = $this->ddos_arrange_files();
            if (empty($eyoufilelist)) {
                return ['code'=>0, 'msg'=>'连接官方扫描接口失败，请尝试'];
                // return ['code'=>0, 'msg'=>'文件 data/conf/eyoufilelist.txt 没有读写权限'];
            }
            // 逐个检查扫描的文件
            $data = $this->ddosLogic->ddosInspectFiles($list, 0, 0, 0, 0, 0, ['opt_type'=>'auto_scan']);
            if (!empty($data) && empty($data['msg'])) {
                $setData = [
                    'ddos_scan_range_files' => 1,
                    'ddos_scan_range_attachment' => 0,
                    'ddos_scan_range_uploads' => 0,
                    'ddos_scan_is_finish' => 2, // 扫描完成
                    'ddos_scan_allscantotal' => $data['allscantotal'],
                    'ddos_is_auto_scan' => 1,
                    'ddos_scan_last_time' => getTime(),
                    'ddos_auto_scan_file' => getTime(),
                ];
                tpSetting('ddos', $setData, 'cn');

                tpCache('system', ['system_explanation_welcome_5'=>0]);

                if (!empty($data['doubtotal'])) {
                    $this->send_email();
                }
            }
            @unlink(DATA_PATH.'conf/eyoufilelist.txt');
            return ['code'=>1, 'msg'=>'扫描完成'];
        }
        else {
            $msg = "上一次扫描：".MyDate('Y-m-d H:i:s', $ddos_auto_scan_file)."，请等待定时任务，不要频繁扫描";
            return ['code'=>1, 'msg'=>$msg];
        }
    }

    /**
     * 整理文件列表
     * @return [type] [description]
     */
    private function ddos_arrange_files()
    {
        delFile(DATA_PATH.'runtime/cache/', false);
        delFile(DATA_PATH.'runtime/temp/', false);
        delFile(DATA_PATH.'schema/', false, ['.htaccess']);
        delFile(DATA_PATH.'backup/', false, ['.htaccess']);
        // 重新生成数据表结构
        if (function_exists('schemaAllTable')) schemaAllTable();
        // 清除session过期文件
        if (function_exists('clear_session_file')) clear_session_file();
        // 生成语言包文件
        if (file_exists('application/common/model/ForeignPack.php')) {
            $foreignPack = new \app\common\model\ForeignPack;
            $foreignPack->updateLangFile();
        }
        // 全部扫描系统文件
        list($list, $eyoufilelist) = $this->scan_file_logic('all');
        
        return [$list, $eyoufilelist];
    }

    private function scan_file_logic($scan_type = 'all')
    {
        $list = [];
        $eyoufilelist = [];
        // 获取官方对应版本的文件列表
        list($eyoufilelist) = $this->ddosLogic->ddos_eyou_source_files('auto_scan');
        if (empty($eyoufilelist)) {
            return [$list, $eyoufilelist];
        }
        
        // 全部扫描系统文件
        if ('all' == $scan_type) {
            if (IS_CLI) {
                $dir = ROOT_PATH;
            } else {
                // Win 环境
                if (IS_WIN) {
                    $dir = APP_PATH.'../';
                }
                // 非 Win 环境
                else {
                    $dir = ROOT_PATH;
                }
            }
            if (!is_readable($dir)) {
                $dir = str_replace('\\', '/', $dir);
                $dir = rtrim($dir, '/').'/';
            }

            // 递归读取文件夹
            $list[] = '/';
            $this->ddosLogic->ddos_getDir($dir, '', $list, ['uploads', 'public/upload', 'upload', 'runtime/cache']);
            // 获取要扫描目录的文件
            $filesData = $this->ddosLogic->getScanFilesData(0, 0, 'auto_scan', $list);
            $list = $filesData['info'];
        }
        // 只扫描核心校验的文件
        else {
            // 获取指定扫描的文件
            $web_adminbasefile = tpCache('global.web_adminbasefile');
            $web_adminbasefile = !empty($web_adminbasefile) ? preg_replace('/^((.*)\/)?([^\/]+)$/i', '${3}', $web_adminbasefile) : 'login.php';
            $list = [
                md5('/') => [
                    'dir' => '/',
                    'files' => [
                        $web_adminbasefile,
                    ],
                ],
            ];
            foreach ($eyoufilelist as $key => $val) {
                $arr = explode('|', $val);
                if (!empty($arr[1]) && file_exists($arr[0])) {
                    if ('login.php' == $arr[0]) {
                        continue;
                    }
                    if (stristr($arr[0], '/')) {
                        $dir = preg_replace('/^(.+)\/([^\/]+)$/i', '${1}', $arr[0]);
                    } else {
                        $dir = '/';
                    }
                    $list[md5($dir)]['dir'] = $dir;
                    if (empty($list[md5($dir)]['files']) || !in_array($arr[0], $list[md5($dir)]['files'])) {
                        $list[md5($dir)]['files'][] = $arr[0];
                    }
                }
            }
            $list = array_values($list);
        }

        return [$list, $eyoufilelist];
    }

    /**
     * 发送邮箱
     * @return [type] [description]
     */
    private function send_email()
    {
        // 满足发送邮箱的条件
        $smtp_config = tpCache('smtp');
        if (!empty($smtp_config['smtp_user']) && !empty($smtp_config['smtp_pwd'])) {
            $email = empty($smtp_config['smtp_from_eamil']) ? $smtp_config['smtp_user'] : $smtp_config['smtp_from_eamil'];
            // 判断标题拼接
            $time = getTime();
            $title = '安全中心定期扫描风险提醒';
            $web_name = '网站名称：'.tpCache('web.web_name');
            $web_basehost = preg_replace('/^(([^\:\.]+):)?(\/\/)?([^\/\:]*)(.*)$/i', '${4}', config('tpcache.web_basehost'));
            if (!empty($web_basehost)) {
                $host_port = !stristr($website_host, ':') ? '' : request()->port();
                $website_host = $web_basehost;
                if (!empty($host_port) && !stristr($website_host, ':')) {
                    $website_host .= ":{$host_port}";
                }
                $website_host = request()->scheme() . '://' . $website_host;
            } else {
                $website_host = request()->domain();
            }
            $web_basehost = '网站地址：'.$website_host.ROOT_DIR;
            $content = "经网站后台定期扫描，发现目前你的网站存在一定的安全风险，请尽快前往网站后台手工处理。<br/>";
            $html =<<<EOF
<p style='text-align: left;'>{$web_name}</p>
<p style='text-align: left;'>{$web_basehost}</p>
<p style='text-align: left;'>{$content}</p>
<p style='text-align: left;'>
    <img src='https://www.eyoucms.com/uploads/allimg/20250423/1008-2504230SJ4556.png?{$time}' />
</p>
EOF;
            // 发送邮件
            try {
                send_email($email, $title, $html, 0, $smtp_config);
            } catch (\Exception $e) {
                
            }
        }
    }
}