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

if (!function_exists('convert_arr_key')) 
{
    /**
     * 将数据库中查出的列表以指定的 id 作为数组的键名 
     *
     * @param array $arr 数组
     * @param string $key_name 数组键名
     * @return array
     */
    function convert_arr_key($arr, $key_name)
    {
        $arr2 = array();
        foreach($arr as $key => $val){
            $arr2[$val[$key_name]] = $val;        
        }
        return $arr2;
    }
}

if (!function_exists('func_encrypt')) 
{
    /**
     * md5加密 
     *
     * @param string $str 字符串
     * @return array
     */
    function func_encrypt($str){
        $auth_code = tpCache('system.system_auth_code');
        if (empty($auth_code)) {
            $auth_code = \think\Config::get('AUTH_CODE');
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('system', ['system_auth_code'=>$auth_code], $val['mark']);
                }
            } else { // 单语言
                tpCache('system', ['system_auth_code'=>$auth_code]);
            }
            /*--end*/
        }

        return md5($auth_code.$str);
    }
}
   
if (!function_exists('get_arr_column')) 
{         
    /**
     * 获取数组中的某一列
     *
     * @param array $arr 数组
     * @param string $key_name  列名
     * @return array  返回那一列的数组
     */
    function get_arr_column($arr, $key_name)
    {
        $arr2 = array();
        foreach($arr as $key => $val){
            $arr2[] = $val[$key_name];        
        }
        return $arr2;
    }
}

if (!function_exists('parse_url_param')) 
{  
    /**
     * 获取url 中的各个参数  类似于 pay_code=alipay&bank_code=ICBC-DEBIT
     * @param string $url
     * @return type
     */
    function parse_url_param($url = ''){
        $url = rtrim($url, '/');
        $data = array();
        $str = explode('?',$url);
        $str = end($str);
        $parameter = explode('&',$str);
        foreach($parameter as $val){
            if (!empty($val)) {
                $tmp = explode('=',$val);
                $data[$tmp[0]] = $tmp[1];
            }
        }
        return $data;
    }
}

if (!function_exists('clientIP')) 
{  
    /**
     * 客户端IP
     */
    function clientIP() {
        $ip = request()->ip();
        if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))          
            return $ip;
        else
            return '';
    }
}

if (!function_exists('serverIP')) 
{  
    /**
     * 服务器端IP
     */
    function serverIP(){   
        return gethostbyname($_SERVER["SERVER_NAME"]);   
    }  
}

if (!function_exists('recurse_copy')) 
{  
    /**
     * 自定义函数递归的复制带有多级子目录的目录
     * 递归复制文件夹
     *
     * @param type $src 原目录
     * @param type $dst 复制到的目录
     */                        
    //参数说明：            
    //自定义函数递归的复制带有多级子目录的目录
    function recurse_copy($src, $dst)
    {
        $now = getTime();
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== $file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    if (file_exists($dst . DIRECTORY_SEPARATOR . $file)) {
                        if (!is_writeable($dst . DIRECTORY_SEPARATOR . $file)) {
                            // exit($dst . DIRECTORY_SEPARATOR . $file . '不可写');
                            return '网站目录没有写入权限，请调整权限';
                        }
                        // @unlink($dst . DIRECTORY_SEPARATOR . $file);
                    }
                    // if (file_exists($dst . DIRECTORY_SEPARATOR . $file)) {
                    //     @unlink($dst . DIRECTORY_SEPARATOR . $file);
                    // }
                    $copyrt = @copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    if (!$copyrt) {
                        // echo 'copy ' . $dst . DIRECTORY_SEPARATOR . $file . ' failed';
                        return '网站目录没有写入权限，请调整权限';
                    }
                }
            }
        }
        closedir($dir);

        return true;
    }
}

if (!function_exists('delFile')) 
{  
    /**
     * 递归删除文件夹
     *
     * @param string $path 目录路径
     * @param boolean $delDir 是否删除空目录
     * @return boolean
     */
    function delFile($path, $delDir = FALSE) {
        if(!is_dir($path))
            return FALSE;       
        $handle = @opendir($path);
        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..")
                    is_dir("$path/$item") ? delFile("$path/$item", $delDir) : @unlink("$path/$item");
            }
            closedir($handle);
            if ($delDir) {
                return @rmdir($path);
            }
        }else {
            if (file_exists($path)) {
                return @unlink($path);
            } else {
                return FALSE;
            }
        }
    }
}

if (!function_exists('getDirFile')) 
{ 
    /**
     * 递归读取文件夹文件
     *
     * @param string $directory 目录路径
     * @param string $dir_name 显示的目录前缀路径
     * @param array $arr_file 是否删除空目录
     * @return boolean
     */
    function getDirFile($directory, $dir_name='', &$arr_file = array()) {
        if (!file_exists($directory) ) {
            return false;
        }

        $mydir = dir($directory);
        while($file = $mydir->read())
        {
            if((is_dir("$directory/$file")) AND ($file != ".") AND ($file != ".."))
            {
                if ($dir_name) {
                    getDirFile("$directory/$file", "$dir_name/$file", $arr_file);
                } else {
                    getDirFile("$directory/$file", "$file", $arr_file);
                }
                
            }
            else if(($file != ".") AND ($file != ".."))
            {
                if ($dir_name) {
                    $arr_file[] = "$dir_name/$file";
                } else {
                    $arr_file[] = "$file";
                }
            }
        }
        $mydir->close();

        return $arr_file;
    }
}

if (!function_exists('ey_scandir')) 
{ 
    /**
     * 部分空间为了安全起见，禁用scandir函数
     *
     * @param string $dir 路径
     * @return array
     */
    function ey_scandir($dir, $type = 'all')
    {
        if(function_exists('scandir')){
            $files = scandir($dir);
        } else {
            $files = [];
            $mydir = dir($dir);
            while($file = $mydir->read())
            {
                $files[] = "$file";
            }
            $mydir->close();
        }
        $arr_file = [];
        foreach ($files as $key => $val) {
            if(($val != ".") AND ($val != "..")){
                if ('all' == $type) {
                    $arr_file[] = "$val";
                } else if ('file' == $type && is_file($val)) {
                    $arr_file[] = "$val";
                } else if ('dir' == $type && is_dir($val)) {
                    $arr_file[] = "$val";
                }
            }
        }

        return $arr_file;
    }
}

