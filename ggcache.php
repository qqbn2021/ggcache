<?php
/**
 * Plugin Name:果果加速
 * Plugin URI:https://www.ggdoc.cn/plugin/5.html
 * Description:支持文件、Redis、Memcached缓存加速，让页面浏览更快！
 * Version:0.0.2
 * Requires at least: 5.0
 * Requires PHP:5.3
 * Author:果果开发
 * Author URI:https://www.ggdoc.cn
 * License:GPL v2 or later
 */

// 直接访问报404错误
if (!function_exists('add_action')) {
    http_response_code(404);
    exit;
}
const GGCACHE_PLUGIN_FILE = __FILE__;
// 插件目录后面有 /
if (!defined('GGCACHE_PLUGIN_DIR')) {
    define('GGCACHE_PLUGIN_DIR', plugin_dir_path(GGCACHE_PLUGIN_FILE));
}
// 定义配置
$ggcache_options = get_option('ggcache_options', array());
/**
 * 自动加载
 * @param string $class
 * @return void
 */
function ggcache_autoload($class)
{
    $class_file = GGCACHE_PLUGIN_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
    if (file_exists($class_file)) {
        require_once $class_file;
    }
}

spl_autoload_register('ggcache_autoload');
// 启用插件
register_activation_hook(GGCACHE_PLUGIN_FILE, array('Ggcache_Plugin', 'plugin_activation'));
// 删除插件
register_uninstall_hook(GGCACHE_PLUGIN_FILE, array('Ggcache_Plugin', 'plugin_uninstall'));
// 禁用插件
register_deactivation_hook(GGCACHE_PLUGIN_FILE, array('Ggcache_Plugin', 'plugin_deactivation'));
// 添加页面
add_action('admin_init', array('Ggcache_Plugin', 'admin_init'));
// 添加菜单
add_action('admin_menu', array('Ggcache_Plugin', 'admin_menu'));
// 在我的插件那添加设置的链接
add_filter('plugin_action_links_' . plugin_basename(GGCACHE_PLUGIN_FILE), array('Ggcache_Plugin', 'link_setting'));
// 添加js文件
add_action('admin_enqueue_scripts', array('Ggcache_Plugin', 'wp_enqueue_scripts'));
// 添加清除缓存操作
add_action('wp_ajax_clear_cache', array('Ggcache_Plugin', 'wp_ajax_clear_cache'));