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

namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\Db;
use think\Loader;

/**
 * 自动定期扫描专用入口
 */
class DdosAutoScan extends Command
{
    protected function configure()
    {
        // $this->setName('ddos_auto_scan')->setDescription('自动定期扫描');
        $this
            // 设置命令名称
            ->setName('ddos_auto_scan')
            // 添加第一个参数（action），它是可选的，并带有描述
            ->addArgument('action', Argument::OPTIONAL, "action start [d]|stop|restart|status")
            // 添加第二个参数（type），它也是可选的，并带有描述 
            ->addArgument('type', Argument::OPTIONAL, "d -d")
            // 设置命令描述
            ->setDescription('自动定期扫描');
    }
    
    protected function execute(Input $input, Output $output)
    {
        global $argv;

        // 获取输入参数
        $action = trim($input->getArgument('action'));
        $type = trim($input->getArgument('type'));
        $type = $type ? '-d' : '';
  
        // 根据不同的action执行不同的操作
        switch ($action) {
            case 'start':
                // 处理启动逻辑
                $output->writeln('Starting Scan service...');
                if ($type === '-d') {
                    $output->writeln('Running in daemon mode...');
                }
                break;
            case 'stop':
                // 处理停止逻辑
                $output->writeln('Stopping Scan service...');
                break;
            case 'restart':
                // 处理重启逻辑
                $output->writeln('Restarting Scan service...');
                break;
            case 'reload':
                // 处理平滑重启逻辑
                $output->writeln('Reloading Scan service...');
                break;
            case 'status':
                // 处理状态检查逻辑
                $output->writeln('Checking Scan service status...');
                break;
            default:
                // 处理无效action
                $output->writeln('Invalid action. Please use one of: start, stop, restart, reload, status.');
        }

        $argv[0] = 'think ddos_auto_scan';
        $argv[1] = $action;
        $argv[2] = $type;

        // 运行服务
        $redata = $this->ddos_auto_scan();
        $msg = $redata['msg'];
        $output->writeln($msg);
    }

    /**
     * 自动定期扫描
     * @return [type] [description]
     */
    public function ddos_auto_scan()
    {
        $ddosApiLogic = new \app\api\logic\DdosApiLogic;
        $redata = $ddosApiLogic->ddos_auto_scan();
        return $redata;
    }
}
