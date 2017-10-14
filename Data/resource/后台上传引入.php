/*上传单张图片*/
<td>
<include file="Public/uploadfile" filetype="image" id="picture" value="{$model['picture']}" editvalue="{:getEditFiles($model['picture'])}" width="100" path="news" limit="1" thumbw="100" thumbh="100" msg=""/>
<div class="ts_msg">100*100</div>
</td>

/*上传多张图片*/
<td>
<include file="Public/uploadfile" filetype="image" id="picture" value="{$model['picture']}" editvalue="{:getEditFiles($model['picture'])}" width="100" path="news" limit="10" thumbw="1920" thumbh="320" msg=""/>
<div class="ts_msg">1920*320,从上至下依次为：产品服务、解决方案、新闻动态、关于我们</div>
</td>

/*上传下载*/
<td>
<include file="Public/uploadfile" filetype="file" id="picture" value="{$model['picture']}" editvalue="{:getEditFiles($model['picture'])}" width="100" path="news" limit="10" thumbw="1920" thumbh="320" msg=""/>
<div class="ts_msg">请上传压缩文件zip/rar，大文件可以用FTP上传后填写地址</div>
</td>


/*说明*/
上传图片、文件基本引入文件是一样的。
重新参数：
filetyp:上传类型(image|file|all)，分别为图片、文件、不限。具体后缀可在控制器中修改配置。
id:同name，唯一。
value:新增时留空即可"",修改时传入值
editvalue:新增时留空"{}",修改时如上传入处理函数带值
path:保存文件路径
limit:最多可上传多少文件
thumw:图片缩略宽，filetype为image时有效。
thumh:图片缩略高，filetype为image时有效。
msg:可向按钮传入显示文件，一般留空。
