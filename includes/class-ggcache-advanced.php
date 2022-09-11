<?php

/**
 * 文件配置
 */
class Ggcache_Advanced
{
    public static $config_file = '/ggcache/config/config.php';

    /**
     * 获取配置值
     * @param string $config 配置项
     * @param mixed $def_value 默认值
     * @return mixed
     */
    public static function get_config($config = '', $def_value = array())
    {
        global $ggcache_plugin_path;
        if (empty($ggcache_plugin_path)) {
            $ggcache_plugin_path = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
        }
        if (!file_exists($ggcache_plugin_path . self::$config_file)) {
            return $def_value;
        }
        $config_data = @include $ggcache_plugin_path . self::$config_file;
        if (!is_array($config_data)) {
            return $def_value;
        }
        if (!empty($config) && isset($config_data[$config])) {
            return $config_data[$config];
        }
        return $config_data;
    }

    /**
     * 保存配置文件
     * @param array $config_data
     * @return false|int
     */
    public static function save_config($config_data)
    {
        if (!is_array($config_data)) {
            return false;
        }
        global $ggcache_plugin_path;
        if (empty($ggcache_plugin_path)) {
            $ggcache_plugin_path = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
        }
        return file_put_contents($ggcache_plugin_path . self::$config_file, '<?php' . PHP_EOL . 'defined( \'ABSPATH\' ) || exit;' . PHP_EOL . 'return ' . var_export($config_data, true) . ';');
    }

    public static function show_cache($cache_key)
    {
        global $ggcache_options;
        global $ggcache_plugin_path;
        $data = array();
        switch ($ggcache_options['type']) {
            case 1:
                // 从文件获取
                require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-file.php';
                $data = Ggcache_File::get_instance()->get($cache_key, array());
                break;
            case 2:
                // 从Redis获取
                require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-redis.php';
                $data = Ggcache_Redis::get_instance($ggcache_options['redis_host'], $ggcache_options['redis_port'], $ggcache_options['redis_password'], $ggcache_options['redis_db'], $ggcache_options['redis_timeout'])->get($cache_key, array());
                break;
            case 3:
                // 从Memcached获取
                require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-memcache.php';
                $data = Ggcache_Memcache::get_instance($ggcache_options['memcache_host'], $ggcache_options['memcache_port'], $ggcache_options['memcache_timeout'])->get($cache_key, array());
                break;
        }
        // 输出缓存内容
        if (!empty($data) && !empty($data['html'])) {
            if (!empty($data['headers'])) {
                foreach ($data['headers'] as $header) {
                    @header($header);
                }
            }
            die($data['html']);
        }
    }

    /**
     * 存储缓存
     * @param string $buffer 输出缓冲区中的内容
     * @param int $phase 比特掩码 PHP_OUTPUT_HANDLER_* 常量
     * @return string
     */
    public static function save_cache($buffer, $phase)
    {
        if (!empty($buffer)) {
            global $ggcache_cache_key;
            global $ggcache_options;
            global $ggcache_plugin_path;
            $data = array(
                'html' => $buffer,
                'headers' => headers_list(),
            );
            switch ($ggcache_options['type']) {
                case 1:
                    // 文件
                    require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-file.php';
                    Ggcache_File::get_instance()->set($ggcache_cache_key, $data);
                    break;
                case 2:
                    // Redis
                    require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-redis.php';
                    Ggcache_Redis::get_instance($ggcache_options['redis_host'], $ggcache_options['redis_port'], $ggcache_options['redis_password'], $ggcache_options['redis_db'], $ggcache_options['redis_timeout'])->set($ggcache_cache_key, $data);
                    break;
                case 3:
                    // Memcached
                    require_once $ggcache_plugin_path . '/ggcache/includes/class-ggcache-memcache.php';
                    Ggcache_Memcache::get_instance($ggcache_options['memcache_host'], $ggcache_options['memcache_port'], $ggcache_options['memcache_timeout'])->set($ggcache_cache_key, $data);
                    break;
            }
        }
        return $buffer;
    }

    /**
     * 清除所有缓存
     * @return bool
     */
    public static function clear_cache()
    {
        global $ggcache_options;
        $result = false;
        switch ($ggcache_options['type']) {
            case 1:
                // 文件
                $result = Ggcache_File::get_instance()->flush();
                break;
            case 2:
                // Redis
                $result = Ggcache_Redis::get_instance($ggcache_options['redis_host'], $ggcache_options['redis_port'], $ggcache_options['redis_password'], $ggcache_options['redis_db'], $ggcache_options['redis_timeout'])->flush();
                break;
            case 3:
                // Memcached
                $result = Ggcache_Memcache::get_instance($ggcache_options['memcache_host'], $ggcache_options['memcache_port'], $ggcache_options['memcache_timeout'])->flush();
                break;
        }
        return $result;
    }

    /**
     * 复制advanced-cache.php到指定目录
     * @return bool
     */
    public static function copy_advanced_cache_file()
    {
        $advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
        if (file_exists($advanced_cache_file)) {
            return false;
        }
        global $ggcache_plugin_path;
        if (empty($ggcache_plugin_path)) {
            $ggcache_plugin_path = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
        }
        if (!file_exists($ggcache_plugin_path . '/ggcache/template/advanced-cache.php')) {
            return false;
        }
        if (!copy($ggcache_plugin_path . '/ggcache/template/advanced-cache.php', $advanced_cache_file)) {
            return false;
        }
        return true;
    }

    /**
     * 删除advanced-cache.php
     * @return bool
     */
    public static function remove_advanced_cache_file()
    {
        if (!defined('GGCACHE_ADVANCED_CACHE')) {
            return false;
        }
        $advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
        if (!file_exists($advanced_cache_file)) {
            return false;
        }
        return @unlink($advanced_cache_file);
    }
}