if (!function_exists('group_same_key')) 
{ 
    /**
     * 将二维数组以元素的某个值作为键，并归类数组
     *
     * array( array('name'=>'aa','type'=>'pay'), array('name'=>'cc','type'=>'pay') )
     * array('pay'=>array( array('name'=>'aa','type'=>'pay') , array('name'=>'cc','type'=>'pay') ))
     * @param $arr 数组
     * @param $key 分组值的key
     * @return array
     */
    function group_same_key($arr,$key){
        $new_arr = array();
        foreach($arr as $k=>$v ){
            $new_arr[$v[$key]][] = $v;
        }
        return $new_arr;
    }
}

if (!function_exists('get_rand_str')) 
{ 
    /**
     * 获取随机字符串
     * @param int $randLength  长度
     * @param int $addtime  是否加入当前时间戳
     * @param int $includenumber   是否包含数字
     * @return string
     */
    function get_rand_str($randLength=6,$addtime=1,$includenumber=0){
        if ($includenumber){
            $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
        }else {
            $chars='abcdefghijklmnopqrstuvwxyz';
        }
        $len=strlen($chars);
        $randStr='';
        for ($i=0;$i<$randLength;$i++){
            $randStr.=$chars[rand(0,$len-1)];
        }
        $tokenvalue=$randStr;
        if ($addtime){
            $tokenvalue=$randStr.getTime();
        }
        return $tokenvalue;
    }
}

if (!function_exists('httpRequest')) 
{ 
    /**
     * CURL请求
     *
     * @param $url 请求url地址
     * @param $method 请求方法 get post
     * @param null $postfields post数据数组
     * @param array $headers 请求header信息
     * @param bool|false $debug  调试开启 默认false
     * @return mixed
     */
    function httpRequest($url, $method="GET", $postfields = null, $headers = array(), $timeout = 30, $debug = false) {
        $method = strtoupper($method);
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
        // curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }
        $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
        curl_setopt($ci, CURLOPT_URL, $url);
        if($ssl){
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
        }
        //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
        $response = curl_exec($ci);
        $requestinfo = curl_getinfo($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);
            echo "=====info===== \r\n";
            print_r($requestinfo);
            echo "=====response=====\r\n";
            print_r($response);
        }
        curl_close($ci);
        return $response;
        //return array($http_code, $response,$requestinfo);
    }
}

if (!function_exists('check_mobile')) 
{
    /**
     * 检查手机号码格式
     *
     * @param $mobile 手机号码
     */
    function check_mobile($mobile){
        if(preg_match('/1\d{10}$/',$mobile))
            return true;
        return false;
    }
}

if (!function_exists('check_telephone')) 
{
    /**
     * 检查固定电话
     *
     * @param $mobile
     * @return bool
     */
    function check_telephone($mobile){
        if(preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/',$mobile))
            return true;
        return false;
    }
}

if (!function_exists('check_email')) 
{
    /**
     * 检查邮箱地址格式
     *
     * @param $email 邮箱地址
     */
    function check_email($email){
        if(filter_var($email,FILTER_VALIDATE_EMAIL))
            return true;
        return false;
    }
}

if (!function_exists('getSubstr')) 
{
    /**
     * 实现中文字串截取无乱码的方法
     *
     * @param string $string 字符串
     * @param intval $start 起始位置
     * @param intval $length 截取长度
     * @return string
     */
    function getSubstr($string='', $start=0, $length=NULL) {
        if(mb_strlen($string,'utf-8')>$length){
            $str = msubstr($string, $start, $length, true,'utf-8');
            return $str;
        }else{
            return $string;
        }
    }
}

if (!function_exists('msubstr')) 
{
    /**
     * 字符串截取，支持中文和其他编码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function msubstr($str='', $start=0, $length=NULL, $suffix=false, $charset="utf-8") {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }

        $str_len = strlen($str); // 原字符串长度
        $slice_len = strlen($slice); // 截取字符串的长度
        if ($slice_len < $str_len) {
            $slice = $suffix ? $slice.'...' : $slice;
        }

        return $slice;
    }
}

if (!function_exists('html_msubstr')) 
{
    /**
     * 截取内容清除html之后的字符串长度，支持中文和其他编码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function html_msubstr($str='', $start=0, $length=NULL, $suffix=false, $charset="utf-8") {
        if (is_language() && 'cn' != get_current_lang()) {
            $length = $length * 2;
        }
        $str = eyou_htmlspecialchars_decode($str);
        $str = checkStrHtml($str);
        return msubstr($str, $start, $length, $suffix, $charset);
    }
}

if (!function_exists('text_msubstr')) 
{
    /**
     * 针对多语言截取，其他语言的截取是中文语言的2倍长度
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function text_msubstr($str='', $start=0, $length=NULL, $suffix=false, $charset="utf-8") {
        if (is_language() && 'cn' != get_current_lang()) {
            $length = $length * 2;
        }
        return msubstr($str, $start, $length, $suffix, $charset);
    }
}

if (!function_exists('eyou_htmlspecialchars_decode')) 
{
    /**
     * 自定义只针对htmlspecialchars编码过的字符串进行解码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    function eyou_htmlspecialchars_decode($str='') {
        if (is_string($str) && stripos($str, '&lt;') !== false && stripos($str, '&gt;') !== false) {
            $str = htmlspecialchars_decode($str);
        }
        return $str;
    }
}

if (!function_exists('isMobile')) 
{
    /**
     * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
     * 是否移动端访问
     *
     * @return boolean
     */
    function isMobile()
    {
        return request()->isMobile();

        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
                return true;
        }
            // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
                return false;
     }
}

if (!function_exists('isWeixin')) 
{
    /**
     * 是否微信端访问
     *
     * @return boolean
     */
    function isWeixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } return false;
    }
}

if (!function_exists('isWeixinApplets')) 
{
    /**
     * 是否微信端小程序访问
     *
     * @return boolean
     */
    function isWeixinApplets() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'miniProgram') !== false) {
            return true;
        } return false;
    }
}

if (!function_exists('isQq')) 
{
    /**
     * 是否QQ端访问
     *
     * @return boolean
     */
    function isQq() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ') !== false) {
            return true;
        } return false;
    }
}

if (!function_exists('isAlipay')) 
{
    /**
     * 是否支付端访问
     *
     * @return boolean
     */
    function isAlipay() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            return true;
        } return false;
    }
}

