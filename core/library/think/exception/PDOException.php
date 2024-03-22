<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------

namespace think\exception;

use think\Config;

/**
 * PDO异常处理类
 * 重新封装了系统的\PDOException类
 */
class PDOException extends DbException
{
    /**
     * PDOException constructor.
     * @param \PDOException $exception
     * @param array         $config
     * @param string        $sql
     * @param int           $code
     */
    public function __construct(\PDOException $exception, array $config, $sql, $code = 10501)
    {
        $error = $exception->errorInfo;
        $code0 = $exception->getCode();
        $code1 = isset($error[1]) ? $error[1] : 0;
        $code2 = isset($error[2]) ? $error[2] : '';

        $this->setData('PDO Error Info', [
            'SQLSTATE'             => $code0,
            'Driver Error Code'    => $code1,
            'Driver Error Message' => $code2,
        ]);

        /*提高错误提示的友好性 by 小虎哥*/
        $errcode = "{$code0}:{$code1}";
        if (stristr($code2, "Incorrect string value:")) {
            if (stristr($code2, "for column '")) {
                $errcode = "HY000:-{$code1}";
            }
        }
        $mysqlcode = Config::get('error_code.mysql');
        $eyou_message = "";
        if (!empty($mysqlcode[$errcode])) {
            $code = 'eyou';
            $eyou_message = $mysqlcode[$errcode];
        }
        /*--end*/

        $message = $exception->getMessage();
        try {
            $message = iconv('GB2312', 'UTF-8', $message); // 转化编码 by 小虎哥
        } catch (\Exception $e) {
            if (function_exists('mb_convert_encoding')) {
                $message = mb_convert_encoding($message, "UTF-8", "GBK");
            }
        }
        // 新增/更新时，字段超过长度报错
        if (stristr($message, 'Data too long for column ')) {
            $table = '';
            if (preg_match('/^INSERT(\s+)INTO(\s+)`([\w\-]+)`(\s+)(.*)$/i', $sql)) {
                $table = preg_replace('/^INSERT(\s+)INTO(\s+)`([\w\-]+)`(\s+)(.*)$/i', '${3}', $sql);
            } else if (preg_match('/^UPDATE(\s+)`([\w\-]+)`(\s+)SET(\s+)`(.*)$/i', $sql)) {
                $table = preg_replace('/^UPDATE(\s+)`([\w\-]+)`(\s+)SET(\s+)`(.*)$/i', '${2}', $sql);
            }
            if (!empty($table) && !stristr($table, '`')) {
                $data = \think\Db::query("show create table " . $table);
                $sqlInfo = empty($data[0]['Create Table']) ? '' : $data[0]['Create Table'];
                if (!empty($sqlInfo)) {
                    $fieldName = preg_replace('/^(.*)Data too long for column (\'|")([^\'\"]+)(\'|") at row (.*)$/i', '${3}', $message);
                    $fieldTitle = preg_replace('/^([\s\S]+)`'.$fieldName.'`(\s+)(.*)(\s+)COMMENT(\s+)(\'|")(.*)(\'|")\,([\s\S]+)$/i', '${7}', $sqlInfo);
                    $fieldLength = preg_replace('/^([\s\S]+)`'.$fieldName.'`(\s+)([a-z]+)\((\d+)([\s\S]+)$/i', '${4}', $sqlInfo);
                    $eyou_message = $fieldTitle.$eyou_message.$fieldLength.'字符';
                }
            }
        }
        $message = $eyou_message."#--wrap--#".$message;

        parent::__construct($message, $config, $sql, $code);
    }
}
