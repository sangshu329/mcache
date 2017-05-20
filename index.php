<?php
/**
 * Created by PhpStorm.
 * User: sien
 * Date: 2017/5/20 0020
 * Time: 14:39
 */
define('APP_ROOT_PATH', str_replace('\\', '/', str_replace('index.php', '', __FILE__)));
if (!is_dir(APP_ROOT_PATH . 'public/runtime/')) @mkdir(APP_ROOT_PATH . 'public/runtime/');
require APP_ROOT_PATH . 'public/config.php';

require APP_ROOT_PATH . 'lib/Cache.php';

$cacheService = new CacheService();
$cache = $cacheService->getInstance();
$data = [
    'title' => '测试数据',
    'content' => '这是用于测试cache 缓存类的数据123'
];
$cache->set('test"2', $data);
var_dump($cache->get('test"2'));






