<?php 
$filename=__ROOT__.'/Public/Upload/download/20161129/583d268c82c37.pdf';//可以是本地路径，也可以是远程路径。
$filename='https://www.baidu.com/img/bd_logo1.png';

import("Org.Util.File");
$file= new\File($filename);
dump($file->getRealFile());
dump($file->getExtName());
dump($file->getSize());