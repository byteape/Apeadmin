当手机的链接和pc的链接都是一样的时候，只是手机地址前有Mobile时
<script>
    if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i))) {
        var url=window.location.href;
        var temp = url.match(/[^\/]+(\/)+[^\/]+\//g);
        var point=temp? Math.max(0, temp[0].length - 1) : -1;
        var host=url.substring(0,point);
        var others=url.substring(point);
        var newurl=host+'/Mobile'+others;//如果是本地测试环境可修改为host+'/项目目录名/Mobile'+others.replace('项目目录名','');
        location.replace(newurl);
    }
</script>

如果有很多链接不一样，还是老老实实地把手机网站的地址填上
<script>
    if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i))) {
        var url='http://m.domain.com';
        location.replace(newurl);
    }
</script>

如果只是切换默认的模板，而不用带跳转链接的可以在config中默认模板进行动态修改

