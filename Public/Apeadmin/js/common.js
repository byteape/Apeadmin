$(document).ready(function() {

    //修改单选值
    $(".edit_toggle").each(function() {
        var t = $(this).attr('attr_t');
        var v = $(this).html();
        var id = $(this).attr('attr_id');
        var html = '<img style="cursor:pointer" alt="点击修改状态" onclick="edit_toggle(this,\'' + t + '\',\'' + id + '\')" src="' + pub + '/Fwadmin/images/no1.gif" />';
        if (v == '1') {
            html = '<img style="cursor:pointer" alt="点击修改状态" onclick="edit_toggle(this,\'' + t + '\',\'' + id + '\')" src="' + pub + '/Fwadmin/images/yes1.gif" />';
        }
        $(this).html(html);
    });

    //修改内容
    $(".edit_input").each(function() {
        var t = $(this).attr('attr_t');
        var v = $(this).html();
        var id = $(this).attr('attr_id');
        var width = $(this).attr('width');
        if (width == '' || width == undefined)
            width = '30px';
        var html = '<div class="edit_area" ><span id="span_' + t + '_' + id + '">' + v + '</span><p style="display:none" id="p_' + t + '_' + id + '"><input type="text" value="' + v + '" style="width:' + width + '" id="' + t + '_' + id + '" attr_id="' + id + '" attr_type="' + t + '"   /></p></div>';
        $(this).html(html);
    });

    $(".edit_area").click(function() {
        $(this).find('p').show();
        $(this).find('input').show();
        $(this).find('span').hide();
        $(this).find('input').focus();
    });
    $(".edit_area input").blur(function() {
        edit_input(this);
    });
    $(".edit_area").attr('title', '点击可编辑内容,按ESC键取消，按ENTER键确定');
    $(".edit_area input").keyup(function(e) {
        var key = e.which;
        if (key == 27) {
            $(this).parent().parent().find('p').hide();
            $(this).parent().parent().find('span').show();
        }
        else if (key == 13) {
            edit_input(this);
        }
    });

});


//直接修改内容
var doc_url = location.href.lastIndexOf("?") == -1 ? location.href.substring((location.href.lastIndexOf("/")) + 1) + "?" : location.href;
//修改文本
function edit_input(obj) {
    var id = $(obj).attr("attr_id");
    var t = $(obj).attr("attr_type");
    var v = $("#" + t + "_" + id).val();
    var oldvalue = $("#span_" + t + "_" + id).html();
    $("#span_" + t + "_" + id).show();
    $("#p_" + t + "_" + id).hide();
    //$("#" + t + "_" + id).hide();
    if (oldvalue != v) {
        $("#span_" + t + "_" + id).html('<img src="' + root + '/Public/Fwadmin/images/loading.gif" />');
        var url = doc_url + "&t=" + t + "&v=" + encodeURI(v) + "&i=" + id + "&n=" + Math.random() + "&ajaxedit=1";
        $.ajax({
            url: url,
            cache: false,
            success: function(val) {
                val = $.trim(val);
                if (val == '1') {
                    $("#span_" + t + "_" + id).html(v);
                }
                else {
                    $("#span_" + t + "_" + id).html(oldvalue);
                }
            }
        });
    }
}

//修改单选值
function edit_toggle(obj, t, id) {
    var v = ($(obj).attr("src").match(/yes1.gif/i)) ? 0 : 1;
    var url = doc_url + "&t=" + t + "&v=" + v + "&i=" + id  + "&ajaxedit=1";
    $(obj).attr("src", root + '/Public/Fwadmin/images/loading.gif');
    $.ajax({
        url: url,
        cache: false,
        success: function(val) {
            val = $.trim(val);
            if (val == '1') {
                if (v == '1') {
                    $(obj).attr("src", root + '/Public/Fwadmin/images/yes1.gif');
                }
                else {
                    $(obj).attr("src", root + '/Public/Fwadmin/images/no1.gif');
                }
            }
            else {
                if (v == '0') {
                    $(obj).attr("src", root + '/Public/Fwadmin/images/yes1.gif');
                }
                else {
                    $(obj).attr("src", root + '/Public/Fwadmin/images/no1.gif');
                }
            }
        }
    });
}

//是否为数字
function isNumber(val) {
    var reg = /^[\d|\.|,]+$/;
    return reg.test(val);
}