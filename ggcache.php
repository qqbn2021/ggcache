<?php
/**
 * Plugin Name:果果加速
 * Plugin URI:https://www.ggdoc.cn/plugin/5.html
 * Description:支持文件、Redis、Memcache缓存加速，让页面浏览更快！
 * Version:0.0.1
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
if (defined('GGCACHE_PLUGIN_DIR')) {
    // 在我的插件那添加重名插件说明
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Ggcache_Plugin', 'duplicate_name'));
    return;
}
// 插件目录后面有 /
define('GGCACHE_PLUGIN_DIR', plugin_dir_path(__FILE__));
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
register_activation_hook(__FILE__, array('Ggcache_Plugin', 'plugin_activation'));
// 删除插件
register_uninstall_hook(__FILE__, array('Ggcache_Plugin', 'plugin_uninstall'));
// 添加页面
add_action('admin_init', array('Ggcache_Plugin', 'admin_init'));
// 添加菜单
add_action('admin_menu', array('Ggcache_Plugin', 'admin_menu'));
// 在我的插件那添加设置的链接
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Ggcache_Plugin', 'link_setting'));
// 非后台页面，开启缓存
if (!empty($ggcache_options['type']) && !empty($ggcache_options['timeout']) && !is_admin() && 'GET' === $_SERVER['REQUEST_METHOD']) {
    ob_start();
    $ggcache_cache_key = md5(wp_unslash(trim($_SERVER['REQUEST_URI'], '/')) . (!empty($ggcache_options['salt']) ? $ggcache_options['salt'] : ''));
    // 显示缓存
    Ggcache_Plugin::show_cache();
    // 保存缓存
    add_action('shutdown', array('Ggcache_Plugin', 'save_cache'), PHP_INT_MIN);
}