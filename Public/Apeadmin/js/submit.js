/**
 * ajax-get
 */
$('.ajax-get').click(function () {
    var target;
    var that = this;
    var href = $(this).attr('href');
    var url = $(this).attr('url');
    var confirmmsg = $(this).attr('confirmmsg') ? $(this).attr('confirmmsg') : '您确定要删除吗？';//默认为询问删除，可自定义文本
    if ((target = href) || (target = url)) {
        bootbox.confirm({
            message: confirmmsg,
            buttons: {
                confirm: {
                    label: '是',
                    className: 'btn-success'
                },
                cancel: {
                    label: '否',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    $.get(target).success(function (data) {
                        if (data.status) {
                            if (data.url) {
                                updateAlert(data.info + ' 页面即将自动跳转~', data.status);
                            } else {
                                updateAlert(data.info, data.status);
                            }
                            setTimeout(function () {
                                if (data.url) {
                                    window.location.href = data.url;
                                } else {
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            updateAlert(data.info, data.status);
                            if (data.url) {
                                window.location.href = data.url;
                            }
                        }
                    });
                }
            }
        });

    }
    return false;
});

/**
 * ajax-post
 */
$('.ajax-post').click(function () {
    var target, query, form;
    var target_form = $(this).attr('target-form');
    var that = this;
    if (($(this).attr('type') == 'submit')) {
        form = $('.' + target_form);
        query = form.serialize();
        $(that).attr('autocomplete', 'off').prop('disabled', true);
        $.post(target, query).success(function (data) {
            if (data.status) {
                if (data.url) {
                    updateAlert(data.info + ' 页面即将自动跳转~', data.status);
                } else {
                    updateAlert(data.info, data.status);
                }
                setTimeout(function () {
                    if (data.url) {
                        window.location.href = data.url;
                    } else {
                        window.location.reload();
                    }
                }, 1500);
            } else {
                updateAlert(data.info, data.status);
                $(that).attr('autocomplete', '').prop('disabled', false);
            }
        });
    }
    return false;
});

$(function () {
    window.updateAlert = function (info, status) {
        if (status) {
            new $.zui.Messager(info, {
                icon: 'check',
                type: 'success',
                time: 2000,
                close: false
            }).show();
        } else {
            new $.zui.Messager(info, {
                icon: 'warning-sign',
                type: 'warning',
                time: 2000,
                close: false
            }).show();
        }
    };
});


