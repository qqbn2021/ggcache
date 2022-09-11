/**
 * 点击清除缓存按钮
 */
jQuery(document).ready(function ($) {
    // 清除缓存
    $("#clear_cache").click(function () {
        Swal.fire({
            title: '确定要清除所有缓存吗?',
            showCancelButton: true,
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            text: '清除缓存后，页面会重新生成缓存',
            icon: 'warning'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(ggcache_obj.ajax_url, {
                    action: 'clear_cache',
                    _ajax_nonce: ggcache_obj.nonce,
                }, function (data) {
                    if (1 == data.status){
                        Swal.fire({
                            title: '温馨提示',
                            text: "清除缓存成功",
                            icon: 'success',
                            showConfirmButton: true,
                            confirmButtonText: '确定'
                        });
                    } else {
                        Swal.fire({
                            title: '温馨提示',
                            text: "清除缓存失败",
                            icon: 'warning',
                            showConfirmButton: true,
                            confirmButtonText: '确定'
                        });
                    }
                });
            }
        });
    });
});