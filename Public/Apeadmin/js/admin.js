$(function () {
    //左右伸缩
    $('.sidebar-toggle').click(function () {
        if ($('.sidebar-menu').is(":visible") === true) {
            $('.main-content').css({
                'margin-left': '0px'
            });
            $('.sidebar-menu').css({
                'margin-left': '-180px'
            });
            $('.sidebar-menu').hide();
            $(".main").addClass("sidebar-closed");
        } else {
            $('.main-content').css({
                'margin-left': '180px'
            });
            $('.sidebar-menu').show();
            $('.sidebar-menu').css({
                'margin-left': '0'
            });
            $(".main").removeClass("sidebar-closed");
        }
        $('.chosen-container').css('width', '100%');
    });
    //列表页checkbox全选与反选
    $('.selectAllCheckbox').click(function () {
        if (this.checked) {
            $('table :checkbox').not(':disabled').prop("checked", true);
        } else {
            $('table :checkbox').not(':disabled').prop("checked", false);
        }
    })
})

/**
 * 格式化文件大小
 * @param value
 * @returns {*}
 */
function renderSize(value) {
    if (null == value || value == '') {
        return "0 Bytes";
    }
    var unitArr = new Array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
    var index = 0;
    var srcsize = parseFloat(value);
    index = Math.floor(Math.log(srcsize) / Math.log(1024));
    var size = srcsize / Math.pow(1024, index);
    size = size.toFixed(2);//保留的小数位数
    return size + unitArr[index];
}

/**
 * 删除询问
 * @param prefun
 * @param callback
 */
function confirmDel(prefun, callback) {
    var msg = '<i class="icon-question-sign" style="color:#EA644A"></i>确认要删除';
    confirmMsg(prefun, msg, callback)
}

/**
 * 弹出选择对话框
 * @param prefun
 * @param msg
 * @param callback
 */
function confirmMsg(prefun, msg, callback) {
    $preback = prefun();
    if ($preback) {
        bootbox.confirm({
            message: msg,
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
            callback: callback
        });
    }
}

/**
 * 列表页全选与反选checkbox
 * @param table
 * @constructor
 */
function CheckAll(table) {
    for (var i = 0; i < table.elements.length; i++) {
        var e = table.elements[i];
        if (e.Name != "chkAll") {
            e.checked = table.chkAll.checked;
        }
    }
}

/**
 * 未选择处理前函数
 * @returns {*}
 */
function prefun() {
    var selidArr = new Array();
    $("input[name='selid[]']:checkbox").each(function () {
        if (true == $(this).is(':checked')) {
            selidArr.push($(this).val());
        }
    });
    if (!selidArr.length) {
        new $.zui.Messager('您没有任何选择内容，请重新选择！', {
            icon: 'times',
            type: 'warning',
            time: 1000
        }).show();
        return false;
    } else {
        return selidArr;
    }
}

/**
 * 删除处理函数
 * @param result
 */
function delsel(result) {
    var selidArr = new Array();
    $("input[name='selid[]']:checkbox").each(function () {
        if (true == $(this).is(':checked')) {
            selidArr.push($(this).attr('value'));
        }
    })
    if (result) {
        $.ajax({
            type: "post",
            url: listPldelAction,//默认提交地址为当前页，即在列表页中使用的当前面页
            dataType: 'json',
            data: {selidArr: selidArr,ajaxedit:2},
            success: function (data) {
                if(data.status){
                    new $.zui.Messager(data['info'], {
                        icon: 'check',
                        type: 'success',
                        time:1000
                    }).show();
                    window.location.reload();
                }else{
                    new $.zui.Messager(data['info'], {
                        icon: 'times',
                        type: 'warning',
                        time: 1000
                    }).show();
                }
            },
            error: function () {
                bootbox.alert({message: '<i class="icon-warning-sign" style="color:#F1A325"></i>请求发起错误', backdrop: true, size: 'small'});
            }
        });
    }
}