if (!function_exists('getFirstCharter')) 
{
    /**
     * php获取中文字符拼音首字母
     *
     * @param string $str 中文
     * @return boolean
     */
    function getFirstCharter($str){
          if(empty($str))
          {
                return '';          
          }
          $fchar=ord($str{0});
          if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
          $s1=iconv('UTF-8','gb2312',$str);
          $s2=iconv('gb2312','UTF-8',$s1);
          $s=$s2==$str?$s1:$str;
          $asc=ord($s{0})*256+ord($s{1})-65536;
         if($asc>=-20319&&$asc<=-20284) return 'A';
         if($asc>=-20283&&$asc<=-19776) return 'B';
         if($asc>=-19775&&$asc<=-19219) return 'C';
         if($asc>=-19218&&$asc<=-18711) return 'D';
         if($asc>=-18710&&$asc<=-18527) return 'E';
         if($asc>=-18526&&$asc<=-18240) return 'F';
         if($asc>=-18239&&$asc<=-17923) return 'G';
         if($asc>=-17922&&$asc<=-17418) return 'H';
         if($asc>=-17417&&$asc<=-16475) return 'J';
         if($asc>=-16474&&$asc<=-16213) return 'K';
         if($asc>=-16212&&$asc<=-15641) return 'L';
         if($asc>=-15640&&$asc<=-15166) return 'M';
         if($asc>=-15165&&$asc<=-14923) return 'N';
         if($asc>=-14922&&$asc<=-14915) return 'O';
         if($asc>=-14914&&$asc<=-14631) return 'P';
         if($asc>=-14630&&$asc<=-14150) return 'Q';
         if($asc>=-14149&&$asc<=-14091) return 'R';
         if($asc>=-14090&&$asc<=-13319) return 'S';
         if($asc>=-13318&&$asc<=-12839) return 'T';
         if($asc>=-12838&&$asc<=-12557) return 'W';
         if($asc>=-12556&&$asc<=-11848) return 'X';
         if($asc>=-11847&&$asc<=-11056) return 'Y';
         if($asc>=-11055&&$asc<=-10247) return 'Z';
         return null;
    }
}

if (!function_exists('pinyin_long')) 
{
    /**
     * 获取整条字符串汉字拼音首字母
     *
     * @param $zh
     * @return string
     */
    function pinyin_long($zh){
        $ret = "";
        $s1 = iconv("UTF-8","gb2312", $zh);
        $s2 = iconv("gb2312","UTF-8", $s1);
        if($s2 == $zh){$zh = $s1;}
        for($i = 0; $i < strlen($zh); $i++){
            $s1 = substr($zh,$i,1);
            $p = ord($s1);
            if($p > 160){
                $s2 = substr($zh,$i++,2);
                $ret .= getFirstCharter($s2);
            }else{
                $ret .= $s1;
            }
        }
        return $ret;
    }
}

if (!function_exists('respose')) 
{
    /**
     * 参数 is_jsonp 为true，表示跨域ajax请求的返回值
     *
     * @param string $res 数组
     * @param bool $is_jsonp 是否跨域
     * @return string
     */
    function respose($res, $is_jsonp = false){
        if (true === $is_jsonp) {
            exit(input('callback')."(".json_encode($res).")");
        } else {
            exit(json_encode($res));
        }
    }
}

if (!function_exists('urlsafe_b64encode')) 
{
    function urlsafe_b64encode($string) 
    {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}

if (!function_exists('getTime')) 
{
    /**
     * 获取当前时间戳
     *
     */
    function getTime()
    {
        return time();
    }
}

if (!function_exists('trim_space')) 
{
    /**
     * 过滤前后空格等多种字符
     *
     * @param string $str 字符串
     * @param array $arr 特殊字符的数组集合
     * @return string
     */
    function trim_space($str, $arr = array())
    {
        if (empty($arr)) {
            $arr = array(' ', '　');
        }
        foreach ($arr as $key => $val) {
            $str = preg_replace('/(^'.$val.')|('.$val.'$)/', '', $str);
        }

        return $str;
    }
}

if (!function_exists('func_preg_replace')) 
{
    /**
     * 替换指定的符号
     *
     * @param array $arr 特殊字符的数组集合
     * @param string $replacement 符号
     * @param string $str 字符串
     * @return string
     */
    function func_preg_replace($arr = array(), $replacement = ',', $str = '')
    {
        if (empty($arr)) {
            $arr = array('，');
        }
        foreach ($arr as $key => $val) {
            $str = preg_replace('/('.$val.')/', $replacement, $str);
        }

        return $str;
    }
}

if (!function_exists('db_create_in')) 
{
    /**
     * 创建像这样的查询: "IN('a','b')";
     *
     * @param    mixed      $item_list      列表数组或字符串,如果为字符串时,字符串只接受数字串
     * @param    string   $field_name     字段名称
     * @return   string
     */
    function db_create_in($item_list, $field_name = '')
    {
        if (empty($item_list))
        {
            return $field_name . " IN ('') ";
        }
        else
        {
            if (!is_array($item_list))
            {
                $item_list = explode(',', $item_list);
                foreach ($item_list as $k=>$v)
                {
                    $item_list[$k] = intval($v);
                }
            }

            $item_list = array_unique($item_list);
            $item_list_tmp = '';
            foreach ($item_list AS $item)
            {
                if ($item !== '')
                {
                    $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
                }
            }
            if (empty($item_list_tmp))
            {
                return $field_name . " IN ('') ";
            }
            else
            {
                return $field_name . ' IN (' . $item_list_tmp . ') ';
            }
        }
    }
}

if (!function_exists('static_version')) 
{
    /**
     * 给静态文件追加版本号，实时刷新浏览器缓存
     *
     * @param    string   $file     为远程文件
     * @return   string
     */
    function static_version($file)
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();
        // ---判断本地文件是否存在，否则返回false，以免@get_headers方法导致崩溃
        if (is_http_url($file)) { // 判断http路径
            if (preg_match('/^http(s?):\/\/'.$request->host(true).'/i', $file)) { // 判断当前域名的本地服务器文件(这仅用于单台服务器，多台稍作修改便可)
                // $pattern = '/^http(s?):\/\/([^.]+)\.([^.]+)\.([^\/]+)\/(.*)$/';
                $pattern = '/^http(s?):\/\/([^\/]+)(.*)$/';
                preg_match_all($pattern, $file, $matches);//正则表达式
                if (!empty($matches)) {
                    $filename = $matches[count($matches) - 1][0];
                    if (!file_exists(realpath(ltrim($filename, '/')))) {
                        return false;
                    }
                    $http_url = $file = $request->domain().$filename;
                }
            }
            
        } else {
            if (!file_exists(realpath(ltrim($file, '/')))) {
                return false;
            }
            $http_url = $request->domain().$file;
        }
        // -------------end---------------

        $parseStr = '';
        $headInf = @get_headers($http_url,1); 
        $update_time_str = !empty($headInf['Last-Modified']) ? '?t='.strtotime($headInf['Last-Modified']) : ''; 
        $type = strtolower(substr(strrchr($file, '.'), 1));
        switch ($type) {
            case 'js':
                $parseStr .= '<script type="text/javascript" src="' . $file .$update_time_str.'"></script>';
                break;
            case 'css':
                $parseStr .= '<link rel="stylesheet" type="text/css" href="' . $file .$update_time_str.'" />';
                break;
            case 'ico':
                $parseStr .= '<link rel="shortcut icon" href="' . $file .$update_time_str.'" />';
                break;
        }

        return $parseStr;
    }
}

