<?php

/**
 * Created by PhpStorm.
 * User: sien
 * Date: 2017/5/20 0020
 * Time: 14:40
 */
class CacheService
{
    /* 连接 */
    protected $connected;

    protected $handler;

    protected $prefix = '~@';

    protected $options = array();

    protected $type;

    protected $expire;

    /* 取得缓存实例 */
    static function getInstance()
    {
        return load_fanwe_cache();
    }

    protected function log_names($name)
    {
        if (!$GLOBALS['distribution_cfg']['CACHE_LOG']) return;
        $name_logs_files = APP_ROOT_PATH . "public/runtime/app/~cache_name.log";
        (!is_dir(dirname($name_logs_files))) ? @mkdir(dirname($name_logs_files)) : '';
        if (!file_exists($name_logs_files)) {
            $names = array();
            array_push($names,$name);
            $names = serialize($names);
            @file_put_contents($name_logs_files, $names);
        } else {
            if ($name != '') {
                $names = @file_get_contents($name_logs_files);
                $names = unserialize($names);
                if (is_array($names) && !in_array($name, $names)) {
                    array_push($names, $name);
                } elseif (!is_array($names)) {
                    $names = array();
                    array_push($names, $name);
                }
                $names = serialize($names);
                @file_put_contents($name_logs_files, $names);
            }
        }
    }

    protected function get_names()
    {
        if (!$GLOBALS['distribution_cfg']['CACHE_LOG']) return;
        $name_logs_files = APP_ROOT_PATH . 'public/runtion/app/~cache_name.log';
        if (file_exists($name_logs_files)) {
            $names = @file_get_contents($name_logs_files);
            $names = unserialize($names);
            if (is_array($names)) {
                return $names;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    protected function del_name_logs()
    {
        if (!$GLOBALS['distribution_cfg']['CACHE_LOG']) return;
        $name_logs_files = APP_ROOT_PATH . 'public/runtime/app/~cache_name.log';
        @unlink($name_logs_files);
    }
}