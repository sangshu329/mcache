<?php
/**
 * Created by PhpStorm.
 * User: sien
 * Date: 2017/5/20 0020
 * Time: 15:27
 */
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



