<?php
if (!defined('ABSPATH')) {
    http_response_code(404);
}
// 定义缓存变量
const GGCACHE_ADVANCED_CACHE = true;
// 以下页面不缓存
if (is_admin() || !isset($_SERVER['REQUEST_METHOD']) || 'GET' !== $_SERVER['REQUEST_METHOD']) {
    return;
}
// 非200状态码不缓存
if (function_exists('http_response_code') && 200 != http_response_code()) {
    return;
}
// 判断有没有配置缓存
$ggcache_plugin_path = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
// 插件文件不存在
if (!file_exists($ggcache_plugin_path . '/ggcache/includes/class-ggcache-advanced.php')) {
    return;
}
require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-advanced.php';
$ggcache_options = Ggcache_Advanced::get_config();
// 没有配置缓存
if (empty($ggcache_options['type']) || empty($ggcache_options['timeout'])) {
    return;
}
define('GGCACHE_PLUGIN_DIR', $ggcache_plugin_path . '/ggcache/');
// 特殊文件不缓存
$request_uri = $_SERVER['REQUEST_URI'];
if (preg_match('/robots\.txt/i', $request_uri) || preg_match('/\.htaccess/i', $request_uri)) {
    return;
}
// 登录后的用户不缓存
if (!empty($_COOKIE)) {
    foreach ($_COOKIE as $k => $v) {
        if (preg_match('/^wordpress_logged_in_/i', $k)) {
            return;
        }
    }
}
// 显示缓存
$ggcache_cache_key = md5(trim($request_uri, '/') . (!empty($ggcache_options['salt']) ? $ggcache_options['salt'] : ''));
Ggcache_Advanced::show_cache($ggcache_cache_key);
// 存储缓存
ob_start(array('Ggcache_Advanced', 'save_cache'));