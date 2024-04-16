<?php
    if(!function_exists('parse_padding')){
        function parse_padding($source)
        {
            $length  = strlen(strval(count($source['source']) + $source['first']));
            return 40 + ($length - 1) * 8;
        }
    }

    if(!function_exists('parse_class')){
        function parse_class($name)
        {
            $names = explode('\\', $name);
            return '<abbr title="'.$name.'">'.end($names).'</abbr>';
        }
    }

    if(!function_exists('parse_file')){
        function parse_file($file, $line)
        {
            /*提高体验 by 小虎哥*/
            $rootPath = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
            if (!stristr($file, $rootPath)) {
                $rootPath = dirname($rootPath);
            }
            $filestr = str_replace($rootPath, '', $file);
            return $filestr." 第 {$line} 行左右";
            /*--end*/
        }
    }

    if(!function_exists('parse_args')){
        function parse_args($args)
        {
            $result = [];

            foreach ($args as $key => $item) {
                switch (true) {
                    case is_object($item):
                        $value = sprintf('<em>object</em>(%s)', parse_class(get_class($item)));
                        break;
                    case is_array($item):
                        if(count($item) > 3){
                            $value = sprintf('[%s, ...]', parse_args(array_slice($item, 0, 3)));
                        } else {
                            $value = sprintf('[%s]', parse_args($item));
                        }
                        break;
                    case is_string($item):
                        if(strlen($item) > 20){
                            $value = sprintf(
                                '\'<a class="toggle" title="%s">%s...</a>\'',
                                htmlentities($item),
                                htmlentities(substr($item, 0, 20))
                            );
                        } else {
                            $value = sprintf("'%s'", htmlentities($item));
                        }
                        break;
                    case is_int($item):
                    case is_float($item):
                        $value = $item;
                        break;
                    case is_null($item):
                        $value = '<em>null</em>';
                        break;
                    case is_bool($item):
                        $value = '<em>' . ($item ? 'true' : 'false') . '</em>';
                        break;
                    case is_resource($item):
                        $value = '<em>resource</em>';
                        break;
                    default:
                        $value = htmlentities(str_replace("\n", '', var_export(strval($item), true)));
                        break;
                }

                $result[] = is_int($key) ? $value : "'{$key}' => {$value}";
            }

            return implode(', ', $result);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo \think\Lang::get('System Error'); ?></title>
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <style>
            *{ padding: 0; margin: 0; }
            *{-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
            body{ background: #fff; font-family: '微软雅黑'; color: #CCC; font-size: 16px; }
            .system-message{ padding: 24px 30px; margin:auto; border: #e8e8e8 1px solid; top:50%; width: auto; max-width:640px; background-color: #fff;box-shadow: 0 0 8px rgba(0,0,0,0.1);border-radius: 4px;overflow: hidden; }
            .system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 5px; }
            .system-message .jump{ padding-top: 10px; color: #999;}
            .system-message .success,.system-message .error{ line-height: 1.8em;  color: #999; font-size: 36px; font-family: '黑体'; }
            .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
            .system-message .tit{position: relative;width: 100%;padding-bottom: 10px;border-bottom: 1px solid #eee;}
            .system-message .tit i{position: absolute;font-size: 26px;color: #53bb4c;}
            .system-message .tit b{margin: 0 15px 0 25px;font-weight: normal;font-size: 18px;color: #53bb4c;}
            .system-message .tit .tishi1{display: block;font-size: 14px;color: #999;margin-bottom: 15px;}
            .system-message .tit .tishi2{display: block;font-size: 20px;color: #999;margin-bottom: 20px;}
            .system-message .tit span{display: block;font-size: 14px;color: #999;margin-bottom: 15px;}
            .system-message ul{margin: 10px auto 0 auto; overflow: hidden;}
            .system-message ul li{float: right;list-style: none;margin:5px 18px 5px 0;}
            .system-message ul li a{color: #337ab7;text-decoration: none;}
            .system-message .buttom{margin: 10px auto; width: 100%; text-align: center; line-height: 40px; color: red;}
            @media (max-width: 640px) {
                .system-message{width:100%;border:none;box-shadow:none;}
                .system-message .tit {border-bottom: none;}
            }
    </style>
</head>
<body>
    <?php
        $message = 'eyou' == $code ? nl2br($message) : nl2br(htmlentities($message));
        $message_arr = explode('#--wrap--#', $message);
    ?>

    <!-- <?php echo $echo;?> -->
    
    <div class="system-message" style="margin-top: 223.333px;">
        <div class="tit">
            <?php echo empty($message_arr[0]) ? '' : '<span class="tishi2">'.$message_arr[0].'</span>'; ?>
            <?php echo empty($message_arr[1]) ? '' : '<span>[错误代码]'.$message_arr[1].'</span>'; ?>
            <?php echo '<span class="tishi1">'.sprintf('报错 %s', parse_file($file, $line)).'</span>'; ?>
        </div>
        <ul>
            <li><a href="javascript:history.back(-1)">返回</a></li>
        </ul>
    </div>
    
</body>
</html>
