<?php

/**
 * Created by PhpStorm.
 * User: sien
 * Date: 2017/5/20 0020
 * Time: 15:04
 */
class CacheFileService extends CacheService
{
    private $dir;

    public function __construct()
    {
        $this->dir = APP_ROOT_PATH . 'public/runtime/data/';
        $this->init();
    }

    public function set_dis($dir = '')
    {
        if ($dir != '') {
            $this->dir = $dir;
            $this->init();
        }
    }

    public function init()
    {
        $stat = @stat($this->dir);

        if (!is_dir($this->dir)) {
            if (!mkdir($this->dir)) return false;
            @chmod($this->dir, 0777);
        }
    }

    private function filename($name, $mdir = false)
    {
        $name = md5($name);
        $filename = $name . '.php';

        $hash_dir = $this->dir . '/c' . substr(md5($name), 0, 1) . '/';
        if ($mdir && !is_dir($hash_dir)) {
            @mkdir($hash_dir);
            @chmod($hash_dir, 0777);
        }
        $hash_dir = $hash_dir . 'c' . substr(md5($name), 1, 1) . '/';
        if ($mdir && !is_dir($hash_dir)) {
            @mkdir($hash_dir);
            @chmod($hash_dir, 0777);
        }
        return $hash_dir . $this->prefix . $filename;
    }

    public function get($name)
    {
        if (IS_DEBUG) return false;
        $var_name = md5($name);
        global $$var_name;
        if ($$var_name) {
            return $$var_name;
        }
        $filename = $this->filename($name);
        $content = @file_get_contents($filename);
        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if ($expire != -1 && time() > filemtime($filename) + $expire) {
                @unlink($filename);
                return false;
            }
            $content = substr($content, 20, -3);
            $content = unserialize($content);
            $$var_name = $content;
            return $content;
        } else {
            return false;
        }
    }

    public function set($name, $value, $expire = "-1")
    {
        if (IS_DEBUG) return false;
        if ($expire == '-1') $expire = 3600 * 24;
        $filename = $this->filename($name, true);
        $data = serialize($value);
        $data = "<?php\n//" . sprintf('%012d', $expire) . $data . "\n?>";
        $this->log_names($name);
        $rs = @file_put_contents($filename, $data);
        if ($rs)
            return true;
        else
            return false;
    }

    public function rm($name)
    {
        return @unlink($this->filename($name));
    }

    public function clear()
    {
        $this->del_name_logs();
        $path = $this->dir;
        clear_dir_file($path);
    }
}