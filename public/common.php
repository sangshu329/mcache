<?php
/**
 * Created by PhpStorm.
 * User: sien
 * Date: 2017/5/20 0020
 * Time: 15:27
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function clear_dir_file($path)
{
    if ($dir = open($path)) {
        while ($file = readdir($dir)) {
            $check = is_dir($path . $file);
            if (!$check) {
                @unlink($path . $path);
            } else {
                if ($file != '.' && $file != '..') {
                    clear_dir_file($path . $file . '/');
                }
            }
        }
        closedir($dir);
        rmdir($path);
        return true;
    }
}

if (!function_exists("load_fanwe_cache")) {
    function load_fanwe_cache()
    {
        global $distribution_cfg ;
        $type= $distribution_cfg['CACHE_TYPE'];
        $cacheClass = 'Cache' .ucWords(strtolower(strim($type))) . 'Service';
        if(file_exists(APP_ROOT_PATH . "lib/" . $cacheClass . '.php')) {
            require_once APP_ROOT_PATH . 'lib/' . $cacheClass . '.php';
            if(class_exists($cacheClass)) {
                $cache = new $cacheClass();
            }
            return $cache;
        } else {
            $file_cache_file = APP_ROOT_PATH . 'lib/CacheFileService.php';
            if(file_exists($file_cache_file))
                require_once APP_ROOT_PATH . 'lib/CacheFileService.php';
            if(class_exists('CacheFileService')) {
                $cache = new CacheFileService();
            }
            return $cache;
        }
    }
}

function strim($str)
{
    return quotes(htmlspecialchars($str));
}

function quotes($content)
{
    if(is_array($content)) {
        foreach ($content as $key => $value) {
            $content[$key] = addslashes($value);
        }
    } else {
        $content = addslashes($content);
    }
    return $content;
}

//检测是否为手机端访问
function isMobile()
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
    // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
    // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
    if ($mobile_browser > 0)
        return true;
    else
        return false;
}

function logger($name = 'mylog')
{
    $log = new Logger($name);
    $log->pushHandler(new StreamHandler(APP_ROOT_PATH . 'logs/' . $name . date('Ymd') . '.log',Logger::DEBUG));
    return $log;
}