if (!function_exists('tp_mkdir')) 
{
    /**
     * 递归创建目录 
     *
     * @param string $path 目录路径，不带反斜杠
     * @param intval $purview 目录权限码
     * @return boolean
     */  
    function tp_mkdir($path, $purview = 0777)
    {
        if (!is_dir($path)) {
            tp_mkdir(dirname($path), $purview);
            if (!mkdir($path, $purview)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('format_bytes')) 
{
    /**
     * 格式化字节大小
     *
     * @param  number $size      字节数
     * @param  string $delimiter 数字和单位分隔符
     * @return string            格式化后的带单位的大小
     */
    function format_bytes($size, $delimiter = '') {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}

if (!function_exists('unformat_bytes')) 
{
    /**
     * 反格式化字节大小
     *
     * @param  number $size      格式化带单位的大小
     */
    function unformat_bytes($formatSize)
    {
        $size = 0;
        if (preg_match('/^\d+P/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024 * 1024 * 1024 * 1024;
        } else if (preg_match('/^\d+T/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024 * 1024 * 1024;
        } else if (preg_match('/^\d+G/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024 * 1024;
        } else if (preg_match('/^\d+M/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024;
        } else if (preg_match('/^\d+K/i', $formatSize)) {
            $size = intval($formatSize) * 1024;
        } else if (preg_match('/^\d+B/i', $formatSize)) {
            $size = intval($formatSize);
        }

        $size = strval($size);

        return $size;
    }
}

if (!function_exists('is_http_url')) 
{
    /**
     * 判断url是否完整的链接
     *
     * @param  string $url 网址
     * @return boolean
     */
    function is_http_url($url)
    {
        // preg_match("/^(http:|https:|ftp:|svn:)?(\/\/).*$/", $url, $match);
        preg_match("/^((\w)*:)?(\/\/).*$/", $url, $match);
        if (empty($match)) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('get_html_first_imgurl')) 
{
    /**
     * 获取文章内容html中第一张图片地址
     *
     * @param  string $html html代码
     * @return boolean
     */
    function get_html_first_imgurl($html){
        $pattern = '~<img [^>]*[\s]?[\/]?[\s]?>~';
        preg_match_all($pattern, $html, $matches);//正则表达式把图片的整个都获取出来了
        $img_arr = $matches[0];//图片
        $first_img_url = "";
        if (!empty($img_arr)) {
            $first_img = $img_arr[0];
            $p="#src=('|\")(.*)('|\")#isU";//正则表达式
            preg_match_all ($p, $first_img, $img_val);
            if(isset($img_val[2][0])){
                $first_img_url = $img_val[2][0]; //获取第一张图片地址
            }
        }

        return $first_img_url;
    }
}

if (!function_exists('checkStrHtml')) 
{
    /**
     * 过滤Html标签
     *
     * @param     string  $string  内容
     * @return    string
     */
    function checkStrHtml($string){
        $string = trim_space($string);

        if(is_numeric($string)) return $string;
        if(!isset($string) or empty($string)) return '';

        $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$string);
        $string  = ($string);

        $string = strip_tags($string,""); //清除HTML如<br />等代码
        // $string = str_replace("\n", "", str_replace(" ", "", $string));//去掉空格和换行
        $string = str_replace("\n", "", $string);//去掉空格和换行
        $string = str_replace("\t","",$string); //去掉制表符号
        $string = str_replace(PHP_EOL,"",$string); //去掉回车换行符号
        $string = str_replace("\r","",$string); //去掉回车
        $string = str_replace("'","‘",$string); //替换单引号
        $string = str_replace("&amp;","&",$string);
        $string = str_replace("=★","",$string);
        $string = str_replace("★=","",$string);
        $string = str_replace("★","",$string);
        $string = str_replace("☆","",$string);
        $string = str_replace("√","",$string);
        $string = str_replace("±","",$string);
        $string = str_replace("‖","",$string);
        $string = str_replace("×","",$string);
        $string = str_replace("∏","",$string);
        $string = str_replace("∷","",$string);
        $string = str_replace("⊥","",$string);
        $string = str_replace("∠","",$string);
        $string = str_replace("⊙","",$string);
        $string = str_replace("≈","",$string);
        $string = str_replace("≤","",$string);
        $string = str_replace("≥","",$string);
        $string = str_replace("∞","",$string);
        $string = str_replace("∵","",$string);
        $string = str_replace("♂","",$string);
        $string = str_replace("♀","",$string);
        $string = str_replace("°","",$string);
        $string = str_replace("¤","",$string);
        $string = str_replace("◎","",$string);
        $string = str_replace("◇","",$string);
        $string = str_replace("◆","",$string);
        $string = str_replace("→","",$string);
        $string = str_replace("←","",$string);
        $string = str_replace("↑","",$string);
        $string = str_replace("↓","",$string);
        $string = str_replace("▲","",$string);
        $string = str_replace("▼","",$string);

        // --过滤微信表情
        $string = preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';}, $string);

        return $string;
    }
}

if (!function_exists('saveRemote')) 
{
    /**
     * 抓取远程图片
     *
     * @param     string  $fieldName  远程图片url
     * @param     string  $savePath  存储在public/upload的子目录
     * @return    string
     */
    function saveRemote($fieldName, $savePath = 'temp/'){
        $allowFiles = [".png", ".jpg", ".jpeg", ".gif", ".bmp", "webp"];

        $imgUrl = htmlspecialchars($fieldName);
        $imgUrl = str_replace("&amp;","&",$imgUrl);

        //http开头验证
        if(strpos($imgUrl,"http") !== 0){
            $data=array(
                'state' => '链接不是http链接',
            );
            return json_encode($data);
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl);
        if(!(stristr($heads[0],"200") && stristr($heads[0],"OK"))){
            $data=array(
                'state' => '链接不可用',
            );
            return json_encode($data);
        }
        //格式验证(扩展名验证和Content-Type验证)
        if(preg_match("/^http(s?):\/\/(mmbiz.qpic.cn|qimg.91ud.com)\/(.*)/", $imgUrl) != 1){
            $fileType = strtolower(strrchr($imgUrl,'.'));
            if(!in_array($fileType,$allowFiles) || stristr($heads['Content-Type'],"image")){
                $data=array(
                    'state' => '链接contentType不正确',
                );
                return json_encode($data);
            }
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl,false,$context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

        $dirname = './'.UPLOAD_PATH.'remote/'.date('Y/m/d').'/';
        $file['oriName'] = $m ? $m[1] : "";
        $file['filesize'] = strlen($img);
        $file['ext'] = strtolower(strrchr('remote.png','.'));
        $file['name'] = uniqid().$file['ext'];
        $file['fullName'] = $dirname.$file['name'];
        $fullName = $file['fullName'];

        //检查文件大小是否超出限制
        if($file['filesize'] >= 10240000){
            $data=array(
                'state' => '文件大小超出网站限制',
            );
            return json_encode($data);
        }

        //创建目录失败
        if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
            $data=array(
                'state' => '目录创建失败',
            );
            return json_encode($data);
        }else if(!is_writeable($dirname)){
            $data=array(
                'state' => '目录没有写权限',
            );
            return json_encode($data);
        }

        //移动文件
        if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
            $data=array(
                'state' => '写入文件内容错误',
            );
            return json_encode($data);
        }else{ //移动成功
            $data=array(
                'state' => 'SUCCESS',
                'url' => substr($file['fullName'],1),
                'title' => $file['name'],
                'original' => $file['oriName'],
                'type' => $file['ext'],
                'size' => $file['filesize'],
            );

            $ossConfig = tpCache('oss');
            if ($ossConfig['oss_switch']) {
                //图片可选择存放在oss
                $savePath = $savePath.date('Y/m/d/');
                $object = UPLOAD_PATH.$savePath.md5(getTime().uniqid(mt_rand(), TRUE)).'.'.pathinfo($data['url'], PATHINFO_EXTENSION);
                $getRealPath = ltrim($data['url'], '/');
                $ossClient = new \app\common\logic\OssLogic;
                $return_url = $ossClient->uploadFile($getRealPath, $object);
                if (!$return_url) {
                    $state = "ERROR" . $ossClient->getError();
                    $return_url = '';
                } else {
                    $state = "SUCCESS";
                }
                @unlink($getRealPath);
                $data['url'] = $return_url;
            }
        }
        return json_encode($data);
    }
}

if (!function_exists('func_common')) 
{
    /**
     * 自定义上传
     *
     * @param     string  $fileElementId  上传表单的ID
     * @param     string  $path  存储在public/upload的子目录
     * @param     string  $file_ext  图片后缀名
     * @return    string
     */
    function func_common($fileElementId = 'uploadImage', $path = 'temp', $file_ext = "jpg|gif|png|bmp|jpeg"){
        $file = request()->file($fileElementId);

        if (empty($file)) {
            return ['errcode'=>1,'errmsg'=>'请选择上传图片'];
        }
        
        $validate = array();
        /*文件大小限制*/
        $validate_size = tpCache('basic.file_size');
        if (!empty($validate_size)) {
            $validate['size'] = $validate_size * 1024 * 1024; // 单位为b
        }
        /*--end*/
        /*文件扩展名限制*/
        $validate_ext = tpCache('basic.image_type');
        if (!empty($validate_ext)) {
            $validate['ext'] = explode('|', $validate_ext);
        }
        /*--end*/
        /*上传文件验证*/
        if (!empty($validate)) {
            $is_validate = $file->check($validate);
            if ($is_validate === false) {
                return ['errcode'=>1,'errmsg'=>$file->getError()];
            }   
        }
        /*--end*/    
        /*验证图片一句话木马*/
        $imgstr = @file_get_contents($_FILES[$fileElementId]['tmp_name']);
        if (false !== $imgstr && preg_match('#<\?php#i', $imgstr)) {
            return ['errcode'=>1,'errmsg'=>'上传图片不合格'];
        }
        /*--end*/ 
        
        $fileName = $_FILES[$fileElementId]['name'];
        // 提取文件名后缀
        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
        // 提取出文件名，不包括扩展名
        $newfileName = preg_replace('/\.([^\.]+)$/', '', $fileName);
        // 过滤文件名.\/的特殊字符，防止利用上传漏洞
        $newfileName = preg_replace('#(\\\|\/|\.)#i', '', $newfileName);
        // 过滤后的新文件名
        $fileName = $newfileName.'.'.$file_ext;

        $savePath =  $path .'/'.date('Ymd/');
        $return_url = "";

        $ossConfig = tpCache('oss');
        if ($ossConfig['oss_switch']) {
            //图片可选择存放在oss
            $object = UPLOAD_PATH.$savePath.md5(getTime().uniqid(mt_rand(), TRUE)).'.'.$file_ext;
            $ossClient = new \app\common\logic\OssLogic;
            $return_url = $ossClient->uploadFile($file->getRealPath(), $object);
            if (!$return_url) {
                $state = "ERROR" . $ossClient->getError();
                $return_url = '';
            } else {
                $state = "SUCCESS";
            }
            @unlink($file->getRealPath());
        } else { // 使用自定义的文件保存规则
            $info = $file->rule(function($file){return md5(mt_rand());})->move(UPLOAD_PATH.$savePath);
            if($info){
                $return_url =  '/'.UPLOAD_PATH.$savePath.$info->getSaveName();
            }
        }
        
        if($return_url){
            return ['errcode'=>0,'errmsg'=>'上传成功','img_url'=>$return_url];
        }else{
            return ['errcode'=>1,'errmsg'=>'上传失败'];
        }
    }
}

if (!function_exists('func_substr_replace')) 
{
    /**
     * 隐藏部分字符串
     *
     * @param     string  $str  字符串
     * @param     string  $replacement  替换显示的字符
     * @param     intval  $start  起始位置
     * @param     intval  $length  隐藏长度
     * @return    string
     */
    function func_substr_replace($str, $replacement = '*', $start = 1, $length = 3)
    {
        $len = mb_strlen($str,'utf-8');
        if ($len > intval($start+$length)) {
            $str1 = msubstr($str,0,$start);
            $str2 = msubstr($str,intval($start+$length),NULL);
        } else {
            $str1 = msubstr($str,0,1);
            $str2 = msubstr($str,$len-1,1);    
            $length = $len - 2;        
        }
        $new_str = $str1;
        for ($i = 0; $i < $length; $i++) { 
            $new_str .= $replacement;
        }
        $new_str .= $str2;

        return $new_str;
    }
}

if (!function_exists('func_authcode')) 
{
    /**
     * 字符串加密解密
     *
     * @param unknown $string   明文或密文
     * @param string $operation   DECODE表示解密,其它表示加密
     * @param string $key   密匙
     * @param number $expiry 密文有效期
     * @return string
     */
    function func_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : 'zxsdcrtkbrecxm');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }

    }
}

/**
 *  获取拼音以gbk编码为准
 *
 * @param     string  $str     字符串信息
 * @param     int     $ishead  是否取头字母
 * @param     int     $isclose 是否关闭字符串资源
 * @return    string
 */
if ( ! function_exists('get_pinyin'))
{
    function get_pinyin($str, $ishead=0, $isclose=1)
    {
        // return SpGetPinyin(utf82gb($str), $ishead, $isclose);

        $s1 = iconv("UTF-8","gb2312", $str);
        $s2 = iconv("gb2312","UTF-8", $s1);
        if($s2 == $str){$str = $s1;}
        
        $pinyins = array();
        $restr = '';
        $str = trim($str);
        $slen = strlen($str);
        if($slen < 2)
        {
            return $str;
        }
        if(empty($pinyins))
        {
            $fp = fopen(DATA_PATH.'conf/pinyin.dat', 'r');
            while(!feof($fp))
            {
                $line = trim(fgets($fp));
                $pinyins[$line[0].$line[1]] = substr($line, 3, strlen($line)-3);
            }
            fclose($fp);
        }
        for($i=0; $i<$slen; $i++)
        {
            if(ord($str[$i])>0x80)
            {
                $c = $str[$i].$str[$i+1];
                $i++;
                if(isset($pinyins[$c]))
                {
                    if($ishead==0)
                    {
                        $restr .= $pinyins[$c];
                    }
                    else
                    {
                        $restr .= $pinyins[$c][0];
                    }
                }else
                {
                    $restr .= "_";
                }
            }else if( preg_match("/[a-z0-9]/i", $str[$i]) )
            {
                $restr .= $str[$i];
            }
            else
            {
                $restr .= "_";
            }
        }
        if($isclose==0)
        {
            unset($pinyins);
        }
        return strtolower($restr);
    }
}

if (!function_exists('filter_line_return')) 
{
    /**
     *  过滤换行回车符
     *
     * @param     string  $str     字符串信息
     * @return    string
     */
    function filter_line_return($str = '', $replace = '')
    {
        return str_replace(PHP_EOL, $replace, $str);
    }
}

if (!function_exists('MyDate')) 
{
    /**
     *  时间转化日期格式
     *
     * @param     string  $format     日期格式
     * @param     intval  $t     时间戳
     * @return    string
     */
    function MyDate($format = 'Y-m-d', $t = '')
    {
        if (!empty($t)) {
            $t = date($format, $t);
        }
        return $t;
    }
}

if (!function_exists('arctype_options')) 
{
    /**
     * 过滤和排序所有文章栏目，返回一个带有缩进级别的数组
     *
     * @param   int     $id     上级栏目ID
     * @param   array   $arr        含有所有栏目的数组
     * @param   string     $id_alias      id键名
     * @param   string     $pid_alias      父id键名
     * @return  void
     */
    function arctype_options($spec_id, $arr, $id_alias, $pid_alias)
    {
        $cat_options = array();

        if (isset($cat_options[$spec_id]))
        {
            return $cat_options[$spec_id];
        }

        if (!isset($cat_options[0]))
        {
            $level = $last_id = 0;
            $options = $id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $id = $value[$id_alias];
                    if ($level == 0 && $last_id == 0)
                    {
                        if ($value[$pid_alias] > 0)
                        {
                            break;
                        }

                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id][$id_alias]    = $id;
                        // $options[$id]['typename']  = $value['typename'];
                        unset($arr[$key]);

                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_id  = $id;
                        $id_array = array($id);
                        $level_array[$last_id] = ++$level;
                        continue;
                    }

                    if ($value[$pid_alias] == $last_id)
                    {
                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id][$id_alias]    = $id;
                        // $options[$id]['typename']  = $value['typename'];
                        unset($arr[$key]);

                        if ($value['has_children'] > 0)
                        {
                            if (end($id_array) != $last_id)
                            {
                                $id_array[] = $last_id;
                            }
                            $last_id    = $id;
                            $id_array[] = $id;
                            $level_array[$last_id] = ++$level;
                        }
                    }
                    elseif ($value[$pid_alias] > $last_id)
                    {
                        break;
                    }
                }

                $count = count($id_array);
                if ($count > 1)
                {
                    $last_id = array_pop($id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_id != end($id_array))
                    {
                        $last_id = end($id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_id = 0;
                        $id_array = array();
                        continue;
                    }
                }

                if ($last_id && isset($level_array[$last_id]))
                {
                    $level = $level_array[$last_id];
                }
                else
                {
                    $level = 0;
                    break;
                }
            }
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }

        if (!$spec_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_id]))
            {
                return array();
            }

            $spec_id_level = $options[$spec_id]['level'];

            foreach ($options AS $key => $value)
            {
                if ($key != $spec_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }

            $spec_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_id_level == $value['level'] && $value[$id_alias] != $spec_id) ||
                    ($spec_id_level > $value['level']))
                {
                    break;
                }
                else
                {
                    $spec_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_id] = $spec_id_array;

            return $spec_id_array;
        }
    }
}

