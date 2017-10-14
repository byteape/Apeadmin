<?php
/*RUNTIME_PATH为TP的一个缓存路径常量*/
import("Org.Util.FileDir");
$file= new \FileDir();
$fileList=$file->getFiles(RUNTIME_PATH);