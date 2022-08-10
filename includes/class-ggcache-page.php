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
        register_setting('ggcache_page', 'ggcache_options');

        add_settings_section(
            'ggcache_page_section',
            '清除缓存',
            array('Ggcache_Page', 'clear_cache_text'),
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
                        'title' => 'Memcache缓存',
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
            'Memcache缓存设置',
            array('Ggcache_Page', 'memcache_text'),
            'ggcache_page'
        );

        add_settings_field(
            'memcache_host',
            'Memcache主机地址',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_memcache_page_section',
            array(
                'label_for' => 'memcache_host',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => 'Memcache服务端监听主机地址。例如：127.0.0.1'
            )
        );

        add_settings_field(
            'memcache_port',
            'Memcache端口',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_memcache_page_section',
            array(
                'label_for' => 'memcache_port',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => 'Memcache服务端监听端口。当Memcache主机地址使用Unix域socket的时候要设置此参数为0。 例如：11211'
            )
        );

        add_settings_field(
            'memcache_timeout',
            'Memcache连接超时时间',
            array('Ggcache_Plugin', 'field_callback'),
            'ggcache_page',
            'ggcache_memcache_page_section',
            array(
                'label_for' => 'memcache_timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '单位：秒。连接Memcache的最长等待时间，超过这个时间，则取消连接。例如：3'
            )
        );
    }

    /**
     * 清除缓存
     * @return void
     */
    public static function clear_cache_text()
    {
        ?>
        如果想要清除缓存，请点击此链接：<a href="options-general.php?page=ggcache-setting&clear_cache=1" title="一键清除所有缓存">一键清除所有缓存</a>
        <?php
    }

    /**
     * Redis说明
     * @return void
     */
    public static function redis_text()
    {
        global $ggcache_options;
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
                $redis = Ggcache_Plugin::get_redis();
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

    /**
     * Memcache说明
     * @return void
     */
    public static function memcache_text()
    {
        global $ggcache_options;
        if (!function_exists('memcache_connect')) {
            ?>
            未安装Memcache扩展，无法使用Memcache缓存
            <?php
        } else {
            if (empty($ggcache_options['memcache_host'])) {
                ?>
                未设置Memcache，无法使用Memcache缓存
                <?php
            } else {
                $memcache = Ggcache_Plugin::get_memcache();
                if (empty($memcache)) {
                    ?>
                    Memcache连接失败，无法使用Memcache缓存
                    <?php
                } else {
                    ?>
                    Memcache连接成功，可以使用Memcache缓存
                    <?php
                }
            }
        }
    }
}