if (!function_exists('img_replace_url')) 
{
    /**
     * 内容图片地址替换成带有http地址
     *
     * @param string $content 内容
     * @param string $imgurl 远程图片url
     * @return string
     */
    function img_replace_url($content='', $imgurl = '')
    {
        $pregRule = "/<img(.*?)src(\s*)=(\s*)[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp|\.ico]))[\'|\"](.*?)[\/]?(\s*)>/i";
        $content = preg_replace($pregRule, '<img ${1} src="'.$imgurl.'" ${5} />', $content);

        return $content;
    }
}

if (!function_exists('getCmsVersion')) 
{
    /**
     * 获取当前CMS版本号
     *
     * @return string
     */
    function getCmsVersion()
    {
        $ver = 'v1.1.8';
        $version_txt_path = ROOT_PATH.'data/conf/version.txt';
        if(file_exists($version_txt_path)) {
            $fp = fopen($version_txt_path, 'r');
            $content = fread($fp, filesize($version_txt_path));
            fclose($fp);
            $ver = $content ? $content : $ver;
        } else {
            $r = tp_mkdir(dirname($version_txt_path));
            if ($r) {
                $fp = fopen($version_txt_path, "w+") or die("请设置".$version_txt_path."的权限为777");
                $web_version = tpCache('system.system_version');
                $ver = !empty($web_version) ? $web_version : $ver;
                if (fwrite($fp, $ver)) {
                    fclose($fp);
                }
            }
        }
        return $ver;
    }
}

