<?php

/**
 * 基础设置
 */
class Ggcache_Page
{
    // 初始化页面
    public static function init_page()
    {
        // 注册一个新页面
        register_setting('ggcache_page', 'ggcache_options', array('Ggcache_Page', 'save_config'));

        add_settings_section(
            'ggcache_page_section',
            null,
            array('Ggcache_Page', 'ggcache_text'),
            'ggcache_page'
        );

        add_settings_field(
            'type',
            '缓存',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_page_section',
            array(
                'label_for' => 'type',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '关闭',
                        'value' => '0'
                    ),
                    array(
                        'title' => '文件缓存',
                        'value' => '1'
                    ),
                    array(
                        'title' => 'Redis缓存',
                        'value' => '2'
                    ),
                    array(
                        'title' => 'Memcached缓存',
                        'value' => '3'
                    )
                )
            )
        );

        add_settings_field(
            'timeout',
            '缓存有效期',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_page_section',
            array(
                'label_for' => 'timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '单位：秒。页面缓存的时间，例如：3600'
            )
        );

        add_settings_section(
            'ggcache_redis_page_section',
            'Redis缓存设置',
            array('Ggcache_Page', 'redis_text'),
            'ggcache_page'
        );

        add_settings_field(
            'redis_host',
            'Redis主机地址',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_redis_page_section',
            array(
                'label_for' => 'redis_host',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '例如：127.0.0.1'
            )
        );

        add_settings_field(
            'redis_port',
            'Redis端口',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_redis_page_section',
            array(
                'label_for' => 'redis_port',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '例如：6379'
            )
        );

        add_settings_field(
            'redis_password',
            'Redis密码',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_redis_page_section',
            array(
                'label_for' => 'redis_password',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '如果设置了Redis密码，则需要填写'
            )
        );

        add_settings_field(
            'redis_db',
            'Redis数据库',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_redis_page_section',
            array(
                'label_for' => 'redis_db',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '一般为0-15之间的一个数字'
            )
        );

        add_settings_field(
            'redis_timeout',
            'Redis连接超时时间',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_redis_page_section',
            array(
                'label_for' => 'redis_timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '单位：秒。连接Redis的最长等待时间，超过这个时间，则取消连接。例如：3'
            )
        );

        add_settings_section(
            'ggcache_memcache_page_section',
            'Memcached缓存设置',
            array('Ggcache_Page', 'memcache_text'),
            'ggcache_page'
        );

        add_settings_field(
            'memcache_host',
            'Memcached主机地址',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_memcache_page_section',
            array(
                'label_for' => 'memcache_host',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => 'Memcached服务端监听主机地址。例如：127.0.0.1'
            )
        );

        add_settings_field(
            'memcache_port',
            'Memcached端口',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_memcache_page_section',
            array(
                'label_for' => 'memcache_port',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => 'Memcached服务端监听端口。当Memcached主机地址使用Unix域socket的时候要设置此参数为0。 例如：11211'
            )
        );

        add_settings_field(
            'memcache_timeout',
            'Memcached连接超时时间',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_memcache_page_section',
            array(
                'label_for' => 'memcache_timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '单位：秒。连接Memcached的最长等待时间，超过这个时间，则取消连接。例如：3'
            )
        );
    }

    /**
     * 清除缓存
     * @return void
     */
    public static function ggcache_text()
    {
        if (!defined('WP_CACHE') || !WP_CACHE) {
            ?>
            <p>请修改网站根目录下的<b>wp-config.php</b>文件，添加<code>define('WP_CACHE', true);</code>配置代码。</p>
            <?php
        } else {
            global $ggcache_options;
            if (!empty($ggcache_options['type'])) {
                if (!defined('GGCACHE_ADVANCED_CACHE')) {
                    // 不存在本插件生成的advanced-cache.php文件
                    ?>
                    <p>请卸载其它缓存插件或者手动删除网站根目录下的此文件：<b>wp-content/advanced-cache.php</b>，然后点击保存更改按钮。</p>
                    <?php
                } else {
                    ?>
                    <p>
                        <a href="javascript:void(0);" id="clear_cache" class="button button-small button-secondary">清除所有缓存</a>
                    </p>
                    <?php
                }
            }
        }
    }

    /**
     * Redis说明
     * @return void
     */
    public static function redis_text()
    {
        global $ggcache_options;
        if (empty($ggcache_options['type']) || 2 != $ggcache_options['type']) {
            ?>
            未使用Redis缓存
            <?php
        } else {
            if (!class_exists('Redis')) {
                ?>
                未安装Redis扩展，无法使用Redis缓存
                <?php
            } else {
                if (empty($ggcache_options['redis_host'])) {
                    ?>
                    未设置Redis，无法使用Redis缓存
                    <?php
                } else {
                    $redis = Ggcache_Redis::get_instance($ggcache_options['redis_host'], $ggcache_options['redis_port'], $ggcache_options['redis_password'], $ggcache_options['redis_db'], $ggcache_options['redis_timeout'])->connect();
                    if (empty($redis)) {
                        ?>
                        Redis连接失败，无法使用Redis缓存
                        <?php
                    } else {
                        ?>
                        Redis连接成功，可以使用Redis缓存
                        <?php
                    }
                }
            }
        }
    }

    /**
     * Memcached说明
     * @return void
     */
    public static function memcache_text()
    {
        global $ggcache_options;
        if (empty($ggcache_options['type']) || 3 != $ggcache_options['type']) {
            ?>
            未使用Memcached缓存
            <?php
        } else {
            if (!class_exists('Memcache') && !class_exists('Memcached')) {
                ?>
                未安装Memcache或Memcached扩展，无法使用Memcached缓存
                <?php
            } else {
                if (empty($ggcache_options['memcache_host'])) {
                    ?>
                    未设置Memcached，无法使用Memcached缓存
                    <?php
                } else {
                    $result = Ggcache_Memcache::get_instance($ggcache_options['memcache_host'], $ggcache_options['memcache_port'], $ggcache_options['memcache_timeout'])->set('ggcache', '1', 3);
                    if (empty($result)) {
                        ?>
                        Memcached连接失败，无法使用Memcached缓存
                        <?php
                    } else {
                        ?>
                        Memcached连接成功，可以使用Memcached缓存
                        <?php
                    }
                }
            }
        }
    }

    /**
     * 保存文件配置
     * @param array $option
     * @return array
     */
    public static function save_config($option)
    {
        Ggcache_Advanced::save_config($option);
        if (!empty($option['type'])) {
            // 开启了缓存
            Ggcache_Advanced::copy_advanced_cache_file();
        } else {
            // 关闭了缓存
            Ggcache_Advanced::remove_advanced_cache_file();
        }
        return $option;
    }
}