<?php 
js重新刷新当前页：window.location.reload();
js跳转网页：window.location.href="{:U('/')}";
js返回上一页：history.go(-1);
js将字符串转换为数字：parseInt();


$.ajax({
	type: "post",
	url : "{:U('/')}",
	dataType:'json',
	data:{type:type,id:id},
	success: function(data){
	}
});

/*手机端详情内容图片宽高自适应*/
//给内容显示div一个id为editor
<script>
   var resizeContentID = "editor";
   var maxWidth = $("#editor").width();
   var images = document.getElementById(resizeContentID).getElementsByTagName("img");
   for (var i = 0; i < images.length; i++) {
	   resizepic(images[i]);
   }
   function resizepic(thispic)
   {
	   thispic.onload = function() {
		   if (thispic.width > maxWidth) {
			   thispic.style.height = thispic.height * maxWidth / thispic.width + "px";
			   thispic.style.width = maxWidth + "px";
		   }
	   }
   }
</script>