if (!function_exists('getVersion')) 
{
    /**
     * 获取当前各种版本号
     *
     * @return string
     */
    function getVersion($filename='version', $ver='v1.0.0')
    {
        $version_txt_path = ROOT_PATH.'data/conf/'.$filename.'.txt';
        if(file_exists($version_txt_path)) {
            $fp = fopen($version_txt_path, 'r');
            $content = fread($fp, filesize($version_txt_path));
            fclose($fp);
            $ver = $content ? $content : $ver;
        } else {
            $r = tp_mkdir(dirname($version_txt_path));
            if ($r) {
                $fp = fopen($version_txt_path, "w+") or die("请设置".$version_txt_path."的权限为777");
                if (fwrite($fp, $ver)) {
                    fclose($fp);
                }
            }
        }
        return $ver;
    }
}

if (!function_exists('getWeappVersion')) 
{
    /**
     * 获取当前插件版本号
     *
     * @param string $ocde 插件标识
     * @return string
     */
    function getWeappVersion($code)
    {
        $ver = 'v1.0';
        $config_path = WEAPP_DIR_NAME.DS.$code.DS.'config.php';
        if(file_exists($config_path)) {
            $config = include $config_path;
            $ver = !empty($config['version']) ? $config['version'] : $ver;
        } else {
            die($code."插件缺少".$config_path."配置文件");
        }
        return $ver;
    }
}

