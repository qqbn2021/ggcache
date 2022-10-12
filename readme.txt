=== 果果加速 ===
Contributors: wyzda2021
Donate link: https://www.ggdoc.cn
Tags:网站加速, 缓存, redis缓存, Memcached缓存, 页面加速, 静态化, 果果加速
Requires at least: 5.0
Requires PHP:5.3
Tested up to: 6.0
Stable tag: 0.0.2
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

支持文件、Redis、Memcached缓存加速，让页面浏览更快！

== Description ==

支持将网站前台页面内容缓存到文件、Redis、Memcached里，以此来加速网站页面的访问速度。

缓存原理：如果页面没有缓存，则会在访问之后生成缓存内容，当页面再次访问时，直接读取缓存内容，显示在浏览器里，以此来加速网站页面的访问速度。

本插件适用于非交互式网站、纯静态展示网站、无需实时更新页面内容的网站、企业网站等。

本插件仅缓存网站前台页面（只会缓存GET请求的且状态码为200的前台页面），不会缓存后台页面、用户登录后的页面、404以及服务器错误页面。

1、支持设置缓存方式，是使用文件缓存还是Redis缓存或者Memcached缓存。

2、可以设置页面内容的缓存有效期。

3、友好的错误提示文字，让您轻松设置Redis缓存或者Memcached缓存。

4、支持配置指定GET\COOKIE参数缓存，排除页面链接（不缓存），页面格式化（源码格式化到一行，压缩源码）。（商业版）

== Installation ==

1. 进入WordPress网站后台，找到“插件-安装插件”菜单；
2. 点击界面左上方的“上传插件”按钮，选择本地提前下载好的插件压缩包文件（zip格式），点击“立即安装”；
3. 安装完成后，启用 “果果加速” 插件；
4. 通过“设置”链接进入插件设置界面；
5. 完成设置后，插件就安装完毕了。

== Frequently Asked Questions ==

= 使用果果加速后有什么效果？ =
提升网站的访问速度，减少数据库的读写操作。在高并发的情况下，可以有效的提升网站加载速度，不让任何一个用户无法访问。

= 如何使用Redis缓存？ =
需要安装PHP Redis扩展，以及Redis软件。宝塔面板用户可以直接从软件商店安装。

= 如何使用Memcached缓存？ =
需要安装PHP Memcached扩展，以及Memcached软件。宝塔面板用户可以直接从软件商店安装。

= 联系作者 =
如果插件使用出现了问题，或者想要定制功能，可以加QQ：1445023846。


== Screenshots ==

1. 果果加速设置
2. Redis缓存设置
3. Memcached缓存设置
4. 未开启加速前的页面加载耗时
5. 开启加速后的页面加载耗时

== Upgrade Notice ==

= 0.0.2 =
* 解决了一个bug

= 0.0.1 =
参考Changelog说明

== Changelog ==

= 0.0.2 =
* 解决了一个bug

= 0.0.1 =
* 新增文件、Redis、Memcached页面缓存方式