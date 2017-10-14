<?php 
------------------------------------------------------------------------------
/*美橙虚拟主机配置注意*/
在web.config的rewrite模块下加入
<handlers>
   <add name="php53" path="*.php" verb="GET,HEAD,POST,DEBUG" modules="FastCgiModule" scriptProcessor="c:\php53\php-cgi.exe" />
</handlers>
同时提交工单由工作人员处理：需要添加列出目录权限，Windows主机用户无法修改权限，需要工作人员修改。
--------------------------------------------------------------------------------