if (!function_exists('strip_sql')) 
{
    /**
     * 转换SQL关键字
     *
     * @param unknown_type $string
     * @return unknown
     */
    function strip_sql($string) {
        $pattern_arr = array(
                "/\bunion\b/i",
                "/\bselect\b/i",
                "/\bupdate\b/i",
                "/\bdelete\b/i",
                "/\boutfile\b/i",
                // "/\bor\b/i",
                "/\bchar\b/i",
                "/\bconcat\b/i",
                "/\btruncate\b/i",
                "/\bdrop\b/i",            
                "/\binsert\b/i", 
                "/\brevoke\b/i", 
                "/\bgrant\b/i",      
                "/\breplace\b/i", 
                // "/\balert\b/i", 
                "/\brename\b/i",            
                // "/\bmaster\b/i",
                "/\bdeclare\b/i",
                // "/\bsource\b/i",
                // "/\bload\b/i",
                // "/\bcall\b/i", 
                "/\bexec\b/i",         
                "/\bdelimiter\b/i",
                "/\bphar\b\:/i",
                "/\bphar\b/i",
                "/\@(\s*)\beval\b/i",
                "/\beval\b/i",
        );
        $replace_arr = array(
                'ｕｎｉｏｎ',
                'ｓｅｌｅｃｔ',
                'ｕｐｄａｔｅ',
                'ｄｅｌｅｔｅ',
                'ｏｕｔｆｉｌｅ',
                // 'ｏｒ',
                'ｃｈａｒ',
                'ｃｏｎｃａｔ',
                'ｔｒｕｎｃａｔｅ',
                'ｄｒｏｐ',            
                'ｉｎｓｅｒｔ',
                'ｒｅｖｏｋｅ',
                'ｇｒａｎｔ',
                'ｒｅｐｌａｃｅ',
                // 'ａｌｅｒｔ',
                'ｒｅｎａｍｅ',
                // 'ｍａｓｔｅｒ',
                'ｄｅｃｌａｒｅ',
                // 'ｓｏｕｒｃｅ',
                // 'ｌｏａｄ',
                // 'ｃａｌｌ',                 
                'ｅｘｅｃ',         
                'ｄｅｌｉｍｉｔｅｒ',
                'ｐｈａｒ',
                'ｐｈａｒ',
                '＠ｅｖａｌ',
                'ｅｖａｌ',
        );
     
        return is_array($string) ? array_map('strip_sql', $string) : preg_replace($pattern_arr, $replace_arr, $string);
    }
}

if (!function_exists('get_weapp_class')) 
{
    /**
     * 获取插件类的类名
     *
     * @param strng $name 插件名
     * @param strng $controller 控制器
     * @return class
     */
    function get_weapp_class($name, $controller = ''){
        $controller = !empty($controller) ? $controller : $name;
        $class = WEAPP_DIR_NAME."\\{$name}\\controller\\{$controller}";
        return $class;
    }
}

if (!function_exists('view_logic')) 
{
    /**
     * 模型对应逻辑
     * @param intval $aid 文档ID
     * @param intval $channel 栏目ID
     * @param intval $result 数组
     * @return array
     */
    function view_logic($aid, $channel, $result = array())
    {
        $result['image_list'] = $result['attr_list'] = $result['file_list'] = array();
        switch ($channel) {
            case '2': // 产品模型
            {
                /*产品相册*/
                $image_list = model('ProductImg')->getProImg($aid);

                // 支持子目录
                foreach ($image_list as $k1 => $v1) {
                    $image_list[$k1]['image_url'] = handle_subdir_pic($v1['image_url']);
                }
                
                $result['image_list'] = $image_list;
                /*--end*/

                /*产品参数*/
                $attr_list = model('ProductAttr')->getProAttr($aid);
                $attr_list = model('LanguageAttr')->getBindValue($attr_list, 'product_attribute', get_main_lang()); // 获取多语言关联绑定的值
                $result['attr_list'] = $attr_list;
                /*--end*/
                break;
            }

            case '3': // 图集模型
            {
                /*图集相册*/
                $image_list = model('ImagesUpload')->getImgUpload($aid);
                
                // 支持子目录
                foreach ($image_list as $k1 => $v1) {
                    $image_list[$k1]['image_url'] = handle_subdir_pic($v1['image_url']);
                }

                $result['image_list'] = $image_list;
                /*--end*/
                break;
            }

            case '4': // 下载模型
            {
                /*下载资料列表*/
                $file_list = model('DownloadFile')->getDownFile($aid);
                
                // 支持子目录
                foreach ($file_list as $k1 => $v1) {
                    $file_list[$k1]['file_url'] = handle_subdir_pic($v1['file_url']);
                }

                $result['file_list'] = $file_list;
                /*--end*/
                break;
            }

            default:
            {
                break;
            }
        }

        return $result;
    }
}

if (!function_exists('uncamelize')) 
{
    /**
     * 驼峰命名转下划线命名
     * 思路:
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     */
    function uncamelize($camelCaps, $separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}

if (!function_exists('GetDatabaseData')) 
{
    /**
     * 获取数据中指定数据表的图片和文件路径
     * 思路:
     * 
     */
    function GetDatabaseData($array)
    {
        $data  = $hello = array();
        $i = '0';
        foreach ($array as $value) {
            $where  = $value['field']."<>''";
            $result = M($value['table'])->field($value['field'])->where($where)->select();
            // 查找多余未使用文件
            foreach ($result as $vv) {
                if ('litpic' == $value['field']) {
                    $path = parse_url($vv['litpic']);
                    if ($path['host']) {
                        $data[$i] = ROOT_DIR.$path['path'];
                    }else{
                        $data[$i] = $path['path'];
                    }
                    $i++;
                } else if ('image_url' == $value['field']) {
                    $path = parse_url($vv['image_url']);
                    if ($path['host']) {
                        $data[$i] = ROOT_DIR.$path['path'];
                    }else{
                        $data[$i] = $path['path'];
                    }
                    $i++;
                } else if ('logo' == $value['field']) {
                    $path = parse_url($vv['logo']);
                    if ($path['host']) {
                        $data[$i] = ROOT_DIR.$path['path'];
                    }else{
                        $data[$i] = $path['path'];
                    }
                    $i++;
                } else if ('file_url' == $value['field']) {
                    $path = parse_url($vv['file_url']);
                    if ($path['host']) {
                        $data[$i] = ROOT_DIR.$path['path'];
                    }else{
                        $data[$i] = $path['path'];
                    }
                    $i++;
                } else if ('head_pic' == $value['field']) {
                    $path = parse_url($vv['head_pic']);
                    if ($path['host']) {
                        $data[$i] = ROOT_DIR.$path['path'];
                    }else{
                        $data[$i] = $path['path'];
                    }
                    $i++;
                } else if ('intro' == $value['field']) {
                    $str = htmlspecialchars_decode($vv['intro']);
                    $str = strip_tags($str, '<img>');
                    preg_match_all('/\<img(.*?)src\=[\'|\"]([\w:\/\.]+)[\'|\"]/i', $str, $matches);
                    $match = $matches[2];
                    foreach ($match as $vvv) {
                        $data[$i] = $vvv;
                        $i++;
                    }
                } else if ('content' == $value['field']) {
                    $str = htmlspecialchars_decode($vv['content']);
                    $str = strip_tags($str, '<img>');
                    preg_match_all('/\<img(.*?)src\=[\'|\"]([\w:\/\.]+)[\'|\"]/i', $str, $matches);
                    $match = $matches[2];
                    foreach ($match as $vvv) {
                        $data[$i] = $vvv;
                        $i++;
                    }
                } else if ('config' == $value['table']) {
                    $strlen = strlen(ROOT_DIR);
                    if (substr($vv['value'], $strlen, 7 ) == '/public' || substr($vv['value'], $strlen, 7 ) == '/upload') {
                        $data[$i] = $vv['value'];
                        $i++;
                    }
                } else if ('ui_config' == $value['table']) {
                    $values = json_decode($vv['value'],true);
                    $str = htmlspecialchars_decode($values['info']['value']);
                    $str = strip_tags($str, '<img>');
                    preg_match_all('/\<img(.*?)src\=[\'|\"]([\w:\/\.]+)[\'|\"]/i', $str, $matches);
                    $match = $matches[2];
                    foreach ($match as $vvv) {
                        $data[$i] = $vvv;
                        $i++;
                    }
                } else if($value['channel_id']){
                    if ('imgs' == $value['dtype']) {
                        $hello = explode(',',$vv[$value['field']]);
                    } else if ('img' == $value['dtype']) {
                        $path = parse_url($vv[$value['field']]);
                        if ($path['host']) {
                            $data[$i] = ROOT_DIR.$path['path'];
                        }else{
                            $data[$i] = $path['path'];
                        }
                        $i++;
                    }
                }
            }
        }

        $data = array_merge($data,$hello);
        $data = array_unique($data);
        return $data;
    }
}

if (!function_exists('read_bidden_inc')) 
{
    /**
     * 读取被禁止外部访问的配置文件
     * 
     */
    function read_bidden_inc($phpfilepath = '')
    {
        $data = @file($phpfilepath);
        if ($data) {
            $data = !empty($data[1]) ? json_decode($data[1]) : [];
        }
        return $data;
    }
}

if (!function_exists('write_bidden_inc')) 
{
    /**
     * 写入被禁止外部访问的配置文件
     */
    function write_bidden_inc($arr = array(), $phpfilepath)
    {
        $r = tp_mkdir(dirname($phpfilepath));
        if ($r) {
            $setting = "<?php die('forbidden'); ?>\n";
            $setting .= json_encode($arr);
            $setting = str_replace("\/", "/",$setting);
            $incFile = fopen($phpfilepath, "w+");
            if (fwrite($incFile, $setting)) {
                fclose($incFile);
                return true;
            }else{
                return false;
            }
        }
    }
}

// if (!function_exists('get_auth_code')) 
// {
//     /**
//      * 密码加密串
//      */
//     function get_auth_code()
//     {
//         $auth_code = \think\Config::get('AUTH_CODE');
//         $filepath = DATA_PATH.'conf/authcode.php';
//         if(file_exists($filepath)) {
//             $data = (array)read_bidden_inc($filepath);
//             $auth_code = !empty($data['auth_code']) ? $data['auth_code'] : $auth_code;
//         } else {
//             write_bidden_inc(['auth_code'=>$auth_code], $filepath);
//         }

//         return $auth_code;
//     }
// }


if (!function_exists('get_urltodomain')) 
{
    /**
     * 取得根域名
     * @param type $domain 域名
     * @return string 返回根域名
     */
    function get_urltodomain($domain = '') {
        if (empty($domain) || !stristr($domain, '.')) {
            return '';
        }
        $re_domain = '';
        $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
        $array_domain = explode(".", $domain);
        $array_num = count($array_domain) - 1;
        if ($array_domain[$array_num] == 'cn') {
            if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
                $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
            } else {
                $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
            }
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
        return $re_domain;
    }
}